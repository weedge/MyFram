<?php
/**
 * Date: 13-9-23
 * Time: 下午6:04
 * Todo:  AOP
 */

class AppController {

    private $before_filter = array();
    private $after_filter  = array();

    protected function before_filter($filter, $condition = array())
    {
        $this->set_filter("before_filter", $filter, $condition);
    }

    protected function after_filter($filter, $condition = array())
    {
        $this->set_filter("after_filter", $filter, $condition);
    }

    private function set_filter($var, $filter, $condition)
    {
        if (!is_callable(array($this, $filter)))
        {
             throw new BizException(ErrorInfo::$no_filter);
        }
        $this->{$var}[$filter] = $condition;
    }

    private function check_filter($method, $condition)
    {
        return  in_array($method, $condition);
    }

    public function run($method)
    {
        if(!is_callable(array($this, $method)))
        {
            throw new BizException(ErrorInfo::$no_method);
        }

        // 校验前置过滤
        foreach($this->before_filter as $filter=> $condition )
        {
            if (!$this->check_filter($method, $condition))
            {
                continue;
            }
            $this->$filter();
        }
        $this->$method();
        foreach($this->after_filter as $filter => $condition)
        {
            if (!$this->check_filter($method, $condition))
            {
                continue;
            }
            $this->$filter();
        }

    }


}
