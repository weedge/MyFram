<?php
/**
 * User: wy
 * Date: 13-9-27
 * Time: 上午11:43
 * Todo: acid
 */
class UnitWork
{
    public function __construct()
    {/*{{{*/
        $this->_sqls    = array();
        $this->_caches  = array();
        $this->_trans   = false;
        $this->_executors= array();
        $this->_cluster = '';
    }/*}}}*/

    static public function getInstance()
    {/*{{{*/
        static $unitwork ;
        if($unitwork instanceof UnitWork )
        {
            return $unitwork;
        }
        $unitwork = new UnitWork();
        return $unitwork;
    }/*}}}*/

    private function getExecutor($db_conf)
    {/*{{{*/
        static $executors = array();
        if(empty($db_conf)) return false;
        $key = md5(serialize($db_conf));
        if(isset($executors[$key]))
        {
            return $executors[$key];
        }
        else
        {
            $execurot = new PDODriver($db_conf['db_host'],$db_conf['db_user'], $db_conf['db_pass'],$db_conf['db_name']);
            $executors[$key] = $execurot;
        }
        return $execurot;
    }/*}}}*/

    public function regSql($cluster, $sql, $values)
    {/*{{{*/
        if($this->_trans)
        {
            $key = md5(serialize($cluster));
            $this->_sqls[$key]['cluster'] = $cluster;
            $this->_sqls[$key]['sqls'][]=array("sql"=>$sql,"values"=>$values);
            return true;
        }
        return false;
    }/*}}}*/

    public function regCache($s,$op,$params)
    {/*{{{*/
        if($this->_trans)
        {
            $this->_caches[] = array('s'=>$s,'op'=>$op,'params'=>$params);
            return true;
        }
        return false;
    }/*}}}*/

    public function needTrans()
    {/*{{{*/
        return $this->_trans;
    }/*}}}*/

    private function exeNoQuery($executor, $sqls)
    {/*{{{*/
        if(empty($executor)) return false;
        foreach($sqls as $val)
        {
            $res = $executor->exeNoQuery($val['sql'], $val['values']);
            if($res==false)
            {
                return false;
            }
        }
        return true;
    }/*}}}*/

    private function exeCacheQuery()
    {
        foreach($this->_caches as $val)
        {
            if($val['op'] == 'delete')
            {
                $val['s']->delete($val['params']['key']);
            }
            if($val['op'] == 'set')
            {
                $val['s']->set($val['params']['key'],$val['params']['val'],$val['params']['expire']);
            }
        }
        return true;
    }

    public function beginTrans()
    {/*{{{*/
        $this->_trans=true;
    }/*}}}*/

    public function endTrans()
    {/*{{{*/
        $this->_trans = false;
    }/*}}}*/
    public function rollback()
    {/*{{{*/
        foreach($this->_executors as $key => $conn)
        {
            $conn->rollback();
        }
    }/*}}}*/

    public function commit()
    {/*{{{*/
        if($this->_trans && !empty($this->_sqls))
        {
            foreach($this->_sqls as $key=>$value)
            {
                try
                {
                    $conn = $this->getExecutor($value['cluster']);
                    $this->_executors[$key] = $conn ;
                    $conn->beginTrans();
                    $ret = $this->exeNoQuery($conn, $value['sqls']);
                    if($ret)
                    {
                        $this->_sqls[$key]['sqls'] = array();
                    }
                    $conn->commit();
                }
                catch(PDOException $e)
                {
                    $this->rollback();
                    $this->endTrans();
                    throw new BizException(ErrorMessage::$err_sys);
                    return false;
                }
            }
            if(!empty($this->_caches))
            {
                $this->exeCacheQuery();
            }
        }
        $this->endTrans();
        return true;
    }/*}}}*/
}
?>
