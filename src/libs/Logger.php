<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-29
 * Time: 上午11:15
 * Todo: 记录日志文件
 */
class Logger
{
    const TYPE_BIZ_PROCESS 	= 'biz_process';
    const TYPE_BIZ_ERROR	= 'biz_error';
    const TYPE_SQL_INSERT	= 'sql_insert';
    const TYPE_SQL_UPDATE	= 'sql_update';
    const TYPE_SQL_EXECUTE	= 'sql_execute';
    const TYPE_SQL_ERROR	= 'sql_error';
    const TYPE_LOGIN_COST	= 'login_cost';
    const TYPE_DB_CONNECT	= 'db_connect';
    const TYPE_EXCEPTION	= 'exception';
    const TYPE_PDO		= 'pdo_connect';
    const TYPE_REG_SRC      = 'reg_src';
    const TYPE_MDF_SRC      = 'mdf_src';
    const TYPE_ALL_COST     = 'all_cost';
    const TYPE_OTHER	= 'other';
    const TYPE_WEATHER_CONFIG	= 'gen_weather_config';
    const TYPE_CMD_ERROR	= 'cmd_error';
    const TYPE_CMD_PROCESS	= 'cmd_process';

    static private function getLogFile($log_type)
    {/*{{{*/
        $dir = ConfigLoader::getServiceConfig('LOGS_PATH')."/{$log_type}/";
        if(!is_dir($dir)){
            @mkdir($dir,0755);
        }
        if ($log_type == self::TYPE_WEATHER_CONFIG){
            $file = $dir . "gen_weac.conf";
        } else {
            $file = $dir . $log_type . '.log.' . date('Ymd');
        }
        return $file;
    }/*}}}*/

    static public function writeLog($log_type, $msg, $prefix = true)
    {/*{{{*/
        $mode = $log_type==self::TYPE_WEATHER_CONFIG ? NULL : FILE_APPEND;

        if($_SERVER['DOCUMENT_ROOT'] == '') {
            return false; // 命令行下运行不记录日志
        }
        $file = self::getLogFile($log_type);
        if(is_array($msg)) {
            $msg = var_export($msg, true);
        }
        $ip = CaseTools::getIP();
        if ($prefix) {
            $msg = $ip . ' ' . date('Y-m-d H:i:s') . ' ' . $msg . PHP_EOL;
        }else {
            $msg .= PHP_EOL;
        }
        return file_put_contents($file,$msg,$mode);
    }/*}}}*/

    static public function writeWeatherConfig($msg)
    {
        return self::writeLog(self::TYPE_WEATHER_CONFIG, $msg,false);
    }

     static public function writeCmdProcessLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_CMD_PROCESS, $msg);
    }/*}}}*/
   
    static public function writeCmdErrorLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_CMD_ERROR, $msg);
    }/*}}}*/

    static public function writeBizProcessLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_BIZ_PROCESS, $msg);
    }/*}}}*/

    static public function writeBizErrorLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_BIZ_ERROR, $msg);
    }/*}}}*/

    static public function writeSqlInsertLog($sql, $value=false)
    {/*{{{*/
        $msg = $sql;
        if($value)
        {
            $msg = array('sql' => $sql, 'value' => $value);
        }
        return self::writeLog(self::TYPE_SQL_INSERT, $msg);
    }/*}}}*/

    static public function writeSqlUpdateLog($sql, $value=false)
    {/*{{{*/
        $msg = $sql;
        if($value)
        {
            $msg = array('sql' => $sql, 'value' => $value);
        }
        return self::writeLog(self::TYPE_SQL_UPDATE, $msg);
    }/*}}}*/

    static public function writeSqlExecuteLog($sql, $value=false)
    {/*{{{*/
        $msg = $sql;
        if($value)
        {
            $msg = array('sql' => $sql, 'value' => $value);
        }
        return self::writeLog(self::TYPE_SQL_EXECUTE, $msg);
    }/*}}}*/

    static public function writeSqlErrorLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_SQL_ERROR, $msg);
    }/*}}}*/

    static public function writeLoginCostLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_LOGIN_COST, $msg);
    }/*}}}*/

    static public function writeDbConnectLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_DB_CONNECT, $msg);
    }/*}}}*/

    static public function writeExceptionLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_EXCEPTION, $msg);
    }/*}}}*/

    static public function writeOtherLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_OTHER, $msg, false);
    }/*}}}*/

    static public function writePDOLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_PDO, $msg);
    }/*}}}*/

    static public function writeRegSrcLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_REG_SRC, $msg);
    }/*}}}*/

    static public function writeMdfSrcLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_MDF_SRC, $msg);
    }/*}}}*/

    static public function writeAllCostLog($msg)
    {/*{{{*/
        return self::writeLog(self::TYPE_ALL_COST, $msg);
    }/*}}}*/

}
