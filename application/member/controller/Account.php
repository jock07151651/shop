<?php
namespace app\member\controller;

use ChuanglanSmsHelper\JuHeSmsApi;
use app\member\model\User as UserModel;


class Account extends Base
{
    // 用户登录
    public function login() {

        if (request()->isPost()) {
            $data = input('post.');
            $user = new UserModel();
            return $user->login($data);
        }
        return view();
    }

    // 检查登录状态
    public function checkLogin() {
        $user = new UserModel();
        return $user->checkLog();
    }

    // 退出登录
    public function logoutA() {
        $user = new UserModel();
        $result = $user->logout();
        if ($result) {
            return json(['code' => 0, 'msg' => '注销登录成功！']);
        }
        return json(['code' => 0, 'msg' => '注销登录失败！']);
    }

    // 注册
    public function reg() {
        if (request()->isPost()) {

            $data = input('post.');
            $users['username'] = trim($data['username']);
            $users['password'] = md5($data['password'].config('sale'));
            $users['email'] = $data['email'];
            $users['mobile_phone'] = $data['mobile_phone'];
            $users['register_type'] = $data['register_type'];
            $users['create_time'] = time();

            $validate = validate('User');
            if (!$validate->check($data)) {
                $this->success($validate->getError());
            }
            $user = new UserModel();
            $result = $user->addASaveUserInfo($users);
            if ($result) {
                self::login();
                $this->success('注册成功!','member/user/index');
            } else {
                $this->error('注册失败!');
            }
        }
        return view();
    }

    /**
     * 发送短信
     * @type 0 注册，1找回密码
     */
    public function sendMsg($type=0) {
        // 接收用户填写的手机号
        $mobile = input('phone');
        $juheApi = new JuHeSmsApi();
        $code = mt_rand(100000,999999);
        $content = $juheApi->sendSMS($mobile,$code);

        if($content){
            $result = json_decode($content,true);
            $error_code = $result['error_code'];
            if($error_code == 0){
                //状态为0，说明短信发送成功
                // echo "短信发送成功,短信ID：".$result['result']['sid'];
                if ($type == 0) {
                    session('phoneCode',$code);
                } else {
                    session('newPhoneCode',$code);
                    session('newPhone',$mobile);
                }

                return json(['code'=>0,'msg'=>'发送成功!']);
            }else{
                //状态非0，说明失败
                // $msg = $result['reason'];
                // echo "短信发送失败(".$error_code.")：".$msg;
                return json(['code'=>1,'msg'=>'发送失败!']);
            }
        }else{
            //返回内容异常，以下可根据业务逻辑自行修改
            return json(['code'=>2,'msg'=>'内部失败!']);
        }
    }

    /*
     * 发送邮件信息
     * $_pwd 发送过来修改密码当作code发给用户
     */
    public function sendEmail($_pwd='') {
        $email = input('email');
        $to = $email;
        $title = '只淘商城';
        $code = mt_rand(111111,999999);
        if ($_pwd) {
            $content = '您的重置后的密码是：'.$_pwd.' 请妥善保管.';
        } else {
            $content = '您的验证码是：'.$code;
        }

        // 调用公共类中的邮件API
        $result = sendMail($to, $title, $content);

        if($result){
            // 邮箱验证码设置为session
            session('emailCode',$code);
            return json(['code'=>0,'msg'=>'发送成功!']);
        }else{
            return json(['code'=>1,'msg'=>'发送失败!']);
        }
    }

    /**
     * 验证用户名
     */
    public function checkUser() {
        if (request()->isAjax()) {
            $username = input('username');
            $user = new UserModel();
            $result = $user->getUserInfo('username',$username);
            if (!$result) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * 验证手机号
     */
    public function checkPhone() {
        if (request()->isAjax()) {
            $mobile = input('mobile_phone');
            $user = new UserModel();
            $result = $user->getUserInfo('mobile_phone',$mobile);
            if (!$result) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * 验证手机code
     */
    public function checkPhoneCode() {
        if (request()->isAjax()) {
            $phoneSendCode = input('mobile_code');
            if ($phoneSendCode == session('phoneCode')) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * 验证邮箱
     */
    public function checkEmail() {
        if (request()->isAjax()) {
            $email = input('email');
            $user = new UserModel();
            $result = $user->getUserInfo('email',$email);
            if (!$result) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * 验证邮箱code
     */
    public function checkEmailCode() {
        if (request()->isAjax()) {
            $emailSendCode = input('send_code');
            if ($emailSendCode == session('emailCode')) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    // 找回密码
    public function get_password() {

        return view();
    }

    // ajax点击获取验证码,判断找回密码的手机号,正确生成session短信验证码
    public function checkPhoneMsg() {
        $phone = input('phone');
        if (trim($phone)) {
            $user = new UserModel();
            $result = $user->getUserInfo('mobile_phone',$phone);
            if ($result) {
                self::sendMsg(1);
                return json(['code' => 0,'msg' => ""]);
            } else {
                return json(['code' => 1,
                    'msg' => "<i class='iconfont icon-minus-sign' style='color: #f42424'></i>手机号不存在"
                ]);
            }

        } else {
            return json(['code' => 2,
                'msg' => "<i class='iconfont icon-minus-sign' style='color: #f42424'></i>手机号不能为空"
            ]);
        }
    }

    // 验证用户输入修改密码的验证码与session保存的验证码是否一致
    public function checkNewPhoneCode() {
        $mobileCode = trim(input('mobile_code'));
        $newPhone = session('newPhone');
        $newCode = session('newPhoneCode');
        $newPwd = md5('123456789'.config('sale'));
        if ($mobileCode == $newCode) {
            $user = new UserModel();
            $result = $user->updatePwd('mobile_phone',$newPhone,$newPwd);
            if ($result !== false) {
                return json([
                    'code' => 0,
                    'msg'=>"<i class='iconfont icon-minus-sign' style='color: #f42424'></i>你的密码已重置为123456789"
                ]);
            } else {
                return json([
                    'code' => 1,
                    'msg'=>"<i class='iconfont icon-minus-sign' style='color: #f42424'></i>密码重置失败"
                ]);
            }
        } else {
            return false;
        }
    }

    // 邮件修改密码
    public function checkPwdEmail() {
        $data = input('post.');
        $userInfo['username'] = $data['user_name'];
        $userInfo['email'] = $data['email'];
        $_pwd = mt_rand(111111,999999);
        $pwd = md5($_pwd.config('sale'));
        $user = new UserModel();
        $result = $user->getUserInfo('username',$userInfo['username']);
        if ($result) {
            if ($result['email'] == $userInfo['email']) {
                $updatePwd = $user->updatePwd('username',$userInfo['username'],$pwd);
                if ($updatePwd !== false) {
                    self::sendEmail($_pwd);
                    $res['code'] = 0;
                    $res['msg'] = '重置密码成功,请前往邮箱查看.';
                } else {
                    $res['code'] = 1;
                    $res['msg'] = '系统错误';
                }

            } else {
                $res['code'] = 2;
                $res['msg'] = '您填写的用户名或邮箱地址错误email';
            }
        } else {
            $res['code'] = 3;
            $res['msg'] = '您填写的用户名或邮箱地址错误userEmpty';
        }
        $this->assign([
            'show_1200' => 1,
            'code' => $res['code'],
            'msg' => $res['msg'],
        ]);
        return view('index@common/tip_info');
    }
}