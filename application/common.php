<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use PHPMailer\PHPMailer\PHPMailer;
// 应用公共文件

// cookie  type = 0 加密  type = 1 解密
function encryption($value,$type=0) {
    $key = config('sale');
    if ($type == 0) {
        return str_replace('=','',base64_encode($value ^ $key));
    } else {
        $value = base64_decode($value);
        return $value ^ $key;
    }
}

/**
 * 截取指定长度的字符
 * @param type $string  内容
 * @param type $start 开始
 * @param type $length 长度
 * @return type
 */
function ds_substing($string, $start=0,$length=80) {
    $string = strip_tags($string);
    $string = preg_replace('/\s/', '', $string);
    return mb_substr($string, $start, $length);
}

/**
 * php显示指定长度的字符串，超出长度以省略号(...)填补尾部显示
 * @ str 字符串
 * @ len 指定长度
 **/
function cutSubstr($str,$len=30){
    if (strlen($str)>$len) {
        $str=substr($str,0,$len) . '...';
    }
    return $str;
}

function sendMail($to, $title, $content) {
    $mail = new PHPMailer();
    // 设置为要发邮件
    $mail->isSMTP();
    // 是否允许发送HTML代码作为邮件的内容
    $mail->isHTML(TRUE);
    $mail->CharSet="UTF-8";
    // 是否需要身份验证
    $mail->SMTPAuth=TRUE;
    /* 邮件服务器上的账号是什么 -> 到163注册一个账号即可 */
    $mail->From = "jock0715@163.com";   // 发送邮件人账号
    $mail->FromName = "只淘商城";       // 发送邮件人名字
    $mail->Host = 'smtp.163.com';
    $mail->Username = 'jock0715@163.com';
    $mail->Password = 'ASPVOVTLDKDVIBRO';

    // 发邮件端口默认25
    $mail->Port = 25;
    // 网站上线默认关闭25端口，需要加上 下面两句
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    // 收件人
    $mail->addAddress($to);
    $mail->Subject = $title;
    $mail->Body = $content;
    return ($mail->send());
}

// 图片资源处理函数
function my_scandir($dir=UEDITOR) {
    $files = [];
    // scandir 获取当前目录和目录下的文件
    $dir_list = scandir($dir);

    foreach ($dir_list as $file) {
        if ($file != '.' && $file != '..') {
            if (is_dir($dir.DS.$file)) {
                $files[$file] = my_scandir($dir.DS.$file);
            } else {
                $files[] = $dir . DS . $file;
            }
        }
    }
    return $files;
}
