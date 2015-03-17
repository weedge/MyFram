<?php
/**
 * Date: 13-10-11
 * Time: 上午11:41
 * Todo: 过滤用户非法输入
 */
class HttpFilter
{
    static public function filteRequest(){
        //note 禁止对全局变量注入
        //禁止GLOBALS=xxx的方式注入
        if (isset($_GET['GLOBALS']) ||isset($_POST['GLOBALS']) ||  isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
            exit('GLOBALS IS NOT RUNNING');
        }
        // slashes 处理,如果没有魔法引号处理（自动转义），则手动转义GET/POST/COOKIE/FILES中的单双引号、null反斜线\
        if(!ConfigLoader::getServiceConfig('MAGIC_QUOTES_GPC')) {
            $_GET	= CaseTools::daddslashes($_GET);
            $_POST	= CaseTools::daddslashes($_POST);
            $_COOKIE	= CaseTools::daddslashes($_COOKIE);
            $_FILES	= CaseTools::daddslashes($_FILES);
        }
    }
}
