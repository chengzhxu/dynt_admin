<?php

namespace Admin\Controller;
use Admin\Model\WorkModel;
use Admin\Builder\AdminConfigBuilder;

/**
 * 工作相关
 *
 * @author Kevin
 */
class WorkController extends AdminController{
    public function _initialize() {
        parent::_initialize();
    }
    
    private $workModel;
    public function __construct() {
        parent::__construct();
        $this->workModel = new WorkModel();
    }
    
    /**
     * 招聘列表
     */
    function index(){
        $name = I('name', '');
        $records = $this->workModel->getRecruitList($name);
        $this->assign('_list', $records['list']);
        $this->assign('_page', $records['page']);
        $this->display();
    }
    
    /**
     * 发布招聘信息
     */
    function add_recruit(){
        if(IS_POST){
            $data = $_POST;
            if(!$data['uid']){
                $data['uid'] = 7;
            }
            $data['dateline'] = time();
            if($this->workModel->add_recruit($data)){
                $this->success('发布招聘信息成功!', U('index'));
            }else{
                $this->error('发布招聘信息失败!');
            }
        }else{
            $builder = new AdminConfigBuilder();
            $builder->title('发布招聘')
                    ->keyText('uid', '发布人uid')
                    ->keyText('position_name', '职位名称')
                    ->keyText('company_name', '公司名称')
                    ->keyTextArea('address_detail', '公司地址')
                    ->keyText('mobile', '联系人电话')
                    ->keyText('realname', '联系人姓名')
                    ->keySelect('salary', '薪资', '', get_select('WORK_SALARY'))
                    ->keySelect('address', '工作地点', '', get_select('WORK_ADDRESS'))
                    ->keySelect('education', '学历要求', '', get_select('WORK_EDUCATION'))
                    ->keySelect('experience', '工作经验', '', get_select('WORK_EXPERIENCE'))
                    ->keySelect('age', '年龄', '', get_select('WORK_AGE'))
                    ->keySelect('gender', '性别', '', get_select('WORK_GENDER'))
                    ->keyTextArea('duty', '工作职责')
                    ->buttonSubmit()
                    ->buttonBack()
                    ->display();
        }
    }
    
    /**
     * 招聘信息详情
     */
    function recruit_info(){
        $id = I('id', 0);
        $recruit = $this->workModel->getRecruitInfo($id);
        $this->assign('recruit', $recruit);
        $this->display();
    }
    
    /**
     * 删除招聘信息
     */
    function del_recruit(){
        $id = I('id', 0);
        if($this->workModel->delRecruit($id)){
            $this->success('删除招聘信息成功', U('index'));
        }else{
            $this->error('删除招聘信息失败');
        }
    }
    
    /**
     * 更改招聘列表排序
     */
    function change_recruit_order(){
        if(IS_AJAX){
            $id = I('id', 0);
            $order = I('order', 0);
            if($id){
                if(M('common_recruit')->where(array('id' => $id))->setField('display_order', $order)){
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
     * 求职信息列表
     */
    function job(){
        $name = I('name', '');
        $records = $this->workModel->getJobList($name);
        $this->assign('_list', $records['list']);
        $this->assign('_page', $records['page']);
        $this->display();
    }
    
    /**
     * 发布求职
     */
    function add_job(){
        if(IS_POST){
            $data = $_POST;
            if(!$data['uid']){
                $data['uid'] = 7;
            }
            $data['dateline'] = time();
            if($this->workModel->add_job($data)){
                $this->success('发布求职信息成功!', U('job'));
            }else{
                $this->error('发布求职信息失败!');
            }
        }else{
            $builder = new AdminConfigBuilder();
            $builder->title('发布求职')
                ->keyText('uid', '发布人uid')
                ->keyText('position_name', '职位名称')
                ->keyText('mobile', '联系人电话')
                ->keyText('realname', '联系人姓名')
                ->keySelect('job_status', '工作状态', '', get_select('WORK_STATUS'))
                ->keySelect('salary', '薪资', '', get_select('WORK_SALARY'))
                ->keySelect('address', '工作地点', '', get_select('WORK_ADDRESS'))
                ->keySelect('education', '学历', '', get_select('WORK_EDUCATION'))
                ->keySelect('experience', '工作经验', '', get_select('WORK_EXPERIENCE'))
                ->keyText('age', '年龄')
                ->keySelect('gender', '性别', '', get_select('WORK_GENDER'))
                ->keyTextArea('introduce', '自我介绍')
                ->buttonSubmit()
                ->buttonBack()
                ->display();
        }
    }
    
    /**
     * 求职信息详情
     */
    function job_info(){
        $id = I('id', 0);
        $job = $this->workModel->getJobInfo($id);
        $this->assign('job', $job);
        $this->display();
    }
    
    /**
     * 删除求职信息
     */
    function del_job(){
        $id = I('id', 0);
        if($this->workModel->delJob($id)){
            $this->success('删除求职信息成功', U('job'));
        }else{
            $this->error('删除求职信息失败');
        }
    }
    
    /**
     * 更改求职列表排序
     */
    function change_job_order(){
        if(IS_AJAX){
            $id = I('id', 0);
            $order = I('order', 0);
            if($id){
                if(M('common_job')->where(array('id' => $id))->setField('display_order', $order)){
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
}
