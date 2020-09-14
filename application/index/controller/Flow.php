<?php
namespace app\index\controller;
use app\index\model\Cart as CartModel;
use app\index\model\Address as AddressModel;
use app\index\model\Order as OrderModel;
use app\index\model\OrderGoods as OrderGoodsModel;

class Flow extends Base
{

    // 商品详情页调用 common.js
    public function addToCart() {
        $data = input('post.');
        $goodsObj = json_decode($data['goods']);
        $goodsID = $goodsObj->goods_id;
        $goodsAttr = $goodsObj->goods_attr;
        $goodsNum = $goodsObj->number;
        $cart = new CartModel();
        $cart->addToCart($goodsID,$goodsAttr,$goodsNum);

        return json(['error'=>0,'one_step_buy'=>'1']);
    }

    public function flow1() {
        $cart = new CartModel();
        $cartGoodsRes = $cart->getGoodsListInCart();

        $this->assign([
            'cartGoodsRes' => $cartGoodsRes,
        ]);

        return view();
    }

    // flow1页面实时显示价格
    public function AjaxCartGoodsAmount() {
        // 传入flow.html中的js文件，自动加载，获取rec_id 20-3,6@20-4,5
        $recID = input('rec_id');
        $cart = new CartModel();
        $result = $cart->AjaxCartGoodsAmount($recID);
        return json($result);
    }

    public function dropGoodsAttr($id) {
        $cart = new CartModel();
        $cart->delCart($id);
        $this->redirect('index/Flow/flow1');
    }

    // js已经获取到商品属性id字符串
    public function deleteCarts() {
        // 批量删除获取的值 cart_value: 7-39,34@7-33,35
        $cartValue = input('cart_value');
        $cart = new CartModel();
        $cart->delCarts($cartValue);
        return json(['status' => 1]);
    }

    // 修改商品数量
    public function updateCartNum() {
        $cart = new CartModel();
        $recID = input('rec_id');
        $goodsNum = input('goods_number');

        $cart->updateCart($recID,$goodsNum);
        return json(['error'=>0,'rec_id'=>$recID, 'goods_number'=>$goodsNum]);

    }

    // Ajax请求，sum购物车商品数量,header
    public function cartGoodsNum() {
        // 判断是否已经设置了cookie，是，便把数据反序列为数组，否则给他一个新数组
        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        $goodsNum = 0;
        foreach ($cart as $k => $v) {
            $goodsNum += $v;
        }
        // 返回到check-log.js
        return json(['cartGoodsNum'=>$goodsNum]);
    }

    // 点击去支付，未登录弹出登录窗口
    public function loginDailog() {
        $backAct = input('back_act','');    // "flow.php"
        $ajaxLoginUrl = url('member/Account/login');
        $content="<div class=\"login-wrap\">\n    \n    <div class=\"login-form\">\n    \t    \t<div class=\"coagent\">\n            <div class=\"tit\"><h3>用第三方账号直接登录<\/h3><span><\/span><\/div>\n            <div class=\"coagent-warp\">\n            \t                                    <a href=\"user.php?act=oath&type=qq&user_callblock=flow.php\" class=\"qq\"><b class=\"third-party-icon qq-icon\"><\/b><\/a>\n                                            <\/div>\n        <\/div>\n                <div class=\"login-box\">\n            <div class=\"tit\"><h3>账号登录<\/h3><span><\/span><\/div>\n            <div class=\"msg-wrap\"><\/div>\n            <div class=\"form\">\n            \t<form name=\"formLogin\" action=\"user.php\" method=\"post\" onSubmit=\"userLogin();return false;\">\n                \t<div class=\"item\">\n                        <div class=\"item-info\">\n                            <i class=\"iconfont icon-name\"><\/i>\n                            <input type=\"text\" id=\"loginname\" name=\"username\" class=\"text\" value=\"\" placeholder=\"请输入账号\" \/>\n                        <\/div>\n                    <\/div>\n                    <div class=\"item\">\n                        <div class=\"item-info\">\n                            <i class=\"iconfont icon-password\"><\/i>\n                            <input type=\"password\"   style=\"display:none\"\/>\n                            <input type=\"password\" id=\"nloginpwd\" name=\"password\" value=\"\" class=\"text\" placeholder=\"请输入密码\" \/>\n                        <\/div>\n                    <\/div>\n                                        <div class=\"item\">\n                        <input id=\"remember\" name=\"remember\" type=\"checkbox\" class=\"ui-checkbox\">\n                        <label for=\"remember\" class=\"ui-label\">请保存我这次的登录信息。<\/label>\n                    <\/div>\n                    <div class=\"item item-button\">\n                    \t<input type=\"hidden\" name=\"dsc_token\" value=\"c125d234e15ffb1a86841a60e23a2991\" \/>\n                        <input type=\"hidden\" name=\"act\" value=\"act_login\" \/>\n                        <input type=\"hidden\" name=\"back_act\" value=\"".$backAct."\" \/>\n                        <input type=\"submit\" name=\"submit\" value=\"登&nbsp;&nbsp;录\" class=\"btn sc-redBg-btn\" \/>\n                    <\/div>\n                    <div class=\"lie\">\n                    \t<a href=\"user.php?act=get_password\" class=\"notpwd gary fl\" target=\"_blank\">忘记密码？<\/a>\n                    \t<a href=\"user.php?act=register\" class=\"notpwd red fr\" target=\"_blank\">免费注册<\/a>                    <\/div>\n                <\/form>\n            <\/div>\n    \t<\/div>        \n    <\/div>\n    <script type=\"text\/javascript\">\n\t\tvar username_empty=\"<i><\/i>\u8bf7\u8f93\u5165\u7528\u6237\u540d\";\n    \tvar username_shorter=\"<i><\/i>\u7528\u6237\u540d\u957f\u5ea6\u4e0d\u80fd\u5c11\u4e8e 4 \u4e2a\u5b57\u7b26\u3002\";\n    \tvar username_invalid=\"<i><\/i>\u7528\u6237\u540d\u53ea\u80fd\u662f\u7531\u5b57\u6bcd\u6570\u5b57\u4ee5\u53ca\u4e0b\u5212\u7ebf\u7ec4\u6210\u3002\";\n    \tvar password_empty=\"<i><\/i>\u8bf7\u8f93\u5165\u5bc6\u7801\";\n    \tvar password_shorter=\"<i><\/i>\u767b\u5f55\u5bc6\u7801\u4e0d\u80fd\u5c11\u4e8e 6 \u4e2a\u5b57\u7b26\u3002\";\n    \tvar confirm_password_invalid=\"<i><\/i>\u4e24\u6b21\u8f93\u5165\u5bc6\u7801\u4e0d\u4e00\u81f4\";\n    \tvar captcha_empty=\"<i><\/i>\u8bf7\u8f93\u5165\u9a8c\u8bc1\u7801\";\n    \tvar email_empty=\"<i><\/i>Email \u4e3a\u7a7a\";\n    \tvar email_invalid=\"<i><\/i>Email \u4e0d\u662f\u5408\u6cd5\u7684\u5730\u5740\";\n    \tvar agreement=\"<i><\/i>\u60a8\u6ca1\u6709\u63a5\u53d7\u534f\u8bae\";\n    \tvar msn_invalid=\"<i><\/i>msn\u5730\u5740\u4e0d\u662f\u4e00\u4e2a\u6709\u6548\u7684\u90ae\u4ef6\u5730\u5740\";\n    \tvar qq_invalid=\"<i><\/i>QQ\u53f7\u7801\u4e0d\u662f\u4e00\u4e2a\u6709\u6548\u7684\u53f7\u7801\";\n    \tvar home_phone_invalid=\"<i><\/i>\u5bb6\u5ead\u7535\u8bdd\u4e0d\u662f\u4e00\u4e2a\u6709\u6548\u53f7\u7801\";\n    \tvar office_phone_invalid=\"<i><\/i>\u529e\u516c\u7535\u8bdd\u4e0d\u662f\u4e00\u4e2a\u6709\u6548\u53f7\u7801\";\n    \tvar mobile_phone_invalid=\"<i><\/i>\u624b\u673a\u53f7\u7801\u4e0d\u662f\u4e00\u4e2a\u6709\u6548\u53f7\u7801\";\n    \tvar msg_un_blank=\"<i><\/i>\u7528\u6237\u540d\u4e0d\u80fd\u4e3a\u7a7a\";\n    \tvar msg_un_length=\"<i><\/i>\u7528\u6237\u540d\u6700\u957f\u4e0d\u5f97\u8d85\u8fc715\u4e2a\u5b57\u7b26\uff0c\u4e00\u4e2a\u6c49\u5b57\u7b49\u4e8e2\u4e2a\u5b57\u7b26\";\n    \tvar msg_un_format=\"<i><\/i>\u7528\u6237\u540d\u542b\u6709\u975e\u6cd5\u5b57\u7b26\";\n    \tvar msg_un_registered=\"<i><\/i>\u7528\u6237\u540d\u5df2\u7ecf\u5b58\u5728,\u8bf7\u91cd\u65b0\u8f93\u5165\";\n    \tvar msg_can_rg=\"<i><\/i>\u53ef\u4ee5\u6ce8\u518c\";\n    \tvar msg_email_blank=\"<i><\/i>\u90ae\u4ef6\u5730\u5740\u4e0d\u80fd\u4e3a\u7a7a\";\n    \tvar msg_email_registered=\"<i><\/i>\u90ae\u7bb1\u5df2\u5b58\u5728,\u8bf7\u91cd\u65b0\u8f93\u5165\";\n    \tvar msg_email_format=\"<i><\/i>\u683c\u5f0f\u9519\u8bef\uff0c\u8bf7\u8f93\u5165\u6b63\u786e\u7684\u90ae\u7bb1\u5730\u5740\";\n    \tvar msg_blank=\"<i><\/i>\u4e0d\u80fd\u4e3a\u7a7a\";\n    \tvar no_select_question=\"<i><\/i>\u60a8\u6ca1\u6709\u5b8c\u6210\u5bc6\u7801\u63d0\u793a\u95ee\u9898\u7684\u64cd\u4f5c\";\n    \tvar passwd_balnk=\"<i><\/i>\u5bc6\u7801\u4e2d\u4e0d\u80fd\u5305\u542b\u7a7a\u683c\";\n    \tvar msg_phone_blank=\"<i><\/i>\u624b\u673a\u53f7\u7801\u4e0d\u80fd\u4e3a\u7a7a\";\n    \tvar msg_phone_registered=\"<i><\/i>\u624b\u673a\u5df2\u5b58\u5728,\u8bf7\u91cd\u65b0\u8f93\u5165\";\n    \tvar msg_phone_invalid=\"<i><\/i>\u65e0\u6548\u7684\u624b\u673a\u53f7\u7801\";\n    \tvar msg_phone_not_correct=\"<i><\/i>\u624b\u673a\u53f7\u7801\u4e0d\u6b63\u786e\uff0c\u8bf7\u91cd\u65b0\u8f93\u5165\";\n    \tvar msg_mobile_code_blank=\"<i><\/i>\u624b\u673a\u9a8c\u8bc1\u7801\u4e0d\u80fd\u4e3a\u7a7a\";\n    \tvar msg_mobile_code_not_correct=\"<i><\/i>\u624b\u673a\u9a8c\u8bc1\u7801\u4e0d\u6b63\u786e\";\n    \tvar msg_confirm_pwd_blank=\"<i><\/i>\u786e\u8ba4\u5bc6\u7801\u4e0d\u80fd\u4e3a\u7a7a\";\n    \tvar msg_identifying_code=\"<i><\/i>\u9a8c\u8bc1\u7801\u4e0d\u80fd\u4e3a\u7a7a\";\n    \tvar msg_identifying_not_correct=\"<i><\/i>\u9a8c\u8bc1\u7801\u4e0d\u6b63\u786e\";\n    \t\t\/* *\n\t\t * \u4f1a\u5458\u767b\u5f55\n\t\t*\/ \n\t\tfunction userLogin()\n\t\t{\n\t\t\tvar frm = $(\"form[name='formLogin']\");\n\t\t\tvar username = frm.find(\"input[name='username']\");\n\t\t\tvar password = frm.find(\"input[name='password']\");\n\t\t\tvar captcha = frm.find(\"input[name='captcha']\");\n\t\t\tvar dsc_token = frm.find(\"input[name='dsc_token']\");\n\t\t\tvar error = frm.find(\".msg-error\");\n\t\t\tvar msg = '';\n\t\t\t\n\t\t\tif(username.val()==\"\"){\n\t\t\t\terror.show();\n\t\t\t\tusername.parents(\".item\").addClass(\"item-error\");\n\t\t\t\tmsg += username_empty;\n\t\t\t\tshowMesInfo(msg);\n\t\t\t\treturn false;\n\t\t\t}\n\t\t\t\n\t\t\tif(password.val()==\"\"){\n\t\t\t\terror.show();\n\t\t\t\tpassword.parents(\".item\").addClass(\"item-error\");\n\t\t\t\tmsg += password_empty;\n\t\t\t\tshowMesInfo(msg);\n\t\t\t\treturn false;\n\t\t\t}\n\t\t\t\n\t\t\tif(captcha.val()==\"\"){\n\t\t\t\terror.show();\n\t\t\t\tcaptcha.parents(\".item\").addClass(\"item-error\");\n\t\t\t\tmsg += captcha_empty;\n\t\t\t\tshowMesInfo(msg);\n\t\t\t\treturn false;\n\t\t\t}\n\t\t\tvar back_act=frm.find(\"input[name='back_act']\").val();\n\t\t\t\n\t\t\t\t\t\t\tAjax.call( '".$ajaxLoginUrl."', 'username=' + username.val()+'&password='+password.val()+'&dsc_token='+dsc_token.val()+'&captcha='+captcha.val()+'&back_act='+back_act, return_login , 'POST', 'JSON');\n\t\t\t\t\t}\n\t\t\n\t\tfunction return_login(result)\n\t\t{\n\t\t\tif(result.error>0)\n\t\t\t{\n\t\t\t\tshowMesInfo(result.message);\t\n\t\t\t}\n\t\t\telse\n\t\t\t{\n\t\t\t\tif(result.ucdata){\n\t\t\t\t\t$(\"body\").append(result.ucdata)\n\t\t\t\t}\n\t\t\t\tlocation.href=result.url;\n\t\t\t}\n\t\t}\n\t\t\n\t\tfunction showMesInfo(msg) {\n\t\t\t$('.login-wrap .msg-wrap').empty();\n\t\t\tvar info = '<div class=\"msg-error\"><b><\/b>' + msg + '<\/div>';\n\t\t\t$('.login-wrap .msg-wrap').append(info);\n\t\t}\n\t<\/script>\n<\/div>\n";
        $content=stripcslashes($content);
        return json(["error"=>0,"message"=>"","content"=>$content]);
    }

    // 显示收件人信息，准备下单
    public function flow2() {
        $cartValue = input('cart_value');
        $cart = new CartModel();
        // 传入返回过滤后的rec_id：20-1,3,4 ... 获取cookie对应键的商品数据
        $cartGoodsRes = $cart->getGoodsListInCart($cartValue);

        // 商品的各种金额
        $goodsAmount = $cart->AjaxCartGoodsAmount($cartValue);

        // 查询下单用户是否存在地址
        $uid = session('uid');
        $address = new AddressModel();
        $uAddress = $address->getUserAddress($uid);

        $this->assign([
            'cartGoodsRes' => $cartGoodsRes,
            'goodsAmount'  => $goodsAmount,
            'uAddress'  => $uAddress,
            'cartValue' => $cartValue,
        ]);
        return view();
    }

    // flow3
    public function flow3() {
        $data = input('post.');
        // 每次下单都修改为最新的地址
        $uid = session('uid');
        $address = new AddressModel();
        $uAddress = $address->getUserAddress($uid);
        echo $uid;
        if ($uAddress) {
            $address->updateAddress($data,$uid);
        } else {
            $address->saveAddress($data);
        }

        // 处理订单基本信息表
        $order = new OrderModel();
        // 获取选择下单的商品属性字符串id 20-1,3,6@14-2,3
        $recId = $data['rec_id'];
        $cart = new CartModel();

        // 传入返回过滤后的rec_id：20-1,3,4 ... 获取cookie对应键的商品数据
        $cartGoods = $cart->getGoodsListInCart($recId);

        // 添加待支付订单信息
        $orderID = $order->addOrder($data,$uid,$recId);

        //处理对应订单id中商品的基本信息表
        $orderGoods = new OrderGoodsModel();
        foreach ($cartGoods as $k => $v) {
            $orderGoods->addOrderGoods($orderID,$v);
            // 删除对应已下单的商品coolie
            $cart->delCart($recId);
        }
        $this->success('下单成功',url('index/Flow/flow4',array('oid'=>$orderID)));
    }

    public function flow4() {

        $orderID = input('oid');
        $order = new OrderModel();
        $orderData = $order->getOrder($orderID);

        // 跳转到支付界面，需要配置好用户ID，key
        if($orderData['payment'] == 1 && $orderData['pay_status'] == 0) {
            include(PAY_PATH.'pay/alipay/alipayapi.php');
            $payBtn = $html_text;
            $this->assign([
                'payBtn' => $payBtn,
            ]);
        }

        $this->assign([
            'orderData' => $orderData,
        ]);
        return view();
    }

    // 支付后，显示支付成功回调界面
    public function paysuccess() {
        // 回调成功，url上面会有一长串参数，获取
        // $arr = input('get.');
        // $outTradeNo = $arr['out_trade_no'];

        $outTradeNo =  '1597976922412616';
        // 通过订单号，获取订单信息
        $orderInfo = db('order')->where('out_trade_no',$outTradeNo)->find();
        $this->assign([
            'orderInfo' => $orderInfo,
        ]);
        return view();
    }

    // 跳转支付成功回调界面时，修改支付状态
    public function alipayNotify() {
        $order = new OrderModel();
        // 引入回调成功，执行的函数
        include(PAY_PATH.'pay/alipay/notify_url.php');
    }

    // flow4传过来订单号，显示微信二维码支付
    public function wxCode($outTradeNo) {
        // 通过订单号获取订单总价
        $price = db('order')->where('out_trade_no',$outTradeNo)
                                  ->value('order_total_price');
        // 微信单位是分
        $price = $price * 100;
        $wxPay = PAY_PATH.'pay/wxpay/';
        include($wxPay.'index2.php');
        $obj = new \WeiXinPay2();
        // 传递生成二维码需要的参数
        $qrurl = $obj->getQrUrl($outTradeNo,$price);

        //2.调用QRcode类生成二维码
        \QRcode::png($qrurl);
    }
    
    // 调用微信回调
    public function wxSuccess() {
        $wxPay = PAY_PATH.'pay/wxpay/';
        include($wxPay.'notify.php');
    }
    
    // 检测订单状态，更改图片
    public function checkOrderStatus() {
        $outTradeNo = input('out_trade_no');
        $payStatus = db('order')->where('out_trade_no',$outTradeNo)
            ->value('pay_status');
        return json(['pay_status'=>$payStatus]);
    }
    
    

}
