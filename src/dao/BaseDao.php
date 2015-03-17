<?php
/**
 * User: wy
 * Date: 13-9-30
 * Time: 上午11:24
 * Todo: 
 */
class BaseDao
{
    protected  $_executor;
    protected  $_unit;
    protected  $_cluster;

    protected function __construct($unit)
    {
        $this->_unit = $unit;
        $this->_cluster['test'] = ConfigLoader::getDBConfig('TEST_SUBJECT');
        $this->_cluster['mdb'] = ConfigLoader::getDBConfig('MDB_SUBJECT');
        $this->_cluster['sdb'] = ConfigLoader::getDBConfig('SDB_SUBJECT');
        $this->_cluster['db'] = ConfigLoader::getDBConfig('PROXY_DB');
        //$db_conf = ConfigLoader::getDbConfig('SUBJECT');
        //if(!$this->_executor)
        //$this->_executor = new PDODriver($db_conf['db_host'], $db_conf['db_user'], $db_conf['db_pass'], $db_conf['db_name']);
    }

    protected function getExecutor()
    {/*{{{*/
        static $executors = array();
        switch($this->_unit)
        {
            case "mdb":
                $key_mdb = md5(serialize($this->_cluster['mdb']));
                if(isset($executors[$key_mdb]))
                {
                    $this->_executor = $executors[$key_mdb];
                }
                else
                {
                    $db_conf = $this->_cluster['mdb'];
                    $this->_executor = new PDODriver($db_conf['host'], $db_conf['port'], $db_conf['user'], $db_conf['pwd'], $db_conf['db']);
                    $executors[$key_mdb] = $this->_executor;
                }
                break;
            case "sdb":
                $key_sdb = md5(serialize($this->_cluster['sdb']));
                if(isset($executors[$key_sdb]))
                {
                    $this->_executor = $executors[$key_sdb];
                }
                else
                {
                    $db_conf = $this->_cluster['sdb'];
                    $this->_executor = new PDODriver($db_conf['host'], $db_conf['port'], $db_conf['user'], $db_conf['pwd'], $db_conf['db']);
                    $executors[$key_sdb] = $this->_executor;
                }
                break;
            case "db":
                $key_db = md5(serialize($this->_cluster['db']));
                if(isset($executors[$key_db]))
                {
                    $this->_executor = $executors[$key_db];
                }
                else
                {
                    $db_conf = $this->_cluster['db'];
                    $this->_executor = new PDODriver($db_conf['host'], $db_conf['port'], $db_conf['user'], $db_conf['pwd'], $db_conf['db']);
                    $executors[$key_db] = $this->_executor;
                }
                break;
            case "test":
            default:
                $key_test = md5(serialize($this->_cluster['test']));
                if(isset($executors[$key_test]))
                {
                    $this->_executor = $executors[$key_test];
                }
                else
                {
                    $db_conf = $this->_cluster['test'];
                    $this->_executor = new PDODriver($db_conf['host'], $db_conf['port'], $db_conf['user'], $db_conf['pwd'], $db_conf['db']);
                    $executors[$key_test] = $this->_executor;
                }
                break;
        }
    }/*}}}*/


    public function addByArray($pairs)
    {/*{{{*/
        $field = $space = '';
        $values = array();
        foreach($pairs as $k => $v)
        {
            $field .= "{$space}{$k} = ? ";
            $space = ",";
            array_push($values, $v);
        }
        $sql = "insert into {$this->_table} set {$field}";
        return $this->exeNoQuery($sql, $values);
    }/*}}} */

    public function query($sql, $values=array())
    {/*{{{*/
        $this->getExecutor();
        return $this->_executor->query($sql, $values);
    }/*}}}*/

    public function querys($sql, $values=array())
    {/*{{{*/
        $this->getExecutor();
        return $this->_executor->querys($sql, $values);
    }/*}}}*/

    public function exeNoQuery($sql, $values=array())
    {/*{{{*/
        if($this->_unit=="mdb"){
            $cluster = $this->_cluster['mdb'];
        }else if($this->_unit=="db"){
            $cluster = $this->_cluster['db'];
        }else{
            $cluster = $this->_cluster['test'];
        }
        $unitwork = UnitWork::getInstance();
        if($unitwork->needTrans())
        {
            return $unitwork->regSql($cluster, $sql, $values);
        }else
        {
            $this->getExecutor();
            return $this->_executor->exeNoQuery($sql, $values);
        }
    }/*}}}*/

    public function execute($sql, $values=array())
    {/*{{{*/
        $this->getExecutor();
        return $this->_executor->execute($sql, $values);
    }/*}}}*/

    public function getLastInsertID()
    {/*{{{*/
        return $this->_executor->getLastInsertID();
    }/*}}}*/

}
