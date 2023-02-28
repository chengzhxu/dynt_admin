<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Logic;
use Admin\Model\ActivityModel;
/**
 * Description of CommentLogic
 *
 * @author Kevin
 */
class CommentLogic {
    
    public $lists = array();
    
    /**
     * 将评论封装为Tree结构
     */
    function getTree(&$data,$pid = 0, $count = 0) {
        if(!isset($data['old'])){
            $data = array('new'=>array(), 'old'=>$data);
        }
        foreach ($data['old'] as $k => $v){
            if($v['parent_id'] == $pid){
//                $v['Count'] = $count * 30;
                $data['new'][] = $v;
                unset($data['old'][$k]);
                $this->getTree($data,$v['id'], $count+1); 
            }
        }
        return $data['new'] ;
     }
     
     function getTreeNode($list){
        if(count($list) > 0){
            $this->lists = array_merge($this->lists,$list);
        }
        foreach ($list as $key => $val) {
            $childList = $this->getCommentInfoByParentId($val['id']);
            if(count($childList) > 0){
                $this->getTreeNode($childList);
            }
        }
        return $this->lists;
    }
     
     /**
     * 获取活动子评论详情
     */
    public function getCommentInfoByParentId($id){
        import('ORG.Util.Page');
        $table = M('common_comment');
        $where = "hjy_common_comment.parent_id = $id and hjy_common_comment.deleted = 0";
//        
        $list = $table->where($where)
            ->field('hjy_common_comment.*')
            ->select();
        $activityModel = new ActivityModel();
        foreach ($list as $key => $val) {
            $list[$key]['fromNickname'] = get_nickname($val['from_uid']);
            $list[$key]['toNickname'] = get_nickname($val['to_uid']);
            $list[$key]['fromMobile'] = get_mobileByUid($val['from_uid']);
            $list[$key]['toMobile'] = get_mobileByUid($val['to_uid']);
            $list[$key]['from_time'] = $activityModel->getMemberCreateTimeById($val['from_uid']);
            $list[$key]['to_time'] = $activityModel->getMemberCreateTimeById($val['to_uid']);
            $list[$key]['from_credit1'] = $activityModel->getMemberCreditById($val['from_uid']);
            $list[$key]['to_credit1'] = $activityModel->getMemberCreditById($val['to_uid']);
            $list[$key]['user_img'] = M('member_profile')->where("uid= {$val['from_uid']}")->getField('headimg');
            $rootid = M('member_account')->where("uid = {$val['from_uid']}")->getField('rootid');
            $list[$key]['is_lock'] = M('member')->where("id= {$rootid}")->getField('status');
        }
        return  $list;
    }
}
