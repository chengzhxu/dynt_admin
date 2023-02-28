<?php

namespace Admin\Model;

/**
 * 消息相关
 *
 * @author kevin
 */
class MessageModel extends \Think\Model{
    protected $autoCheckFields = false;
    
    /**
     * 获取消息列表
     */
    function getMessageList($content = '', $type = -1){
        import('ORG.Util.Page');
        $table = M('common_message');
        
        $map['deleted'] = 0;
        if($content){
            $map['content'] = array('like', '%' . (string) $content . '%');
        }
        if($type > -1){
            $map['type'] = $type;
        }
        
        $count = $table->where($map)->count();
        $Page = new \Think\Page($count, 20);
        $pagination = $Page->show();
        $order['dateline'] = 'desc';
        $list = $table->where($map)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        
        foreach ($list as $key => $value) {
            $list[$key]['from_nick'] = ubb_to_emoij(M('member_account')->where(array('uid' => $value['from_uid']))->getField('nickname'));
            if($value['to_uid'] == 0){
                $list[$key]['to_nick'] = '所有人';
            }else{
                $list[$key]['to_nick'] = ubb_to_emoij(M('member_account')->where(array('uid' => $value['to_uid']))->getField('nickname'));
            }
            $list[$key]['content'] = ubb_to_emoij($value['content']);
            $obj_title = '';
            $obj_url = '';
            switch ($value['type']) {
                case 0:
                    $list[$key]['msg_type'] = '系统通知';
                    break;
                case 1:
                    $list[$key]['msg_type'] = '评论';
                    break;
                case 2:
                    $list[$key]['msg_type'] = '点赞';
                    break;
                default:
                    $list[$key]['msg_type'] = '未知';
                    break;
            }
            switch ($value['objtype']) {
                case 1:
                    $obj_title = ubb_to_emoij(M('common_topic')->where(array('id' => $value['objid']))->getField('content'));
                    $obj_url = 'Topic/topic_info?id='.$value['objid'];
                    break;
                case 2:
                    $obj_title = ubb_to_emoij(M('common_recruit')->where(array('id' => $value['objid']))->getField('position_name'));
                    $obj_url = 'Work/recruit_info?id='.$value['objid'];
                    break;
                case 3:
                    $obj_title = ubb_to_emoij(M('common_job')->where(array('id' => $value['objid']))->getField('position_name'));
                    $obj_url = 'Work/job_info?id='.$value['objid'];
                    break;

                default:
                    $obj_title = '系统通知';
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
     * 新增消息
     */
    function addMessage($data){
        if($data){
            return M('common_message')->add($data);
        }
    }
    
    /**
     * 删除消息
     */
    function delMessage($id = 0){
        if($id){
            if(M('common_message')->where(array('id' => $id))->setField('deleted', 1)){
                return true;
            }
        }
    }
}
