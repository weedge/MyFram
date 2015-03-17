<?php
/**
 * User: wy
 * Date: 13-9-30
 * Time: 下午2:30
 * Todone: 实现一个todolist的控制
 */
class TodoController extends AppController
{
    protected $_todoModel;
    public function __construct()
    {
        $is_test = ConfigLoader::getServiceConfig('IS_TEST');
        if($is_test!=0){
            $this->_todoModel = new TodoModel('test');
        }else{
            switch($_GET['action']){
                case "addTask":
                case "updateTask":
                case "deleteTask":
                    $this->_todoModel = new TodoModel('mdb');
                    break;
                case "queryTasks":
                    $this->_todoModel = new TodoModel('sdb');
                    break;
            }
        }
        $this->before_filter('checkVars', array('addTask','updateTask','queryTasks'));
    }

    public function checkVars()
    {
        //审核请求的参数
        $action = $_GET['action'];

        switch ($action) {
        case 'addTask':
        case 'updateTask':
            if(!isset($_POST['content'])||empty($_POST['content'])) {
                echo json_encode(array('code'=>-1,'desc'=>'参数不对~'));
                exit;
            }
            if(!isset($_POST['title'])||empty($_POST['title'])) {
                echo json_encode(array('code'=>-1,'desc'=>'参数不对~'));
                exit;
            }
            break;
        case 'queryTasks':
            break;
        default:
            break;
        }
    }

    public function addTask()
    {
        //添加任务
        $task_id = $this->_todoModel->_todo_dao->addTask($_POST['title'],$_POST['content'],$_POST['data_due']);
        echo $task_id;
        exit;
        $this->render('addTaskSuccess',$task_id);
    }

    public function renderAddTaskSuccess($data)
    {
        $todoViewer = ObjectContainer::find('Viewer');
        $todoViewer->task_id = $data;
        $todoViewer->render("todo");
    }

    public function checkPage()
    {
        if(isset($_GET['page'])&&$_GET['page']<1)
        {
            $_GET['page'] = 1;
        }
    }

    public function queryTasks()
    {
        //查询任务
        $data = $this->_todoModel->query($_GET['page']);
        $this->render("queryTasks",$data);
    }

    public function renderQueryTasks($data)
    {
        $task_num_pre_page = ConfigLoader::getServiceConfig("TASK_NUM_PRE_PAGE");
        $total_num = $data['tatal_num'];
        $page_num = intval($total_num/$task_num_pre_page);
        $page_num = ($total_num%$task_num_pre_page)?($page_num+1):$page_num;

        $cur_page = $data['cur_page'];
        if($cur_page==0)
        {
            $page_list_start = 0;
            $page_list_end = -1;
        }else{
            $page_list_start = ($cur_page%10)?(intval($cur_page/10)*10+1):($cur_page-9);
            $page_list_end = $page_list_start+9;
            if($page_list_end>$page_num)
            {
                $page_list_end = $page_num;
            }
        }

        $todoViewer = ObjectContainer::find('Viewer');
        $todoViewer->total_task_num = $total_num;
        $todoViewer->page_num = $page_num;
        $todoViewer->page_list_start = $page_list_start;
        $todoViewer->page_list_end = $page_list_end;
        $todoViewer->data = $data['data'];
        $todoViewer->render("query");
    }

    public function updateTask()
    {
        //更新任务
        $res = $this->_todoModel->_todo_dao->modifiedTask($_POST['title'],$_POST['content'],$_POST['add_time'],"",$_POST['tid']);
        echo $res;
        exit;
    }

    public function deleteTask()
    {
        //删除任务
        echo "deleteTask";
    }

    public function render($action,$data)
    {
        $method = "render".ucfirst($action);
        $this->$method($data);
    }

}

