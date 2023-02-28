<?php

namespace Admin\Model;

/**
 * 评论管理
 *
 * @author kevin
 */
class CommentModel extends \Think\Model{
    protected $autoCheckFields = false;
    
    /**
     * 获取评论首页
     */
    function getCommentList($content, $objtype = 0){
        import('ORG.Util.Page');
        $table = M('common_comment');
        $where['deleted'] = 0;
        if($content){
            $where['content'] = array('like', '%' . (string) $content . '%');
        }
        if($objtype > 0){
            $where['objtype'] = $objtype;
        }
        
        $count = $table->where($where)->count();
        $Page = new \Think\Page($count, 20);
        $pagination = $Page->show();
        $order['dateline'] = 'desc';
        $list = $table->where($where)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        
        foreach ($list as $key => $value) {
            $from = M('member_account')->where(array('uid' => $value['from_uid']))->field('nickname, mobile')->find();
            $to = M('member_account')->where(array('uid' => $value['to_uid']))->field('nickname, mobile')->find();
            $list[$key]['from_nick'] = ubb_to_emoij($from['nickname']);
            $list[$key]['from_mobile'] = $from['mobile'];
            $list[$key]['to_nick'] = ubb_to_emoij($to['nickname']);
            $list[$key]['to_mobile'] = $to['mobile'];
            $list[$key]['content'] = ubb_to_emoij($value['content']);
            $obj_title = '';
            $obj_url = '';
            switch ($value['objtype']) {
                case 1:
                    $list[$key]['comment_type'] = '话题';
                    $obj_title = ubb_to_emoij(M('common_topic')->where(array('id' => $value['objid']))->getField('content'));
                    $obj_url = 'Topic/topic_info?id='.$value['objid'];
                    break;
                case 2:
                    $list[$key]['comment_type'] = '招聘';
                    $obj_title = ubb_to_emoij(M('common_recruit')->where(array('id' => $value['objid']))->getField('position_name'));
                    $obj_url = 'Work/recruit_info?id='.$value['objid'];
                    break;
                case 3:
                    $list[$key]['comment_type'] = '求职';
                    $obj_title = ubb_to_emoij(M('common_job')->where(array('id' => $value['objid']))->getField('position_name'));
                    $obj_url = 'Work/job_info?id='.$value['objid'];
                    break;

                default:
                    $list[$key]['comment_type'] = '未知';
                    break;
            }
            if(mb_strlen($obj_title) > 40){
                $obj_title = mb_substr($obj_title,0,15,'utf-8') . '...';
            }
            $list[$key]['obj_title'] = $obj_title;
            $list[$key]['obj_url'] = $obj_url;
        }
        return array('list' => $list, 'page' => $pagination);
    }
    /**
     * 删除评论
     */
    function delComment($id = 0){
        if($id){
            $comment = M('common_comment')->where(array('id' => $id))->find();
            if(M('common_comment')->where(array('id' => $id))->setField('deleted', 1)){
                if($comment['parent_id'] == 0){
                    $this->del_reply($id);
                }else{
                    M('common_comment')->where(array('parent_id' => $comment['id']))->setField('parent_id', $comment['parent_id']);
                }
                return true;
            }
        }
    }
    
    /**
     * 删除评论的子评论
     */
    function del_reply($parent_id = 0){
        if($parent_id){
            $reply_list = M('common_comment')->where(array('parent_id' => $parent_id))->select();
            M('common_comment')->where(array('parent_id' => $parent_id))->setField('deleted', 1);
            foreach ($reply_list as $key => $value) {
                $this->del_reply($value['id']);
            }
        }
    }
    
    /**
     * 评论
     */
    function addComment($data){
        if($data){
            return M('common_comment')->add($data);
        }
    }
}
