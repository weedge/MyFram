<?php
class ErrorInfo
{
    static public $post_data_error       = array('code'=>1,   'msg'=>'POST上传的数据解压错误');
    static public $post_data_too_long    = array('code'=>2,   'msg'=>'POST上传数据过大');
    static public $get_data_error        = array('code'=>3,   'msg'=>'GET上传的数据解码错误');
    static public $get_data_empty        = array('code'=>4,   'msg'=>'GET上传的数据为空');
    static public $post_data_empty       = array('code'=>5,   'msg'=>'POST上传的数据为空');
    static public $get_data_too_long     = array('code'=>6,   'msg'=>'GET上传数据过大');
    static public $create_dir_failed     = array('code'=>7,   'msg'=>'创建目录失败');
    static public $write_file_failed     = array('code'=>8,   'msg'=>'写入文件失败');
    static public $no_filter             = array('code'=>9,   'msg'=>'没有这个过滤器');
    static public $no_method             = array('code'=>10,  'msg'=>'没有这个方法');
    static public $bad_param             = array('code'=>11,  'msg'=>'参数错误');
    static public $token_error           = array('code'=>12,  'msg'=>'Token错误');
    static public $decrypt_error         = array('code'=>13,  'msg'=>'DES解密错误');
    static public $json_error            = array('code'=>14,  'msg'=>'解析JSON串错误');
    static public $para_decode_error     = array('code'=>15,  'msg'=>'para解码错误');
    static public $intercept_os_error    = array('code'=>16,  'msg'=>'平台错误');
    static public $intercept_myver_error = array('code'=>17,  'msg'=>'MyVer不合法');
    static public $id_error              = array('code'=>18,  'msg'=>'Id不合法');
    static public $sys_error             = array('code'=>800, 'msg'=>'服务器内部错误');
    static public $decode_error          = array('code'=>19,  'msg'=>'decode数据错误');
}
