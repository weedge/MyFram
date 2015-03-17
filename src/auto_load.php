<?php
spl_autoload_register("myAutoLoad");
function myAutoLoad($classname)
{
    $classpath = getClassPath();
    if (isset($classpath[$classname]))
    {
        include($classpath[$classname]);
    }
}
function getClassPath()
{
    static $classpath=array();
    if (!empty($classpath)) return $classpath;
    if(function_exists("apc_fetch"))
    {
        $classpath = apc_fetch("sogou:case:genautoload:1394165993");
        if ($classpath) return $classpath;

        $classpath = getClassMapDef();
        apc_store("sogou:case:genautoload:1394165993",$classpath);
    }
    else if(function_exists("eaccelerator_get"))
    {
        $classpath = eaccelerator_get("sogou:case:genautoload:1394165993");
        if ($classpath) return $classpath;

        $classpath = getClassMapDef();
        eaccelerator_put("sogou:case:genautoload:1394165993",$classpath);
    }
    else
    {
        $classpath = getClassMapDef();
    }
    return $classpath;
}
function getClassMapDef()
{
    return array(
        		"TodoController" => "/search/nginx/html/MyFrame/src/controller/TodoController.php",
		"VoteController" => "/search/nginx/html/MyFrame/src/controller/VoteController.php",
		"BaseDao" => "/search/nginx/html/MyFrame/src/dao/BaseDao.php",
		"TodoDao" => "/search/nginx/html/MyFrame/src/dao/TodoDao.php",
		"ErrorInfo" => "/search/nginx/html/MyFrame/src/dictionary/ErrorInfo.php",
		"AppController" => "/search/nginx/html/MyFrame/src/libs/AppController.php",
		"BizException" => "/search/nginx/html/MyFrame/src/libs/BizException.php",
		"CaseTools" => "/search/nginx/html/MyFrame/src/libs/CaseTools.php",
		"ConfigLoader" => "/search/nginx/html/MyFrame/src/libs/ConfigLoader.php",
		"HttpFilter" => "/search/nginx/html/MyFrame/src/libs/HttpFilter.php",
		"Logger" => "/search/nginx/html/MyFrame/src/libs/Logger.php",
		"ObjectContainer" => "/search/nginx/html/MyFrame/src/libs/ObjectContainer.php",
		"PDODriver" => "/search/nginx/html/MyFrame/src/libs/PDODriver.php",
		"RedisUtil" => "/search/nginx/html/MyFrame/src/libs/RedisUtil.php",
		"UnitWork" => "/search/nginx/html/MyFrame/src/libs/UnitWork.php",
		"TodoModel" => "/search/nginx/html/MyFrame/src/model/TodoModel.php",
		"VoteModel" => "/search/nginx/html/MyFrame/src/model/VoteModel.php",
		"Viewer" => "/search/nginx/html/MyFrame/src/view/Viewer.php",

    );
}
?>