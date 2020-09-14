<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');   // 所有都去掉../ 上一级目录，路径的public就可以省略
// 定义图片上传路径
define('IMG_UPLOADS', __DIR__ .'/static/uploads');

// 支付接口路径
define('PAY_PATH', __DIR__ . '/../application/../');

// 定义UEDITOR图片上传路径
define('UEDITOR', __DIR__ .'/ueditor');
define('HTTP_UEDITOR', '/ueditor');
define('DEL_UEDITOR',__DIR__);

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
