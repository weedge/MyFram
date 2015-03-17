<?php
/**
 * User: wy
 * Date: 13-9-27
 * Time: 上午11:42
 * Todo: define vars
 */

// 定义根目录
define("ROOT_PATH",realpath(dirname(dirname(__FILE__))));
// 定义数据目录的路径
define('DATA_PATH', ROOT_PATH.'/data');
// 定义日志目录的路径
define('LOGS_PATH', ROOT_PATH.'/logs');
// 定义每页显示的任务条数
define('TASK_NUM_PRE_PAGE',20);
define('NUM_PRE_PAGE',TASK_NUM_PRE_PAGE);

///设置魔法引用(自动转义，单双引号反斜线)
define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
///判断字符编码转换函数是否存在
define('ICONV_ENABLE', function_exists('iconv'));
///亚洲字符转码函数是否存在, 因为mb_开头字符处理亚洲字符会比较高效，初步判断用于转码时先用mb_来处理:
define('MB_ENABLE', function_exists('mb_convert_encoding'));

//当前时间戳被定义为一个常量，效率更高吧，也不用global了。
define('TIMESTAMP', time());

//定义测试环境
define('IS_TEST',1);

//连接超时时间
define('PDO_CONNECT_TIMEOUT',1000);

//是否使用utf8mb4
define('USE_UTF8MB4',1);//如果知道mysql版本是5.5.3+打开,不知道也可以打开~~~

//是否记录执行sql日志
define('LOG_SQL',1);
