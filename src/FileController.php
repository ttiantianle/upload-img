<?php
namespace ttiantianle\upload; //自定义命名空间，放到自己相放的位置 common/controllers
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
     * @param array $allowExt  ['image', 'html', 'text', 'video', 'audio', 'flash','pdf']
     * @return array
     */
    public function actionUploadFile()
    {
        $allowExt= \Yii::$app->request->get('type','image');
        $path=\Yii::$app->request->get('path','');
        $maxSize=\Yii::$app->request->get('size','20480000');
        $instance = UploadedFile::getInstanceByName('file');
        if (!$instance) return ['error'=>'请选择文件'];
        $ext = $instance->getExtension();
        $val = self::validateExt($ext,$allowExt);

        if (!$val){
            return ['error'=>"不支持该文件类型"];
        }
        if ($instance->size > $maxSize){
            return ['error'=>'文件大小不得大于'.self::formatSize($maxSize)];
        }
        if ($instance->error != UPLOAD_ERR_OK){
            return ['error' => 'upload error errorNo:'.$instance->error];
        }

        if (!$path){
            $basePath = \Yii::$app->basePath;
            $path=$basePath.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR;
            if (!is_dir($path)){
                mkdir($path,'755');
            }
            $url = $path . $instance->baseName . '.' . $instance->extension;
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

    /**
     * ['image', 'html', 'text', 'video', 'audio', 'flash', 'object','pdf','other']
     *
     */
    private function validateExt($ext,$type='image')
    {
        if (!in_array($type, ['image', 'html', 'text', 'video', 'audio', 'flash', 'pdf'])){
            return false;
        }
        $val = false;
        switch ($type){
            case 'image':
                $val = in_array($ext,['jpg','gif','png','jpeg']) ? true: false;
                break;
            case 'html':
                $val = in_array($ext,['html','htm','shtml','shtm']) ? true: false;
                break;
            case 'text':
                $val = in_array($ext,['txt','docx','doc','xls']) ? true: false;
                break;
            case 'video':
                $val = in_array($ext,['flv','avi','mov','mp4','wmv']) ? true: false;
                break;
            case 'audio':
                $val = in_array($ext,['mp3','wma','midi','wav']) ? true: false;
                break;
            case 'flash':
                $val = in_array($ext,['swf','exe']) ? true: false;
                break;
            case 'pdf':
                $val = in_array($ext,['pdf']) ? true: false;
                break;
            default:$val=false;break;
        }
        return $val;
    }
}