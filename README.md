<h1 align="center"> laravel-relation-save </h1>

<p align="center"> 📦laravel 关联保存 hasOne hasMany belongsto.</p>


## Installing

```shell
$ composer require liaosp/laravel-relation-save -vvv
```

## ✈Usage

在模型中添加

```
use SaveRelation;
```

🔨使用

```
$this->model->save($validate->getData());

$this->model->saveRelation($validate->getData());
```

## 🌰 example 

比如有主表shop 和 store_detail 

定义HasOne 关系

```
   public function store_detail()
    {
        return $this->hasOne(\App\Models\StoreDetail::class, 'store_id', 'id');
    }
```



比如前端json 请求

```
  {
    "name": "老廖的店铺",
    "store_detail": {
      "address": "福建省厦门市思明区吕厝地铁口"
    }
  }
```
创建数据简化前 （大概是这种意思）
```
 //一些店铺操作
 $this->store->save();
 $this->store_detail->store_id = $this->store->id;
 $this->store_detail->address = $request->get('store_detail')['address'];
 $this->store_detail->save();
```

我看了下Laravel-admin 的关联保存，对于快速开发的项目，觉得挺好的，值得学习。`encore\laravel-admin\src\Form.php`

只需如下可以保存关联关系的数据了

```
$this->model->save($request->all());

$this->model->saveRelation($request->all());
```

适用 创建和更新 ，可参考源码，学习更多小技巧。

[laravel-relation-save](https://github.com/liaoshengping/laravel-relation-save)

## ✏Reference

[laravel-admin](https://laravel-admin.org/)

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/liaosp/laravel-relation-save/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/liaosp/laravel-relation-save/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT