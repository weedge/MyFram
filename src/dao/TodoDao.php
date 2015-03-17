<?php
/**
 * User: wy
 * Date: 13-10-9
 * Time: 下午3:59
 * Todo: todo Dao curd operation
 */
class TodoDao extends BaseDao
{
    protected $_table;

    public function __construct($unit)
    {
        parent::__construct($unit);
        $this->_table = 'case2014_todo_task';
    }

    public function addTask($title,$descript,$date_due,$user)
    {
        $params['title'] = isset($title)?$title:"";
        $params['content'] = isset($descript)?$descript:"";
        $params['date_due'] = isset($date_due)?$date_due:ConfigLoader::getServiceConfig('TIMESTAMP');
        $params['add_time'] = ConfigLoader::getServiceConfig('TIMESTAMP');
        $params['user'] = isset($user)&&!empty($user)?$user:"admin";
        $this->addByArray($params);
        return $this->getLastInsertID();
    }

    public function modifiedTask($title,$descript,$date_due,$user,$todo_id)
    {
        //修改任务
        $params['title'] = isset($title)?$title:"";
        $params['descript'] = isset($descript)?$descript:"";
        $params['date_due'] = isset($date_due)?$date_due:ConfigLoader::getServiceConfig('TIMESTAMP');
        $params['user'] = isset($user)?$user:"admin";
        $sql = "update {$this->_table} set `title`=?,`content`=?,`date_due`=?,add_time`=".ConfigLoader::getServiceConfig('TIMESTAMP').",`user`=? where `id`=? ";
        return $this->exeNoQuery($sql,array($title,$descript,$date_due,$user,$todo_id));
    }

    public function viewTasks($page,$is_done="")
    {
        //查看任务
        $page_count = ConfigLoader::getServiceConfig('TASK_NUM_PRE_PAGE');
        $sql = "select * from {$this->_table} ";
        if(!empty($is_done)){
            $sql .= "where `is_done`={$is_done} ";
        }
        $sql .= "order by `add_time` desc limit {$page},$page_count";
        $res = $this->querys($sql,array());
        if(empty($res)){
            return false;
        }
        return $res;
    }

    public function deleteTask($todo_id)
    {
        //删除任务
        $sql = "delete {$this->_table} where `todo_id`=?";
        $this->exeNoQuery($sql,array($todo_id));
        return $this->getLastInsertID();
    }

    public function getTotalCount($is_done="")
    {
        //任务总数
        $sql = "select count(*) as total_count from {$this->_table} ";
        if(!empty($is_done))
        {
            $sql .= "where `is_done`=?";
        }
        $res = $this->query($sql,array($is_done));

        if(empty($res)){
            return 0;
        }
        return $res['total_count'];
    }

}
