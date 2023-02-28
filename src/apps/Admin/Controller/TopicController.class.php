<?php

namespace Admin\Controller;
use Admin\Model\TopicModel;
use Admin\Builder\AdminConfigBuilder;

/**
 * 话题管理
 *
 * @author Kevin
 */
class TopicController extends AdminController{
    public function _initialize() {
        parent::_initialize();
    }
    
    private $topicModel;
    public function __construct() {
        parent::__construct();
        $this->topicModel = new TopicModel();
    }
    
    /**
     * 话题列表
     */
    function index(){
        $content = I('content', '');
        $column_id = I('column_id', 0);

        $records = $this->topicModel->getTopicList($content, $column_id);
        $column_list = M('common_topic_column')->where(array('deleted' => 0))->field('id, name')->select();
        $column_arr = array();
        for($i = 0; $i < count($column_list); $i++){
            $column_arr[$column_list[$i]['id']] = $column_list[$i]['name'];
        }
        $this->assign('_list', $records['list']);
        $this->assign('_page', $records['page']);
        $this->assign('column_arr', $column_arr);
        $this->assign('column_id', $column_id);
        $this->display();
    }
    
    function del_topic(){
        $id = I('id', 0);
        if($this->topicModel->delTopic($id)){
            $this->success('删除话题成功', U('index'));
        }else{
            $this->error('删除话题失败');
        }
    }
    
    /**
     * 话题排序
     */
    function change_topic_order(){
        if(IS_AJAX){
            $id = I('id', 0);
            $order = I('order', 0);
            if($id){
                if(M('common_topic')->where(array('id' => $id))->setField('display_order', $order)){
                    $data['status'] = 1;
                }else{
                    $data['status'] = 0;
                }
            }else{
                $data['status'] = 0;
            }
            $this->ajaxReturn($data);
        }
    }
    
    /**
     * 话题详情
     */
    function topic_info(){
        $id = I('id', 0);
        $records = $this->topicModel->getTopicDetail($id);
        $this->assign('topic', $records['topic']);
        $this->assign('thumbs', $records['thumbs']);
        $this->display();
    }
    
    /**
     * 话题分类列表
     */
    function topic_column(){
        $name = I('name', '');
        $records = $this->topicModel->getTopicColumnList($name);
        $this->assign('_list', $records['list']);
        $this->assign('_page', $records['page']);
        $this->display();
    }
    
    /**
     * 新增话题栏目
     */
    function add_topic_column(){
        if(IS_POST){
            $data = $_POST;
            $data['dateline'] = NOW_TIME;
            if($this->topicModel->addTopicColumn($data)){
                $this->success('新增话题栏目成功!', U('topic_column'));
            }else{
                $this->error('新增话题栏目失败!');
            }
        }else{
            $builder = new AdminConfigBuilder();
            $builder->title('新增话题栏目')
                    ->keyText('name', '栏目名称')
                    ->buttonSubmit()
                    ->buttonBack()
                    ->display();
        }
    }
    
    /**
     * 删除话题栏目
     */
    function del_topic_column(){
        $id = I('id', 0);
        if($this->topicModel->delTopicColumn($id)){
            $this->success('删除话题栏目成功', U('topic_column'));
        }else{
            $this->error('删除话题栏目失败');
        }
    }
}
