<?php
/**
 * User: Administrator
 * Date: 13-9-30
 * Time: 下午2:32
 * Todo: base viewer
 */
class Viewer
{
    public function __set($key,$value){
        $this->$key = $value;
    }

    public function __get($key){
        if(isset($this->$key)){
            return $this->$key;
        }
        return null;
    }

    public function render($action){//渲染文件路径
        include(dirname(dirname(__FILE__))."/scripts/_html/".$action.".phtml");
    }
}
