<?php
namespace app\controllers; //自定义命名空间，放到自己相放的位置 common/controllers
use yii\web\Controller;

use yii\web\Response;
use yii\web\UploadedFile;

class FileController extends Controller
{
    public $enableCsrfValidation = false;//关闭csrf验证
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ],
            ]
        ];
    }

    /**
     * 上传图片，todo 可能存在的问题，上传同名文件会被覆盖,换一个命名规则
     * @param string $path
     * @param string $maxSize
     * @param array $allowExt
     * @return array
     */
    public function actionUploadFile($path='',$maxSize='20480000',$allowExt=['jpg','gif','png','jpeg'])
    {
        $instance = UploadedFile::getInstanceByName('file');
        if (!$instance) return ['error'=>'请选择文件'];
        $ext = $instance->getExtension();
        if (!in_array($ext,$allowExt)){
            return ['error'=>"不支持该文件类型"];
        }
        if ($instance->size > $maxSize){
            return ['error'=>'文件大小不得大于'.self::formatSize($maxSize)];
        }
        if ($instance->error != UPLOAD_ERR_OK){
            return ['error' => 'upload error errorNo:'.$instance->error];
        }

        if (!$path){
            if (!is_dir('uploads')){
                mkdir("uploads",'755');
            }
            $url = 'uploads/' . $instance->baseName . '.' . $instance->extension;
        }

        if ($path && is_dir($path)){
            $url =  $url = $path.DIRECTORY_SEPARATOR. $instance->baseName . DIRECTORY_SEPARATOR . $instance->extension;
        }
        if ($path && !is_dir($path)){
            mkdir($path,'755');
            $url =  $url = $path.DIRECTORY_SEPARATOR. $instance->baseName . DIRECTORY_SEPARATOR . $instance->extension;
        }

        if($instance->saveAs($url)){
            return ['filelink'=>$url, 'filename'=>basename($url)];
        }
        return ['error'=>"上传失败,请稍后重试"];

    }
    public function actionDelFile()
    {
        return "{}";
    }
    private function formatSize($size){
        if ($size > 1024000){
            $res = floatval($size/1024000);
            return $res.'M';
        }
        if ($size > 1024){
            $res = floatval($size/1024);
            return $res.'K';
        }
        return $size.'B';
    }
}