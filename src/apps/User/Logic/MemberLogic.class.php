<?php

namespace User\Logic;

class MemberLogic {

    function __construct() {
        
    }

    function login($username, $password) {
        $map['mobile'] = $username;
        $map['type'] = 1;
        $account = M('MemberAccount')->where($map)->find();
        if (!$account) {
            return -42;
        }

        $pwd = md5($password . $account['salt']);
        //print_r($account['password']);exit;
        if ($pwd != $account['password']) {
            return -43;
        }
        
        $admin = M('sys_auth_group_access')->where("uid = {$account['uid']}")->find();
        if(!$admin){
            return -42;
        }
        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid' => $account['uid'],
            'username' => ubb_to_emoij($account['nickname'])
        );
//dump($auth);exit;
        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
        
        return $account['uid'];
    }

    function getErrorMessage($error_code = null) {
        $error = $error_code == null ? $this->error : $error_code;
        switch ($error) {
            case -1:
                $error = '用户名长度必须在32个字符以内！';
                break;
            case -2:
                $error = '用户名被禁止注册！';
                break;
            case -3:
                $error = '用户名被占用！';
                break;
            case -4:
                $error = '密码长度必须在6-30个字符之间！';
                break;
            case -41:
                $error = '用户旧密码不正确';
                break;
            case -42:
                $error = '无效的用户账号';
                break;
            case -43:
                $error = '用户密码不正确';
                break;
            case -5:
                $error = '邮箱格式不正确！';
                break;
            case -6:
                $error = '邮箱长度必须在1-32个字符之间！';
                break;
            case -7:
                $error = '邮箱被禁止注册！';
                break;
            case -8:
                $error = '邮箱被占用！';
                break;
            case -9:
                $error = '手机格式不正确！';
                break;
            case -10:
                $error = '手机被禁止注册！';
                break;
            case -11:
                $error = '手机号被占用！';
                break;
            case -12:
                $error = '用户名必须以中文或字母开始，只能包含拼音数字，字母，汉字！';
                break;
            case -31:
                $error = '昵称禁止注册';
                break;
            case -33:
                $error = '昵称长度不合法';
                break;
            case -32:
                $error = '昵称不合法';
                break;
            case -30:
                $error = '昵称已被占用';
                break;

            default:
                $error = '未知错误';
        }
        return $error;
    }

}
