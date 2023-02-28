<?php

namespace Admin\Model;

/**
 * 工作相关
 *
 * @author kevin
 */
class WorkModel extends \Think\Model{
    protected $autoCheckFields = false;
    
    /**
     * 获取招聘列表
     */
    function getRecruitList($title){
        import('ORG.Util.Page');
        $table = M('common_recruit');
        
        $map['deleted'] = 0;
        if($title){
            $map['position_name'] = array('like', '%' . (string) $title . '%');
        }
        
        $count = $table->where($map)->count();
        $Page = new \Think\Page($count, 20);
        $pagination = $Page->show();
        $order['display_order'] = 'desc';
        $order['dateline'] = 'desc';
        $list = $table->where($map)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        
        foreach ($list as $key => $value) {
            if($value['uid']){
                $list[$key]['nickname'] = ubb_to_emoij(M('member_account')->where(array('uid' => $value['uid']))->getField('nickname'));
            }
        }
        
        return array('list' => $list, 'page' => $pagination);
    }
    
    /**
     * 新增招聘信息
     */
    function add_recruit($data){
        return M('common_recruit')->add($data);
    }
    
    /**
     * 获取招聘详情
     */
    function getRecruitInfo($id = 0){
        $recruit = M('common_recruit')->where(array('id' => $id))->find();
        if($recruit){
            $member = M('member_account')->where(array('uid' => $recruit['uid']))->field('nickname, mobile')->find();
            $recruit['nickname'] = ubb_to_emoij($member['nickname']);
            $recruit['username'] = $member['mobile'];
            $recruit['duty'] = ubb_to_emoij($recruit['duty']);
        }
        return $recruit;
    }
    
    /**
     * 删除招聘信息
     */
    function delRecruit($id = 0){
        if($id){
            if(M('common_recruit')->where(array('id' => $id))->setField('deleted', 1)){
                $recruit = M('common_recruit')->where(array('id' => $id))->field('uid, position_name')->find();
//                if(mb_strlen($recruit['position_name']) > 40){
//                    $recruit['position_name'] = saveengsubstr($recruit['position_name'],0,12);
//                }
                $msg_arr = array(
                    'from_uid' => C('ADMINUID'),
                    'to_uid' => $recruit['uid'],
                    'title' => '系统通知',
                    'content' => '由于您发布的招聘信息《' . $recruit['position_name'] . '》违反了用户服务协议，为了打造干净的网络环境，现已删除您的内容！',
                    'dateline' => time()
                );
                add_message($msg_arr);
                return true;
            }
        }
    }
    
    /**
     * 获取求职列表
     */
    function getJobList($title = ''){
        import('ORG.Util.Page');
        $table = M('common_job');
        
        $map['deleted'] = 0;
        if($title){
            $map['position_name'] = array('like', '%' . (string) $title . '%');
        }
        
        $count = $table->where($map)->count();
        $Page = new \Think\Page($count, 20);
        $pagination = $Page->show();
        $order['display_order'] = 'desc';
        $order['dateline'] = 'desc';
        $list = $table->where($map)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        
        foreach ($list as $key => $value) {
            if($value['uid']){
                $list[$key]['nickname'] = ubb_to_emoij(M('member_account')->where(array('uid' => $value['uid']))->getField('nickname'));
            }
        }
        
        return array('list' => $list, 'page' => $pagination);
    }
    
    /**
     * 发布求职
     */
    function add_job($data){
        return M('common_job')->add($data);
    }
    
    /**
     * 获取求职信息详情
     */
    function getJobInfo($id = 0){
        if($id){
            $job = M('common_job')->where(array('id' => $id))->find();
            if($job){
                $member = M('member_account')->where(array('uid' => $job['uid']))->field('nickname, mobile')->find();
                $job['nickname'] = ubb_to_emoij($member['nickname']);
                $job['username'] = $member['mobile'];
                $job['introduce'] = ubb_to_emoij($job['introduce']);
            }
            return $job;
        }
    }
    
    /**
     * 删除求职信息
     */
    function delJob($id = 0){
        if($id){
            if(M('common_job')->where(array('id' => $id))->setField('deleted', 1)){
                $job = M('common_job')->where(array('id' => $id))->field('uid, position_name')->find();
//                if(mb_strlen($job['position_name']) > 40){
//                    $job['position_name'] = saveengsubstr($job['position_name'],0,12);
//                }
                $msg_arr = array(
                    'from_uid' => C('ADMINUID'),
                    'to_uid' => $job['uid'],
                    'title' => '系统通知',
                    'content' => '由于您发布的内容《' . $job['position_name'] . '》违反了用户服务协议，为了打造干净的网络环境，现已删除您的内容！',
                    'dateline' => time()
                );
                add_message($msg_arr);
                return true;
            }
        }
    }
}
