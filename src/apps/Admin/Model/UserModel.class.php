<?php

namespace Admin\Model;

/**
 * Description of UserModel
 *
 * @author Kevin
 */
class UserModel extends \Think\Model{
    protected $autoCheckFields = false;
    
    /**
     * 获取用户列表
     */
    function get_user_list($nickname = '', $type = -1){
        import('ORG.Util.Page');
        $table = M('member_account');
        if($nickname){
            if(is_numeric($nickname)){
                $map['mobile'] = array('like', '%' . (string) $nickname . '%');
            }else{
                $map['nickname'] = array('like', '%' . (string) $nickname . '%');
            }
        }
        
        if($type > -1){
            $map['type'] = $type;
        }
        
        $count = $table->where($map)->count();
        $Page = new \Think\Page($count, 20);
        $pagination = $Page->show();
        $list = $table->where($map)->order('dateline desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $key => $value) {
            $list[$key]['nickname'] = ubb_to_emoij($value['nickname']);
        }
        return array('list' => $list, 'page' => $pagination);
    }
    
    /**
     * 获取用户详情
     */
    function get_user_info($uid = 0){
        if($uid){
            $member = M('member_account')->where(array('uid' => $uid))->find();
            $member['nickname'] = ubb_to_emoij($member['nickname']);
            return $member;
        }
    }
    
    /**
     * 更新用户
     */
    function update_account($data){
        if($data){
            if(M('member_account')->where(array('uid' => $data['uid']))->save($data)){
                return true;
            }else{
                return false;
            }
        }
    }
    
    /**
     * 获取所有管理员类型0
     */
    public function getAdminType(){
        import('ORG.Util.Page');
        $table = M('sys_auth_group')->where("status = 1");
        $order['hjy_sys_auth_group.id'] = 'desc';
        $list = $table->select();
        return $list;
    }
}
