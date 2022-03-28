<?php


namespace Liaosp\LaravelRelationSave;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Trait SaveRelation
 * @package Liaosp\LaravelRelationSave
 * @method saveRelation($relationsData)
 */
trait SaveRelation
{
    /**
     * Update relation data.
     *
     * @param array $relationsData
     *
     * @return void
     */
    protected function scopeSaveRelation($query,$relationsData)
    {
        foreach ($relationsData as $name => $values) {
            if (!method_exists($this, $name)) {
                continue;
            }

            $relation = $this->$name();

            if (empty($relationsData)) {
                continue;
            }

            switch (true) {
                case $relation instanceof\Illuminate\Database\Eloquent\Relations\BelongsToMany:
                case $relation instanceof\Illuminate\Database\Eloquent\Relations\MorphToMany:
                    if (isset($relationsData[$name])) {
                        $relation->sync($relationsData[$name]);
                    }
                    break;
                case $relation instanceof\Illuminate\Database\Eloquent\Relations\HasOne:
                case $relation instanceof\Illuminate\Database\Eloquent\Relations\MorphOne:
                    $related = $this->getRelationValue($name) ?: $relation->getRelated();

//                    foreach ($relationsData[$name] as $column => $value) {
//                        $related->setAttribute($column, $value);
//                    }
                    $related->fill($relationsData[$name]);

                    // save child
                    $relation->save($related);
                    break;
                case $relation instanceof\Illuminate\Database\Eloquent\Relations\BelongsTo:
                case $relation instanceof\Illuminate\Database\Eloquent\Relations\MorphTo:
                    $related = $this->getRelationValue($name) ?: $relation->getRelated();

                    foreach ($relationsData[$name] as $column => $value) {
                        $related->setAttribute($column, $value);
                    }

                    // save parent
                    $related->save();

                    // save child (self)
                    $relation->associate($related)->save();
                    break;
                case $relation instanceof\Illuminate\Database\Eloquent\Relations\HasMany:
                case $relation instanceof\Illuminate\Database\Eloquent\Relations\MorphMany:
                    foreach ($relationsData[$name] as $related) {
                        /** @var\Illuminate\Database\Eloquent\Relations\HasOneOrMany $relation */
                        $relation = $this->$name();

                        $keyName = $relation->getRelated()->getKeyName();

                        /** @var Model $child */
                        $child = $relation->findOrNew(Arr::get($related, $keyName));

                        if (Arr::get($related, '__remove__') == 1) {
                            $child->delete();
                            continue;
                        }

                        Arr::forget($related, '__remove__');
                        $arrarr = [];
                        foreach ($related as $colum => $value) {
                            if (is_array($value)){
                                if (method_exists($child, $colum)) {
                                    $arrarr[][$colum] = $value;
                                    continue;
                                }
                                continue;
                            }
//                            $child->setAttribute($colum, $value);
                        }
                        $child->fill($related);


                        $child->save();

                        if ($arrarr){
                            foreach ($arrarr as $childchild){
                                $child->saveRelation($childchild);
                            }
                        }

                    }
                    break;
            }
        }
    }
}