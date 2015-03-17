<?php
/**
 * User: wy
 * Date: 13-10-11
 * Time: 下午6:32
 * Todo: db config, 主从未在db代理层进行处理，直接在程序中处理了。
 */

return array(
    //专题库
    'TEST_SUBJECT' => array(//test
        'host' => '10.11.215.63',
        'port' => '3306',
        'user' => 'dhuser',
        'pwd' => 'dhdev123',
        'db' => 'dh123',
    ),

    'SDB_SUBJECT' => array(
        'host' => array_rand(array('' => 1, '' => 1)),
        'port' => '3306',
        'user' => 'subjectdev_s',
        'pwd' => 'H09s99k9pWoatZ1M=',
        'db' => 'subject',
    ),

    'MDB_SUBJECT' => array(
        'host' => '',
        'port' => '3306',
        'user' => 'research_s',
        'pwd' => 'V9ZG8_5pp8%7_BBq1$',
        'db' => 'research_test',
    ),

    //走dbproxy
    'PROXY_DB' => array(//online
        'host' => '',
        'port' => '3306',
        'user' => 'research_s',
        'pwd' => 'V9ZG8_5pp8%7_BBq1$',
        'db' => 'research_test',
    )

);
