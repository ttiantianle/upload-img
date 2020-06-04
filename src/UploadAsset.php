<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace ttiantianle\upload;
use yii\web\JqueryAsset;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * This asset bundle provides the base JavaScript files for the Yii Framework.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UploadAsset extends AssetBundle
{
    public $sourcePath = '@ttiantianle/upload/src/fileinput';
    public $css = [
        "css/fileinput.css"
    ];
    public $js = [
        "js/fileinput.js",
        "js/locales/zh.js",
    ];
    public $jsOptions = [
      'position'=>View::POS_HEAD
    ];
    public $depends = [

    ];
}
