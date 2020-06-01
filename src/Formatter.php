<?php
/**
 * 输出格式化
 * 方便显示图片
'components' => [
'formatter' => [
'class' => 'app\widgets\Formatter',
],
],
 *
 */
namespace ttiantianle\upload;

use yii\bootstrap\Html;

class Formatter extends \yii\i18n\Formatter{

    public function asAvatar($val){
        if(!$val) return null;
        $format = '70';
        return Html::img($val, ['style'=>"max-width:{$format}px;max-height:{$format}px"]);
    }
    public function asImages($val)
    {
        if(!$val) return null;
        $arr = json_decode($val,true);
        if (!is_array($arr)||empty($arr)) return null;
        $format = '70';
        $re = '';
        foreach ($arr as $v){
            $re .= Html::img($v, ['style'=>"margin-right:5px;max-width:{$format}px;max-height:{$format}px"]);
        }
        return $re;
    }

    public function asJson($val){
        return '<pre>'.json_encode(json_decode($val), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT).'</pre>';
    }
}