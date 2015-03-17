<?php
/**
 * User: wy
 * Date: 13-9-27
 * Time: 上午11:43
 * Todo: PDO
 */
class PDODriver
{

    private $_dbh = null;
    private $_db_encoding = 'utf8';

    public function __construct($db_host, $db_post, $db_user, $db_pass, $db_name)
    {/*{{{*/
        $persistent = empty($_SERVER['argv'][0]) ? true : false;//客户端cli连接持久连接
        $t1 = microtime(true);
        $this->_dbh = new PDO("mysql:host={$db_host};dbname={$db_name};post={$db_post}", $db_user, $db_pass, array(PDO::ATTR_PERSISTENT => $persistent));
        $t2 = round((microtime(true)- $t1)*1000, 3);
        if($t2 > ConfigLoader::getServiceConfig('PDO_CONNECT_TIMEOUT')) {//记录连接超时
            $log_msg = ' timeout: ' . $t2 . ' host: ' . $db_host;
            Logger::writePDOLog($log_msg);
        }
        $this->_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //mysql 5.5.3+ use the encoding utf8mb4
        if (ConfigLoader::getServiceConfig('USE_UTF8MB4')!=0){
            $this->useUTF8mb4();
        }

        $this->_dbh->query("SET character_set_client=binary");
    }/*}}}*/

    protected function useUTF8mb4(){
        $version_res = $this->_dbh->query("select version() as version");
        $version = $version_res->fetch();
        if(preg_match('/(.*)\.(.*)\.(.*)/',$version['version'],$match_arr)){
            if($match_arr[1]>5){
                $this->_db_encoding = "utf8mb4";
            }else if($match_arr[1]==5&&$match_arr[2]==5&&$match_arr[3]>3){
                $this->_db_encoding = "utf8mb4";
            }
        }
    }

    public function query($sql, $values=array())
    {/*{{{*/
        $this->_dbh->query("SET NAMES '{$this->_db_encoding}'");
        if (ConfigLoader::getServiceConfig('LOG_SQL')!=0) $this->log($sql, $values);
        try {
            $sth = $this->_dbh->prepare($sql);
            $i = 0;
            foreach($values as $value) {
                $sth->bindValue(++$i, $value);
            }
            if($sth->execute()) {
                $result = $sth->fetch(PDO::FETCH_ASSOC);
                if($result) return $result;
            }
        } catch (PDOException $e) {
            $this->processError($sql, $e, $values);
        }

        return false;
    }/*}}}*/

    public function querys($sql, $values=array())
    {/*{{{*/
        $this->_dbh->query("SET NAMES '{$this->_db_encoding}'");
        if (ConfigLoader::getServiceConfig('LOG_SQL')!=0) $this->log($sql, $values);
        try {
            $sth = $this->_dbh->prepare($sql);
            $i = 0;
            foreach($values as $value) {
                $sth->bindValue(++$i, $value);
            }
            if($sth->execute()) {
                return $sth->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        catch (PDOException $e)
        {
            $this->processError($sql, $e, $values);
        }
    }/*}}}*/

    public function exeNoQuery($sql, $values=array())
    {/*{{{*/
        $this->_dbh->query("SET NAMES '{$this->_db_encoding}'");
        if (ConfigLoader::getServiceConfig('LOG_SQL')!=0) $this->log($sql, $values);
        try {
            $sth = $this->_dbh->prepare($sql);
            $i = 0;
            foreach($values as $value)
            {
                $sth->bindValue(++$i, $value);
            }
            return $sth->execute();
        }
        catch (PDOException $e)
        {
            $this->processError($sql, $e, $values);
        }
    }/*}}}*/

    public function execute($sql, $values=array())
    {/*{{{*/
        $this->_dbh->query("SET NAMES '{$this->_db_encoding}'");
        if (ConfigLoader::getServiceConfig('LOG_SQL')!=0) $this->log($sql, $values);
        try {
            $sth = $this->_dbh->prepare($sql);
            $i = 0;
            foreach($values as $value)
            {
                $sth->bindValue(++$i, $value);
            }
            $sth->execute();
            return $sth->rowCount();
        }
        catch (PDOException $e)
        {
            $this->processError($sql, $e, $values);
        }
    }/*}}}*/

    public function processError($sql, $e, $values=array())
    {/*{{{*/
        $msg['sql'] = $sql;
        $msg['values'] = var_export($values, true);
        $msg['message'] = $e->getMessage();
        $msg['TraceAsString'] = $e->getTraceAsString();
        Logger::writeSqlErrorLog($msg);
        throw new Exception($e->getMessage());
    }/*}}}*/

    public function setTransactionLevel($level)
    {/*{{{*/
        $sql_set = "SET transaction isolation level $level; ";
        return self::execute($sql_set);
    }/*}}}*/

    public function beginTrans()
    {/*{{{*/
        $this->_dbh->beginTransaction();
    }/*}}}*/

    public function commit()
    {/*{{{*/
        return $this->_dbh->commit();
    }/*}}}*/

    public function rollback()
    {/*{{{*/
        return $this->_dbh->rollback();
    }/*}}}*/

    public function getLastInsertID()
    {/*{{{*/
        return (int)$this->_dbh->lastInsertId();
    }/*}}}*/

    private function log($sql, $values=array())
    {/*{{{*/
        $op = strtolower(substr(trim($sql), 0, 6));
        if($op == 'insert')
        {
            Logger::writeSqlInsertLog($sql, $values);
        }
        elseif($op == 'update')
        {
            Logger::writeSqlUpdateLog($sql, $values);
        }
        else
        {
            Logger::writeSqlExecuteLog($sql, $values);
        }
    }/*}}}*/
}
