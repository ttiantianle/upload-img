<?php
require_once "vendor/autoload.php";
$class = new \ttiantianle\upload\Upload();
$res = $class->file();
echo json_encode($res,JSON_UNESCAPED_UNICODE);die;