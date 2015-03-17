<?php
/**
 * User: wy
 * Date: 13-10-10
 * Time: 下午3:49
 * Todo: model
 */

class TodoModel
{
    private $_title;
    private $_descript;
    private $_date;
    private $_is_done;
    private $_todo_dao;

    public function __construct($unit){
        $this->_todo_dao = new TodoDao($unit);
    }

    public function __set($key,$value){
        $this->$key = $value;
    }

    public function __get($key){
        if(isset($this->$key)){
            return $this->$key;
        }
        return null;
    }

    public function query($page){
        $total_count = $this->_todo_dao->getTotalCount($this->_is_done);
        $cur_page = isset($page)?$page:0;
        $res['total_num'] = $total_count;
        $res['data'] = $this->_todo_dao->viewTasks($cur_page,$this->_is_done);
        $res['cur_page'] = $cur_page;
        return $res;
    }
}
