<?php
/**
 * User: wy
 * Date: 13-9-23
 * Time: 下午3:49
 * Todo:
 */
class ObjectContainer {
    private $_objects = array();

    static public function getInstance(){
        static $container = null;
        if(is_null($container)){
            $container = new ObjectContainer();
        }
        return $container;
    }

    static function find($model){
        $container = self::getInstance();
        return $container->get($model);
    }

    private function get($model){
        if(!isset($this->_objects[$model])){
            $this->set($model);
        }
        return $this->_objects[$model];
    }

    private function set($model){
        $this->_objects[$model] = new $model;
    }

}

