<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

// 文章栏目
Route::rule('cate/:cateID','Cate/index','get',['ext'=>'html|htm'],['id'=>'\d{1,9}']);
// 文章列表
Route::rule('article/:artID','Article/index','get',['ext'=>'html|htm'],['id'=>'\d{1,9}']);
// 商品栏目
Route::rule('category','Category/index','get',['ext'=>'html|htm'],['id'=>'\d{1,9}']);
// 商品栏目
Route::rule('goods/:goodsID','index/Goods/index','get',['ext'=>'html|htm'],['id'=>'\d{1,9}']);

// 商品详情页
Route::rule('goods','index/Goods/index','post|get');

// 订单页
Route::rule('flow1','index/Flow/flow1','post|get');
// 填写收件信息，准备下单
Route::rule('flow2','index/Flow/flow2','post|get');
// 下单成功，准备支付
Route::rule('flow4/:oid','index/Flow/flow4','post|get');

// 支付宝
// 支付成功
//Route::rule('pay_success/:outTradeNo','index/Flow/paysuccess','post|get');
// 支付成功
Route::rule('pay_success','index/Flow/paysuccess','post|get');
// 支付成功并修改商品支付状态
Route::rule('alipay_notify','index/Flow/alipayNotify','post|get');


// 微信
// 调起微信二维码准备付款
Route::rule('wx_code/:outTradeNo','index/Flow/wxCode','post|get');
// 支付成功修改订单状态
Route::rule('wx_success','index/Flow/wxSuccess','post|get');



// 登录
Route::rule('login','member/Account/login','post|get');
// 注册
Route::rule('reg','member/Account/reg','post|get');
// 找回密码
Route::rule('get_password','member/Account/get_password','post|get');