<?php

namespace Admin\Controller;
use Admin\Model\MessageModel;
use Admin\Builder\AdminConfigBuilder;

/**
 * 消息管理
 *
 * @author Kevin
 */
class MessageController extends AdminController{
    public function _initialize() {
        parent::_initialize();
    }
    
    private $messageModel;
    public function __construct() {
        parent::__construct();
        $this->messageModel = new MessageModel();
    }
    
    /**
     * 消息列表
     */
    function index(){
        $content = I('content', '');
        $type = I('type', -1);
        $records = $this->messageModel->getMessageList($content, $type);
        $this->assign('_list', $records['list']);
        $this->assign('_page', $records['page']);
        $this->assign('type', $type);
        $this->assign('alltype', array('0' => '系统消息', '1' => '评论', '2' => '点赞'));
        $this->display();
    }
    
    /**
     * 新增消息
     */
    function add_message(){
        if(IS_POST){
            $data = $_POST;
            $data['from_uid'] = C('ADMINUID');
            $data['dateline'] = time();
            if($data['to_uid'] == 0){         //所有用户
                if($this->messageModel->addMessage($data)){
                    $this->success('发送消息成功', U('index'));
                }else{
                    $this->error('发送消息失败');
                }
            }else{
                $msg_arr = array();
                $touid_arr = explode(',', $data['to_uid']);
                for($i = 0; $i < count($touid_arr); $i++){
                    if($touid_arr[$i]){
                        $msg_arr[$i] = array(
                            'title' => $data['title'],
                            'content' => $data['content'],
                            'from_uid' => C('ADMINUID'),
                            'to_uid' => $touid_arr[$i],
                            'dateline' => time()
                        );
                    }
                }
                if(M('common_message')->addAll($msg_arr)){
                    $this->success('发送消息成功', U('index'));
                }else{
                    $this->error('发送消息失败');
                }
            }
        }else{
            $builder = new AdminConfigBuilder();
            $builder->title('新增消息')
                    ->keyText('title', '消息标题')
                    ->keyTextArea('content', '消息内容')
                    ->keyTextArea('to_uid', '接收用户UID(全部写0，多个英文逗号分开，如1,2,3)')
                    ->buttonSubmit()
                    ->buttonBack()
                    ->display();
        }
    }
    
    /**
     * 删除消息
     */
    function del_message(){
        $id = I('id', 0);
        if($this->messageModel->delMessage($id)){
            $this->success('删除消息成功', U('index'));
        }else{
            $this->error('删除消息失败');
        }
    }
}
