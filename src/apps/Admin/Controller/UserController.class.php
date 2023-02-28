<?php

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminSortBuilder;
use Common\Model\MemberModel;
use User\Api\UserApi;
use Admin\Model\UserModel;

/**
 * 后台用户控制器
 */
class UserController extends AdminController {
    
    public function _initialize() {
        parent::_initialize();
    }
    
    private $userModel;
    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }
    

    /**
     * 用户管理首页
     */
    public function index() {
        $nickname = I('nickname', '');
        $type = I('type', -1);
        
        $records = $this->userModel->get_user_list($nickname, $type);
        $this->assign('_list', $records['list']);
        $this->assign('_page', $records['page']);
        $this->assign('admin_type', $this->userModel->getAdminType());
        $this->assign('type', $type);
        $this->assign('all_type', array('0' => '普通用户', '1' => '管理员'));
        $this->display();
    }
    
    /**
     * 用户详情
     */
    function user_info(){
        $uid = I('uid', 0);
        $user = $this->userModel->get_user_info($uid);
        $this->assign('user', $user);
        $this->display();
    }
    
    /**
     * 设为/解除管理员
     */
    public function changeUserToAdmin(){
        if(IS_AJAX){
            $uid = I('uid');
            $type = I('type');
            $group_id = I('group_id');
            if($type == 'A'){     //设为管理员
                $access = array('uid' => $uid, 'group_id' => $group_id);
                $res = M('sys_auth_group_access')->add($access);
                if ($res) {
                    M('member_account')->where(array('uid' => $uid))->setField('type', 1);
                    $data['status'] = 1;
                } else {
                    $data['status'] = 0;
                    
                }
            }else if($type == 'D'){     //解除管理员
                $res = M('sys_auth_group_access')->where("uid = {$uid}")->delete();
                if ($res) {
                    M('member_account')->where(array('uid' => $uid))->setField('type', 0);
                    $data['status'] = 1;
                } else {
                    $data['status'] = 0;
                }
            }else if($type == 'U'){     //更改管理员分组
                $res = M('sys_auth_group_access')->where('uid = ' . $uid)->setField('group_id', $group_id);
                if ($res) {
                    M('member_account')->where(array('uid' => $uid))->setField('type', 1);
                    $data['status'] = 1;
                } else {
                    $data['status'] = 0;
                }
            }
            $this->ajaxReturn($data);
        }
    }
    
    
    /**
     * 重置用户密码
     */
    public function initPass() {
        $uid = I('uid');
        if (!$uid) {
            $this->error('请选择要重置的用户！');
        }
        
        $salt = 0 + mt_rand() / mt_getrandmax() * (1 - 0);
        $pwd = md5("123456" . $salt);  
        $user = M('member_account');
        $user->password = $pwd;
        $user->salt = $salt;
        $res = $user->where('uid = ' . $uid)->save();
        
        deleteMemberCache($uid);        //删除缓存
        
        if ($res) {
            $this->success('密码重置成功！新密码为“123456”。');
        } else {
            $this->error('密码重置失败！可能密码重置前就是“123456”。');
        }
    }
    
    /**
     * 更改用户昵称
     */
    function change_nickname(){
        if(IS_AJAX){
            $nickname = I('nickname');
            $uid = I('uid', 0);
            $data['status'] = 0;
            if($uid){
                $old_name = M('member_account')->where(array('uid' => $uid))->getField('nickname');
                if($old_name != $nickname){
                    if(M('member_account')->where(array('nickname' => $nickname))->find()){        //昵称已存在
                        $data['status'] = -1;
                    }else{
                        if(M('member_account')->where(array('uid' => $uid))->setField('nickname', $nickname)){    //更改成功
                            deleteMemberCache($uid);          //删除缓存
                            $data['status'] = 1;
                        }else{
                            $data['status'] = 0;
                        }
                    }
                }else{
                    $data['status'] = 2;
                }
            }
            $this->ajaxReturn($data);
        }
    }

    /**
     * 修改昵称初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updateNickname() {
        $nickname = M('member_account')->where(array('uid' => UID))->getField('nickname');
        $this->assign('nickname', $nickname);
        $this->meta_title = '修改昵称';
        $this->display();
    }

    /**
     * 修改昵称提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitNickname() {
        //获取参数
        $nickname = I('post.nickname');
        $password = I('post.password');
        empty($nickname) && $this->error('请输入昵称');
        empty($password) && $this->error('请输入密码');
        //密码验证
//        $User = new \UserApi();
        $user = M('member_account')->where(array('uid' => UID))->field('password, salt')->find();
        $npwd = md5($password . $user['salt']);
        if($npwd != $user['password']){
            $this->error('密码输入错误!');
        }
        $old_nickname = M('member_account')->where(array('uid' => UID))->getField('nickname');
        if($nickname == $old_nickname){
            $this->error('新昵称与之前昵称重复，无法更改!');
        }
        $res = M('member_account')->where(array('uid' => UID))->save(array('nickname' => $nickname));
        
        if ($res) {
            $user = session('user_auth');
            $user['username'] = $nickname;
            session('user_auth', $user);
            session('user_auth_sign', data_auth_sign($user));
            
            deleteMemberCache(UID);             //删除缓存
            $this->success('修改昵称成功！');
        } else {
            $this->error('修改昵称失败！');
        }
    }

    /**
     * 修改密码初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updatePassword() {
        $this->meta_title = '修改密码';
        $this->display();
    }

    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitPassword() {
        //获取参数
        $password = I('post.old');
        empty($password) && $this->error('请输入原密码');
        $data['password'] = I('post.password');
        empty($data['password']) && $this->error('请输入新密码');
        $repassword = I('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');

        if ($data['password'] !== $repassword) {
            $this->error('您输入的新密码与确认密码不一致');
        }
        
        $user = M('member_account')->where(array('uid' => UID))->find();
        if($user['password'] !== md5($password . $user['salt'])){
            $this->error('原密码输入错误');
        }
        if($password == $repassword){
            $this->error('原密码和新密码相同，更改失败');
        }
        $new_pwd = md5($repassword . $user['salt']);
        $user['password'] = $new_pwd;
        $res = M('member_account')->where(array('uid' => UID))->save($user);
        
        if ($res) {
            deleteMemberCache(UID);             //删除缓存
            
            $this->success('修改密码成功！');
        }else{
            $this->error('更改密码失败！');
        }
    }
    
    /**
     * 用户行为列表
     * @author huajie <banhuajie@163.com>
     */
    public function action() {
        $aModule = I('post.module', '', 'text');
        $map['module'] = $aModule;
        $this->assign('current_module', $aModule);
        $map['status'] = array('gt', -1);
        //获取列表数据
        $Action = M('Action')->where(array('status' => array('gt', -1)));

        $list = $this->lists($Action, $map);
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('_list', $list);
        $module = D('Common/Module')->getAll();
        foreach ($module as $key => $v) {
            if ($v['is_setup'] == false) {
                unset($module[$key]);
            }
        }
        $module = array_merge(array(array('name' => '', 'alias' => '系统')), $module);
        $this->assign('module', $module);

        $this->meta_title = '用户行为';
        $this->display();
    }

    /**
     * 新增行为
     * @author huajie <banhuajie@163.com>
     */
    public function addAction() {
        $this->meta_title = '新增行为';
        //$module = D('Module')->getAll();
        $this->assign('module', $module);
        $this->assign('data', null);
        $this->display('editaction');
    }

    /**
     * 编辑行为
     * @author huajie <banhuajie@163.com>
     */
    public function editAction() {
        $id = I('get.id');
        empty($id) && $this->error('参数不能为空！');
        $data = M('Action')->field(true)->find($id);

        $module = D('Module')->getAll();
        $this->assign('module', $module);
        $this->assign('data', $data);
        $this->meta_title = '编辑行为';
        $this->display();
    }

    /**
     * 更新行为
     * @author huajie <banhuajie@163.com>
     */
    public function saveAction() {
        $res = D('Action')->update();
        if (!$res) {
            $this->error(D('Action')->getError());
        } else {
            $this->success($res['id'] ? '更新成功！' : '新增成功！', Cookie('__forward__'));
        }
    }

    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0) {
        switch ($code) {
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
            default:
                $error = '未知错误';
        }
        return $error;
    }

}
