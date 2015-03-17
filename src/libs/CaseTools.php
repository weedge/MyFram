<?php
/**
 * Date: 13-10-11
 * Time: 下午2:49
 * from: case_tools文件
 *
 */

class CaseTools
{
    /**
     * getIP 获取访问者的ip地址
     */
    static function getIP(){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $rm_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
            $ip = explode(",", $rm_addr);
            return $ip[0];
        }
        return (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "0.0.0.0");
    }

    /**
     *
     * 将microtime产生的两部份数据合并，unix时间戳和微秒部份
     */
    static public function dmicrotime() {
        return array_sum(explode(' ', microtime()));
    }

    /**
     *
     * 重写html过滤
     */
    static public function dhtmlspecialchars($string) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = self::dhtmlspecialchars($val);
            }
        } else {
            $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
            if(strpos($string, '&amp;#') !== false) {
                $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
            }
        }
        return $string;
    }

    /**
     *
     * 验证email是否合法
     */
    static public function isemail($email) {
        return strlen($email) > 6 && preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/", $email);
    }

    /**
     *
     * 取随机数
     */
    static public function random($length, $numeric = 0) {
        $seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
        $hash = '';
        $max = strlen($seed) - 1;
        for($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

    /**
     *
     * 将格式2011-11-11的字符串，返回一个日期的 Unix 时间戳。
     * 如果不符合此格式将返回0
     */

    static public function dmktime($date) {
        if(strpos($date, '-')) {
            $time = explode('-', $date);
            return mktime(0, 0, 0, $time[1], $time[2], $time[0]);
        }
        return 0;
    }


    /**
     *
     * 将一个数据转化带有双引号用英文逗号分隔的字符串，多用mysql查询中in方法中
     */
    static public function dimplode($array) {
        if(!empty($array)) {
            return "'".implode("','", is_array($array) ? $array : array($array))."'";
        } else {
            return 0;
        }
    }

    /**
     *
     * 字符串长，一个汉字为2个字符记算
     */
    static public function dstrlen($str) {
        if(strtolower(CHARSET) != 'utf-8') {
            return strlen($str);
        }
        $count = 0;
        for($i = 0; $i < mb_strlen($str); $i++){
            $value = ord($str[$i]);
            if($value > 127) {
                $count++;
                if($value >= 192 && $value <= 223) $i++;
                elseif($value >= 224 && $value <= 239) $i = $i + 2;
                elseif($value >= 240 && $value <= 247) $i = $i + 3;
            }
            $count++;
        }
        return $count;
    }

    /**
     *
     * 截取字符串长，一个汉字为2个字符记算
     */
    static public function cutstr($string, $length, $dot = ' ...') {

        if(self::dstrlen($string) <= $length) {
            return $string;
        }

        $pre = chr(1);
        $end = chr(1);
        $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

        $strcut = '';

        if(strtolower(CHARSET) == 'utf-8') {

            $n = $tn = $noc = 0;
            while($n < strlen($string)) {

                $t = ord($string[$n]);
                if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $tn = 1; $n++; $noc++;
                } elseif(194 <= $t && $t <= 223) {
                    $tn = 2; $n += 2; $noc += 2;
                } elseif(224 <= $t && $t <= 239) {
                    $tn = 3; $n += 3; $noc += 2;
                } elseif(240 <= $t && $t <= 247) {
                    $tn = 4; $n += 4; $noc += 2;
                } elseif(248 <= $t && $t <= 251) {
                    $tn = 5; $n += 5; $noc += 2;
                } elseif($t == 252 || $t == 253) {
                    $tn = 6; $n += 6; $noc += 2;
                } else {
                    $n++;
                }

                if($noc >= $length) {
                    break;
                }

            }
            if($noc > $length) {
                $n -= $tn;
            }

            $strcut = substr($string, 0, $n);

        } else {
            for($i = 0; $i < $length; $i++) {
                $strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
            }
        }

        $strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

        $pos = strrpos($strcut, chr(1));
        if($pos !== false) {
            $strcut = substr($strcut,0,$pos);
        }
        return $strcut.$dot;
    }

    /**
     * utf8编码时截取等长中英文字串
     */
    static public function substrutf8($string,$start,$length,$dot='...'){
        $chars = $string;

        $i=$m=$n=0;

        do{
            if (preg_match ("/[0-9a-zA-Z]/", $chars[$i])){//纯英文
                $m++;
            }else{//非英文字节
                $n++;
            }
            $k = $n/3+$m/2;
            $l = $n/3+$m;//最终截取长度；$l = $n/3+$m*2？
            $i++;
        }while($k < $length);

        $str1 = mb_substr($string,$start,$l,'utf-8');//保证不会出现乱码

        return $str1.$dot;
    }

    static public function dstripslashes($string) {
        if(empty($string)) return $string;
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = self::dstripslashes($val);
            }
        } else {
            $string = stripslashes($string);
        }
        return $string;
    }


    /**
     * 用于创建目录
     *
     */
    static public function dmkdir($dir, $mode = 0755){
        if(!is_dir($dir)) {
            self::dmkdir(dirname($dir));
            @mkdir($dir, $mode);

        }
        return true;
    }

    /**
     * 重写addslashes方法，用于数组
     *
     */
    static public function daddslashes($string, $force = 1) {
        if(is_array($string)) {
            $keys = array_keys($string);
            foreach($keys as $key) {
                $val = $string[$key];
                unset($string[$key]);
                $string[addslashes($key)] = self::daddslashes($val, $force);
            }
        } else {
            $string = addslashes($string);
        }
        return $string;
    }

    /**
     * 检查是否为robot机器人
     *
     */
    static public function checkrobot($useragent = '') {
        static $kw_spiders = array('bot', 'crawl', 'spider' ,'slurp', 'sohu-search', 'lycos', 'robozilla');
        static $kw_browsers = array('msie', 'netscape', 'opera', 'konqueror', 'mozilla');

        $useragent = strtolower(empty($useragent) ? $_SERVER['HTTP_USER_AGENT'] : $useragent);
        if(strpos($useragent, 'http://') === false && self::dstrpos($useragent, $kw_browsers)) return false;
        if(self::dstrpos($useragent, $kw_spiders)) return true;
        return false;
    }

    /**
     *
     * 重写strpos方法，用于搜索的字符存在于数组的某个值中
     * $string:搜索的字符
     * $arr：被搜索的数组
     * $returnvalue : 如果命中搜索是否，是否返回命中的值。默认返回true,false
     * @return string|boolean
     */
    static public function dstrpos($string, &$arr, $returnvalue = false) {
        if(empty($string)) return false;
        foreach((array)$arr as $v) {
            if(strpos($string, $v) !== false) {
                $return = $returnvalue ? $v : true;
                return $return;
            }
        }
        return false;
    }


    /**
     *
     * 检查用户是否用手机访问
     */
    static public function checkmobile() {
        global $_G;
        $mobile = array();
        static $mobilebrowser_list =array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
            'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',
            'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
            'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
            'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
            'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
            'benq', 'haier', '^lct', '320x320', '240x320', '176x220');
        $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if(($v = self::dstrpos($useragent, $mobilebrowser_list, true))) {
            $_G['mobile'] = $v;
            return true;
        }
        $brower = array('mozilla', 'chrome', 'safari', 'opera', 'm3gate', 'winwap', 'openwave', 'myop');
        if(self::dstrpos($useragent, $brower)) return false;

        $_G['mobile'] = 'unknown';
        if($_GET['mobile'] === 'yes') {
            return true;
        } else {
            return false;
        }
    }



    /**
     *
     * 判断某字符是否在该串中存在
     */
    static public function strexists($string, $find) {
        return !(strpos($string, $find) === FALSE);
    }

    static public function stripsearchkey($string) {
        $string = trim($string);
        $string = str_replace('*', '%', addcslashes($string, '%_'));
        $string = str_replace('_', '\_', $string);
        return $string;
    }

    /**
     *
     * 将数组中key和value分成两个数组返回
     */
    static public function renum($array) {
        $newnums = $nums = array();
        foreach ($array as $id => $num) {
            $newnums[$num][] = $id;
            $nums[$num] = $num;
        }
        return array($nums, $newnums);
    }

    /**
     *
     * 将bytes格式化输出，为GB,MB,KB
     */
    static public function sizecount($size) {
        if($size >= 1073741824) {
            $size = round($size / 1073741824 * 100) / 100 . ' GB';
        } elseif($size >= 1048576) {
            $size = round($size / 1048576 * 100) / 100 . ' MB';
        } elseif($size >= 1024) {
            $size = round($size / 1024 * 100) / 100 . ' KB';
        } else {
            $size = $size . ' Bytes';
        }
        return $size;
    }


    static public function strreplace_strip_split($searchs, $replaces, $str) {
        $searchspace = array('((\s*\-\s*)+)', '((\s*\,\s*)+)', '((\s*\|\s*)+)', '((\s*\t\s*)+)', '((\s*_\s*)+)');
        $replacespace = array('-', ',', '|', ' ', '_');
        return trim(preg_replace($searchspace, $replacespace, str_replace($searchs, $replaces, $str)), ' ,-|_');
    }

    /**
     *
     * 判断目录是否有写权限
     */
    static public function dir_writeable($dir) {
        if(!is_dir($dir)) {
            @mkdir($dir, 0755);
        }
        if(is_dir($dir)) {
            if($fp = @fopen("$dir/test.txt", 'w')) {
                @fclose($fp);
                @unlink("$dir/test.txt");
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        }
        return $writeable;
    }

    /**
     *
     * 通过header跳转
     */
    static public function headerLocation($url) {
        ob_end_clean();
        ob_start();
        @header('location: '.$url);
        exit;
    }

    /**
     *
     * 发送email的html标签
     */
    static public function parseemail($email, $text) {
        $text = str_replace('\"', '"', $text);
        if(!$email && preg_match("/\s*([a-z0-9\-_.+]+)@([a-z0-9\-_]+[.][a-z0-9\-_.]+)\s*/i", $text, $matches)) {
            $email = trim($matches[0]);
            return '<a href="mailto:'.$email.'">'.$email.'</a>';
        } else {
            return '<a href="mailto:'.substr($email, 1).'">'.$text.'</a>';
        }
    }

    /**
     *
     * 是否为火狐浏览器
     */
    static public function ismozilla() {
        $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if(strpos($useragent, 'gecko') !== FALSE) {
            preg_match("/gecko\/(\d+)/", $useragent, $regs);
            return $regs[1];
        }
        return FALSE;
    }

    /**
     * 类型转化
     *
     * **/
    static public function dintval($var){

        $result = abs(intval($var));
        if ($result)
            return $result;
        else
            return 0;
    }

    /**
     * 检查用户phone号码是否正确
     *
     **/
    static public function isphone($phone){
        if(!preg_match('/^1[3|4|5|8]\d{9}$/', $phone)){
            return false;
        }else{
            return true;
        }
    }
    /**
     * 反解js的escape
     *
     **/
    static public function js_unescape($str)
    {
        $ret = '';
        $len = strlen($str);

        for ($i = 0; $i < $len; $i++)
        {
            if ($str[$i] == '%' && $str[$i+1] == 'u')
            {
                $val = hexdec(substr($str, $i+2, 4));

                if ($val < 0x7f) $ret .= chr($val);
                else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
                else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));

                $i += 5;
            }
            else if ($str[$i] == '%')
            {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            }
            else $ret .= $str[$i];
        }
        return $ret;
    }


    //获取ip数
    static public function getIpNum($ip)
    {
        $info 		= array();
        $ip_array 	= explode('.', $ip);

        //将ip转化为一个字符串，转化公式为：a.b.c.d = 256*(c+256*(b+256*a))+d
        $n_ip 		= 256*($ip_array[2]+256*($ip_array[1]+256*$ip_array[0]))+$ip_array[3]-1;
        return $n_ip;

    }

    //十六进制颜色转换为rgb颜色值
    static public function Hex2RGB($hexColor){
        $color = str_replace('#', '', $hexColor);
        if (strlen($color) > 3){
            $rgb = array(
                r => hexdec(substr($color, 0, 2)),
                g => hexdec(substr($color, 2, 2)),
                b => hexdec(substr($color, 4, 2))
            );
        }else{
            $color = str_replace('#', '', $hexColor);
            $r = substr($color, 0, 1) . substr($color, 0, 1);
            $g = substr($color, 1, 1) . substr($color, 1, 1);
            $b = substr($color, 2, 1) . substr($color, 2, 1);
            $rgb = array(
                r => hexdec($r),
                g => hexdec($g),
                b => hexdec($b)
            );
        }
        return $rgb;
    }
}
