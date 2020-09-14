<?php


namespace app\member\model;
use ChuanglanSmsHelper\IpGet;

class User extends BaseModel
{
    // 验证用户名，手机号，邮箱等
    public function getUserInfo($info,$check) {
        return self::where($info,$check)->find();
    }

    // 修改用户密码
    public function updatePwd($where,$phone,$newPwd) {
        return self::where($where, $phone)
                    ->update(['password' => $newPwd]);
    }

    // 新增用户
    public function addASaveUserInfo($data,$id='') {
        return self::save($data,$id);
    }

    // 更改登录时间
    public function saveLoginTime($login_time,$ip,$ipd,$id) {
        return self::save([
            'login_time'  => $login_time,
            'user_ip' => $ip,
            'ip_location' => $ipd
        ],['id' => $id]);
    }

    // 获取登录用户
    public function checkUserInfo($username,$password) {
        return self::where(['username|email|mobile_phone'=>$username,'password'=>$password])->find();
    }

    // 登录
    public function login($data) {
        $username = trim($data['username']);
        $password = md5($data['password'].config('sale'));
        $result = self::checkUserInfo($username,$password);
        if ($result) {
            $ipGet = new IpGet();
            $result['login_time'] = time();
            // IP操作
            $result['user_ip'] = $ipGet->getIP();
            $ipResult = $ipGet->getUserIp($result['user_ip']);
            $result['ip_location'] = $ipResult['result']['area'].$ipResult['result']['location'];
            // 更新登录状态
            self::saveLoginTime($result['login_time'],$result['user_ip'],$result['ip_location'],$result['id']);
            // 插入session
            session('uid',$result['id']);
            session('username',$result['username']);
            // 是否勾选自动登录
            if (isset($data['remember'])) {
                $username = encryption($data['username'],0);    // cookie加密
                $password = encryption($data['password'],0);
                cookie('username',$username,86400,'/');
                cookie('password',$password,86400,'/');
            }
            // 用户登录成功前,把会员价的值写入session中
            $memberLevel = new MemberLevel();
            $points = $memberLevel->getUserLevel($result['points']);
            session('user_level_id',$points['id']);
            session('user_rate',$points['rate']);

            return json(['error'=>0,'message'=>'','url'=>'']);
        } else {
            return json([
                'error'=>1,
                'message'=>"<i class='iconfont icon-minus-sign'></i>用户名或密码错误",
                'url'=>''
            ]);
        }
    }

    // 退出
    public function logout() {
        session(null);
        cookie('username',null);
        cookie('password',null);
        return true;
    }

    // 检查登录状态
    public function checkLog() {
        $uid = session('uid');
        $username = session('username');
        if ($uid || $username) {
            return json(['code' => 0,'uid'=>$uid,'username'=>$username]);
        } else {
            if (cookie('username') && cookie('password')) {
                $data['username'] = encryption(cookie('username'),1);   // cookie解密
                $data['password'] = encryption(cookie('password'),1);
                $loginRes = self::login($data);
                // 如果返回json对象到php,可以传个参数来判断返回的是对象还是新建一个数组返回
                if ($loginRes['error'] == 0) {
                    return json(['code' => 0,'uid'=>$uid,'username'=>$username]);
                }
            }
            return json(['code'=>1]);
        }

    }

}