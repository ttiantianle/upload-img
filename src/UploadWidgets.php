<?php
/**
 * 中文：http://bootstrap-fileinput.com/useage.html
 * 官网文档：https://plugins.krajee.com/file-input
 */
namespace ttiantianle\upload;

use Yii;
use yii\helpers\Console;
use yii\web\View;

class UploadWidgets
{
    private static $staticRegisterInstance = false;
    private function getVIew(){
        return Yii::$app->getView();
    }

    /**
     * 预览文件配置项
     * @param $file
     * @param string $type 取值：['image', 'html', 'text', 'video', 'audio', 'flash', 'object','pdf','other']
     * @return array
     */
    private function generatePreview($file,$type='image')
    {
        if (!in_array($type,['image', 'html', 'text', 'video', 'audio', 'flash', 'object','pdf'])){
            return ["[]","[]"];
        }
        if (!$file) return ["[]","[]"];
        if (!is_array($file)) $file=[$file];
        $preview = [];
        $previewConfig = [];
        foreach ($file as $v)
        {
            $preview[] =$v;
            $previewConfig[] = array('key'=>$v,'type'=>$type);
        }
        $initialPreview = '["'.implode('","',$preview).'"]';
        $initialPreviewConfig = json_encode($previewConfig,true);
        return [$initialPreview,$initialPreviewConfig];

    }

    /**
     * 注册静态资源，需要放在js前面filinput.js要在zh.js之前引入
     * @throws \yii\base\InvalidConfigException
     */
    private function registerStaticFile()
    {
        if (self::$staticRegisterInstance) return;
        $view = self::getVIew();

//        $view->registerCssFile($staticPath."/css/fileinput.css");
        $view->registerCssFile('/css/fileinput.js',['depends'=>'ttiantianle\upload\UploadAsset','position'=>\yii\web\View::POS_HEAD]);

        $view->registerJsFile('/js/fileinput.js',['depends'=>'ttiantianle\upload\UploadAsset','position'=>\yii\web\View::POS_HEAD]);
        $view->registerJsFile("/js/locales/zh.js",['depends'=>'ttiantianle\upload\UploadAsset','position'=>\yii\web\View::POS_HEAD]);
        self::$staticRegisterInstance = true;
//        return $view;
    }

    /**
     * 虽然可以上传多种类型，但是每次上传只允许一种，比如多文件上传，只能同时上传一种类型文件
     * @param string $inputId
     * @param string $arrtValue 单文件时是字符串，多文件时需要传数组
     * @param int $maxFileCount
     * @param string $type ['image', 'html', 'text', 'video', 'audio', 'flash', 'object','pdf']
     * @return string
     */
    public static function uploadFile($inputId='',$arrtValue='',$maxFileCount =1,$type="image",$path='',$size='20480000')
    {

        $view = Yii::$app->getView();
        self::registerStaticFile();
        $uniqId = self::randomFieldName();
        $uploadInputId = 'upload-'.$uniqId;
        list($initialPreview,$initialPreviewConfig) = self::generatePreview($arrtValue);
        $js = <<<JS
$(function(){
    // console.log('$initialPreview')
    $('#$uploadInputId').fileinput({
        language: 'zh',
        uploadUrl: '?r=file/upload-file&type=$type&path=$path&size=$size',
        deleteUrl: '?r=file/del-file',
        overwriteInitial: false,
        allowedFileTypes: [$type],
        initialPreviewAsData: true,
        initialPreviewShowDelete:true,
        maxFileCount:$maxFileCount,
        initialPreview:$initialPreview,
        initialPreviewConfig:$initialPreviewConfig,
    })});


$('#$uploadInputId').on('fileuploaded', function(event, data, previewId, index) {
  var form = data.form, files = data.files, extra = data.extra,
      response = data.response, reader = data.reader;
  if (!response.filelink){
      alert(response.error)
        return false;
  }
  // const fileurl = $('#$uploadInputId').data('fileinput').initialPreview;
   $('#$uploadInputId').data('fileinput').initialPreview.push(response.filelink);
   if ($maxFileCount ==1 ){
      $('#$inputId').val(response.filelink);
   }else{
       const fileUrl = $('#$uploadInputId').data('fileinput').initialPreview.filter(Boolean)
       $('#$inputId').val(JSON.stringify(fileUrl));
   }
});
$('#$uploadInputId').on('fileselect', function(event, numFiles, label) {
        console.log("fileselect");
    });
$('#$uploadInputId').on('filepredelete', function(event, key) {
   return !confirm("确定删除吗？")
});

$('#$uploadInputId').on('filedeleted', function(event, key) {
    if ($maxFileCount>1){
        const imgurl = $('#$uploadInputId').data('fileinput').initialPreview.filter(Boolean)
       $('#$inputId').val(JSON.stringify(imgurl));
    }else{
          $('#$inputId').val("");
    }
   
});
//$('#$uploadInputId').on('fileuploaderror', function(event, data, msg) {
// alert("上传失败");
//});
$('#$uploadInputId').on('filedeleteerror', function(event, data, msg) {
 alert("删除失败");
});
JS;


        $view->registerJs($js);
        $multiple = $maxFileCount>1? "multiple " : "";
        return <<<HTML
<div class="form-group">
<input type="file" id="$uploadInputId" name="file" $multiple />
<div class="help-block">Avatar cannot be blank.</div>
</div>
HTML;

    }

    /**
     * 生成唯一的id，防止出现同id
     * @return string
     */
    private static function randomFieldName($len=6){
        return substr(md5(uniqid()),0,$len);
    }

}