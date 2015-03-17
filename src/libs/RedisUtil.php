<?php
/**
 * redis的接口类，作为对redis的一个简单包装
 */
class RedisUtil
{
    private $host = "";
    private $port = "";
    private $_redis = NULL; //保存redis实例
    private static $instances = array(); //全局缓存区
    private function __construct ($server) {
        $this->host = $server['host'];
        $this->port = $server['port'];
        try {
            $redis = new Redis();
            $redis->connect($this->host, $this->port);
            $this->_redis = $redis;
        } catch (Exception $e) {
            $msg = "error(" . $e->getMessage() . ")";
            $this->logError($msg);
            $this->_redis = NULL;
        }
    }
    private function logError ($msg) {
        $file = "/tmp/appstorage_ad_redis.err.log";
        $serverStr = date("Y-m-d\TH:i:s") . " [{$this->host}:{$this->port}] ";
        file_put_contents($file, $serverStr . $msg . "\n", FILE_APPEND);
    }
    /*
     * 获取redis的全局实例
     * @param array $servers array(array("host" => "111", "port" => 123));                           
     */
    public static function instance ($server) {
        $host = $server['host'];
        $port = $server['port'];
        if (! $host || ! $port) {
            return false;
        }
        $key = $host . ":" . $port;
        if (! isset(self::$instances[$key])) {
            self::$instances[$key] = new RedisUtil($server);
        }
        return self::$instances[$key];
    }
    public function __call ($name, $arguments) {
        if (! $this->_redis) {
            $msg = "redis is null(invoking $name)";
            self::logError($msg);
            return false;
        }
        return call_user_func_array(array($this->_redis, $name), $arguments);
    }
}
