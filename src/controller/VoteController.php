<?php
/**
 * Class VoteController
 * todo:
 * 1.按抢票顺序进行出票，8,28,58,98...的基准获得门票
 * 2.分为普通登录用户，和发了笑话的升级vip用户，failure:前者抢票失败记录抢票时间，120秒内不能抢票；后者60秒内不能抢票
 * 3.success: 从数据库中获取填写的手机地址发送短信；
 * 4.从数据库中获取抢票成功的用户
 */
class VoteController extends AppController
{
    private $_voteModel;
    public function __construct(){
        $is_test = ConfigLoader::getServiceConfig('IS_TEST');
        if ($is_test==0) {
            $this->_voteModel = new VoteModel('test');
        } else {
            $this->_voteModel = new VoteModel('db');
        }
        $this->before_filter('checkVars',array('grabVote'));
        $this->after_filter('endProcess',array('grabVote'));
    }

    private function checkVars(){

    }

    //抢票
    public function grabVote(){

    }

    //获取抢票成功用户
    public function querySucUsers(){

    }

    public function endPorcess(){

    }
}
