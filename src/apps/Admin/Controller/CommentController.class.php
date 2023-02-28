<?php

namespace Admin\Controller;
use Admin\Model\CommentModel;

/**
 * 评论管理
 *
 * @author kevin
 */
class CommentController extends AdminController{
    public function _initialize() {
        parent::_initialize();
    }
    
    private $commentModel;
    public function __construct() {
        parent::__construct();
        $this->commentModel = new CommentModel();
    }
    
    function index(){
        $content = I('content', '');
        $objtype = I('objtype', 0);
        $records = $this->commentModel->getCommentList($content, $objtype);
        $this->assign('_list', $records['list']);
        $this->assign('_page', $records['page']);
        $this->assign('objtype', $objtype);
        $this->assign('alltype', array('0' => '全部', '1' => '话题', '2' => '招聘', '3' => '求职'));
        $this->display();
    }
    
    function del_comment(){
        $id = I('id', 0);
        if($this->commentModel->delComment($id)){
            $this->success('删除评论成功', U('index'));
        }else{
            $this->error('删除评论失败');
        }
    }
}
