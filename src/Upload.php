<?php
/**
 * 公共上传，都能使用
 */
namespace ttiantianle\upload;
class Upload
{
    public static $imgExtConf = ['.gif', '.jpg', '.jpeg', '.bmp', '.png', '.swf'];    //图片允许的后缀
    public static $nameRoleConf = ['timeStampName','uniqName'];

    protected $file = [];
    protected $ext = [];
    protected $size = 0;                        //不限大小 字节
    protected $path = '';                       //上传路径

    public function __construct($ext=[],$size=0,$path='')
    {
        $this->ext = empty($ext) ?  self::$imgExtConf : $ext ;
        $this->size = intval($size) >0 ? intval($size) : 0;
        $this->path = empty($path) ?  self::defaultPath() : $path ;
    }

    /**
     * 设置上传大小
     * @param int $size
     */
    public function setSize($size=0){
        if (intval($size)<=0){
            $this->size = 0;
            return;
        }
        $this->size = intval($size);
        return;
    }

    /**
     * 设置扩展
     * @param $ext
     */
    public function setExt($ext)
    {
        //todo 验证输入的后缀是否合法
        if (empty($ext)){
            $this->ext = self::$imgExtConf;
            return;
        }
        if (is_string($ext)){
            $this->ext = explode(',',$ext);
            return;
        }
        if (is_array($ext)){
            $this->ext = $ext;
            return;
        }
    }

    public function setPath($path){
        $this->path = $path;
        return;
    }

    /**
     * @param string $nameRole timeStampName和uniqidName可选
     * @return array
     */
    public function file($nameRole="timeStampName")
    {
        $path = $this->path;
        if (!in_array($nameRole,self::$nameRoleConf)){
           return $this->arrayOut('1','名命规则不合法');
        }
        $this->file = isset($_FILES) ? $_FILES : [];
        $arr = [];
        if (!empty($this->file)){
//            验证图片
            $resCheck = self::validate($this->ext,$this->size);
            switch ($resCheck){
                case 1 : return $this->arrayOut('3','存在格式不正确文件');
                case 2 : return $this->arrayOut('4','存在超出限制大小文件');
            }

            //处理上传文件
           foreach ($this->file as $v)      //可以一次上传多图片（name值不同），建议只上传同一name图片，可以多张
           {
               if (is_dir($path) || mkdir($path, 0755, true)) {
                   if (is_array($v['name'])){  //多图上传
                        foreach ($v['tmp_name'] as $k=>$va){
                            $fileName = $path.DIRECTORY_SEPARATOR.$this->$nameRole().self::getFileExt($v['name'][$k]);
                            if (move_uploaded_file($va,$fileName)){
                                $arr[] =  $fileName;
                            }
                        }

                   }else{                    //单图上传
                       $fileName = $path.DIRECTORY_SEPARATOR.$this->$nameRole().self::getFileExt($v['name']);
                      if (move_uploaded_file($v['tmp_name'],$fileName)){
                          $arr[] =  $fileName;
                      }

                   }
               }


           }
           return $this->arrayOut('0','上传成功',$arr);
        }
        return $this->arrayOut('2','未找到上传图片');
    }

    private function getFileExt($name)
    {
        return $ext = substr($name,strripos($name,'.'));
    }

    /**
     * 验证类型和大小
     */
    private function validate($type=[],$size=0)
    {
        foreach ($this->file as $v) {
            if (is_array($v['name'])) {  //多图上传
                foreach ($v['name'] as $k => $va) {
                    if (!in_array(self::getFileExt($va),$type)){
                        return 1;
                    }
                    if ($size>0 && $v['size'][$k]>$size){
                        return 2;
                    }
                }

            } else {                    //单图上传
                if(!in_array(self::getFileExt($v['name']),$type)){
                    return 1;//格式错误
                }
                if ($size>0 && $v['size']>$size){
                    return 2;//超出大小
                }
            }
        }
        return 0;
    }

    private function defaultPath()
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.date("Y").DIRECTORY_SEPARATOR.date('md').DIRECTORY_SEPARATOR;
    }

    /**
     * 以时间名命20200527194211+1-9999随机串
     */
    private function timeStampName()
    {
        $name = date('YmdHis',time());
        $rand = str_pad(rand(1,100),4,'0',STR_PAD_LEFT);
        return $name.$rand;
    }

    /**
     * 以uniqid来名命
     */
    private function uniqName()
    {
        return substr(md5(uniqid()),0,18 );
//        return uniqid(time());
    }

    private function jsonOut($code='0',$msg='',$data=[]){
        $arr = array(
            'code'      => $code,
            'message'   => $msg,
            'data'      => $data
        );
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
    }
    private function arrayOut($code='0',$msg='',$data=[]){
     $arr = array(
            'code'      => $code,
            'message'   => $msg,
            'data'      => $data
        );
     return $arr;
    }
}