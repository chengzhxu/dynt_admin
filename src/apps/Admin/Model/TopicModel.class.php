<?php

namespace Admin\Model;

/**
 * 话题管理
 *
 * @author Kevin
 */
class TopicModel extends \Think\Model{
    protected $autoCheckFields = false;
    
    /**
     * 获取话题列表
     */
    function getTopicList($content = '', $column_id = 0){
        import('ORG.Util.Page');
        $table = M('common_topic');
        
        $map['deleted'] = 0;
        if($content){
            $map['content'] = array('like', '%' . (string) $content . '%');
        }
        if($column_id){
            $map['column_id'] = $column_id;
        }
        
        $count = $table->where($map)->count();
        $Page = new \Think\Page($count, 20);
        $pagination = $Page->show();
        $order['display_order'] = 'desc';
        $order['dateline'] = 'desc';
        $list = $table->where($map)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        
        foreach ($list as $key => $value) {
            if($value['uid']){
                $list[$key]['nickname'] = M('member_account')->where(array('uid' => $value['uid']))->getField('nickname');
            }
            $list[$key]['image'] = M('common_topic_detail')->where(array('topic_id' => $value['id']))->getField('location');
            $list[$key]['img_count'] = M('common_topic_detail')->where(array('topic_id' => $value['id']))->count();
            $list[$key]['content'] = ubb_to_emoij($value['content']);
            
            if($value['column_id'] > 0){
                $list[$key]['column_name'] = M('common_topic_column')->where(array('id' => $value['column_id']))->getField('name');
            }else{
                $list[$key]['column_name'] = '无所属栏目';
            }
        }
        
        return array('list' => $list, 'page' => $pagination);
    }
    
    /**
     * 删除话题
     */
    function delTopic($id = 0){
        if($id){
            if(M('common_topic')->where(array('id' => $id))->setField('deleted', 1)){
                //主题标记删除，附件物理删除
                $detail_list = M('common_topic_detail')->where(array('topic_id' => $id))->select();
                foreach ($detail_list as $key => $value) {
                    $filearr = explode('/',$value['location']);
                    $index = count($filearr) - 1;
                    $filename = $filearr[$index];
                    $filepath = '/data/webroot/dynt/dynt_api/src/www/uploads/topic/' . $filename;
                    unlink($filepath);    //删除服务器文件
                }
                M('common_topic_detail')->where(array('topic_id' => $id))->delete();
                $topic = M('common_topic')->where(array('id' => $id))->field('uid, content')->find();
                if(mb_strlen($topic['content']) > 40){
                    $topic['content'] = saveengsubstr($topic['content'],0,12);
                }
                $msg_arr = array(
                    'from_uid' => C('ADMINUID'),
                    'to_uid' => $topic['uid'],
                    'title' => '系统通知',
                    'content' => '由于您发布的内容《' . $topic['content'] . '》违反了用户服务协议，为了打造干净的网络环境，现已删除您的内容！',
                    'dateline' => time()
                );
                add_message($msg_arr);
                return true;
            }
        }
    }
    
    /**
     * 获取话题详情
     */
    function getTopicDetail($id = 0){
        if($id){
            $topic = M('common_topic')->where(array('id' => $id))->find();
            $member = M('member_account')->where(array('uid' => $topic['uid']))->field('nickname, mobile')->find();
            $topic['nickname'] = $member['nickname'];
            $topic['mobile'] = $member['mobile'];
            $topic['content'] = ubb_to_emoij($topic['content']);
            $thumbs = M('common_topic_detail')->where(array('topic_id' => $id))->select();
            $width = 150;
            foreach ($thumbs as $key => $value) {
                $img_info = getimagesize($value['location']);
                $thumbs[$key]['width'] = $width;
                $thumbs[$key]['height'] = sprintf("%.2f", $img_info[1] * $width / $img_info[0]);
            }
            
            return array('topic' => $topic, 'thumbs' => $thumbs);
        }
    }
    
    /**
     * 获取话题栏目列表
     */
    function getTopicColumnList($name = ''){
        import('ORG.Util.Page');
        $table = M('common_topic_column');
        
        $map['deleted'] = 0;
        $map['id'] = array('gt', 0);
        if($name){
            $map['name'] = array('like', '%' . (string) $name . '%');
        }
        
        $count = $table->where($map)->count();
        $Page = new \Think\Page($count, 20);
        $pagination = $Page->show();
        $order['dateline'] = 'desc';
        $list = $table->where($map)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        return array('list' => $list, 'page' => $pagination);
    }
    
    /**
     * 新增话题栏目
     */
    function addTopicColumn($data){
        if($data){
            return M('common_topic_column')->add($data);
        }
    }
    
    /**
     * 删除话题栏目
     */
    function delTopicColumn($id){
        if($id){
            if(M('common_topic_column')->where(array('id' => $id))->setField('deleted', 1)){
                M('common_topic')->where(array('column_id' => $id))->setField('column_id', 0);
                return true;
            }else{
                return false;
            }
        }
    }
}
