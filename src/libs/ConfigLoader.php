<?php
/**
 * User: wy
 * Date: 13-9-29
 * Time: 下午1:24
 * Todone: 加载配置文件
 */
class ConfigLoader
{
    static public function getServiceConfig($key){
        static $configs = array();
        if(array_key_exists($key,$configs)){
            return $configs[$key];
        }
        $configs = self::_getServiceConfigs();
        $config = isset($configs[$key]) ? $configs[$key] : false;
        return $config;
    }

    static public function getDBConfig($key){
        static $configs = array();
        if(array_key_exists($key,$configs)){
            return $configs[$key];
        }
        $configs = self::_getDBConfigs();
        $config = isset($configs[$key]) ? $configs[$key] : false;
        return $config;
    }

    static public function getMemConfig($key){
        static $configs = array();
        if(array_key_exists($key,$configs)){
            return $configs[$key];
        }
        $configs = self::_getMemConfigs();
        $config = isset($configs[$key]) ? $configs[$key] : false;
        return $config;
    }

    private function _getServiceConfigs(){
        include('config.server.php');
        $configs = get_defined_constants();
        return $configs;
    }

    private function _getDBConfigs(){
        $configs = include('config.db.php');
        return $configs;
    }

    private function _getMemConfigs(){
        $configs = include('config.memc.php');
        return $configs;
    }

    public function getWeaConfig(){
        $configs = include('config.weac.php');
        return $configs;
    }
}
