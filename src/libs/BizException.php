<?php
class BizException extends Exception
{
    function __construct($error_info)
    {
        parent::__construct($error_info['msg'], $error_info['code']);
    }
}
