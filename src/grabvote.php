<?php
/**
 * User: wy
 * Time: 下午5:12
 * Todo:
 */
ini_set('include_path',ini_get('include_path').':'.dirname(dirname(__FILE__)).'/config/');
date_default_timezone_set("PRC");
require_once('auto_load.php');
echo "<pre>";

session_start();


/*======防刷==========*/
/*======防刷==========*/

function commom_soa_api(){
//在一个try-catch块中包含所有代码，来捕获所有可能的异常! todo: 实现token
    try {
        //过滤请求
        HttpFilter::filteRequest();

        //获得在POST/GET request中的所有参数
        $params = $_REQUEST;

        //获取controller并把它正确的格式化使得第一个字母总是大写的
        $controller = ucfirst(strtolower($params['controller']));

        //获取action并把它正确的格式化，使它所有的字母都是小写的，并追加一个'Action'
        $action = strtolower($params['action']).'Action';

        //检查controller是否存在。如果不存在，抛出异常
        if( file_exists("controller/{$controller}.php") ) {
            include_once "controller/{$controller}.php";
        } else {
            throw new Exception('Controller is invalid.');
        }

        //创建一个新的controller实例
        $controller = new $controller();

        //执行action
        $action = isset($_REQUEST['action'])?strtolower(substr($_GET['action'], 0, 1)).substr($_GET['action'], 1):'queryTasks';
        $result['data'] = $controller->run($action);
        $result['success'] = true;

    } catch( Exception $e ) {
        //捕获任何一次样并且报告问题
        $result = array();
        $result['success'] = false;
        $result['errormsg'] = $e->getMessage();
    }

	//回显调用API的结果
    echo json_encode($result);
    exit();
}

//直接访问
try
{
    //过滤请求
    HttpFilter::filteRequest();

    $controller = new TodoController();
    $action = isset($_GET['action'])?strtolower(substr($_GET['action'], 0, 1)).substr($_GET['action'], 1):'queryTasks';
    $controller->run($action);
}
catch(BizException $e)
{
    $log_info = $_GET;
    $log_info['err_msg'] =  $e->getMessage();
    Logger::writeBizErrorLog($log_info);
    //$process_result = "Error ".$e->getCode();
    $process_result = "Error ".$e->getMessage();
}
catch(Exception $e)
{
    Logger::writeExceptionLog($e->getTraceAsString());
    $process_result = "Error sys";
}

$log_msg = "";
$space = "";
foreach($_GET as $k=>$v)
{
    $log_msg .=$space."$k=$v";
    $space = "&";
}

Logger::writeBizProcessLog("[".$log_msg."] [".$process_result."]");
