> 基于fileinput的yii2图片上传封装

# 上传图片 
1. 在配置文件中,添加别名@ttiantianle
```
'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@ttiantianle' =>'@vendor/ttiantianle'
    ],
```

2.在需要上传图片文件的试图中注册静态资源
```
use ttiantianle\upload\UploadAsset;
UploadAsset::register($this);

```
3. 在需要上传图片（文件）的地方，调用方法，例如
```
单图：
<?= $form->field($model, 'avatar')->hiddenInput(['maxlength' => true,'id'=>'avatar']) ?>
<?= \ttiantianle\upload\UploadWidgets::uploadFile('avatar',$model->avatar,1)?>
多图：
<?= $form->field($model, 'image')->hiddenInput(['id' => "image"]) ?>
<?= \ttiantianle\upload\UploadWidgets::uploadFile('image',json_decode($model->image),5) ?>

```
4. 修改上传图片的路径
ttiantianle/upload/src/UploadWidgets.php中的uploadFile方法
```
  $('#$uploadInputId').fileinput({
        language: 'zh',
        uploadUrl: '?r=file/upload-file&type=$type&path=$path&size=$size',//换成自己的上传图片接口，可参照ttiantianle/upload/src/FileController.php
        deleteUrl: '?r=file/del-file',
        overwriteInitial: false,
        allowedFileTypes: [$type],
        initialPreviewAsData: true,
        initialPreviewShowDelete:true,
        maxFileCount:$maxFileCount,
        initialPreview:$initialPreview,
        initialPreviewConfig:$initialPreviewConfig,
    })});
```

### 参考
- http://www.jq22.com/jquery-info5231
- https://www.yiichina.com/doc/guide/2.0/structure-assets
- tcpadmin的指导