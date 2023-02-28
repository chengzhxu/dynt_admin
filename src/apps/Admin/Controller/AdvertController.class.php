<?php

namespace Admin\Controller;
use Admin\Builder\AdminConfigBuilder;
use Admin\Model\AdvertModel;

/**
 * Description of AdvertController
 *
 * @author Kevin
 */
class AdvertController extends AdminController{
    public function _initialize() {
        parent::_initialize();
    }
    
    private $advertModel;
    public function __construct() {
        parent::__construct();
        $this->advertModel = new AdvertModel();
    }
    
    function index(){
        $this->display();
    }
    
    function add_advert(){
        if(IS_POST){
            print_r($_POST);exit;
        }else{
            $builder = new AdminConfigBuilder();
            
            $builder->title('新增广告信息')
                    ->buttonSubmit()
                    ->buttonBack()
                    ->display();
        }
    }
    
    
    //app启动广告图
    function app_start(){
        $title = I('title', '');
        $records = $this->advertModel->getAppStartList($title);
        
        $this->assign('_list', $records['list']);
        $this->assign('_page', $records['page']);
        $this->display();
    }
    
    function add_start(){
        if(IS_POST){
            $data = $_POST;
            if($data['id']){   //编辑
                if($this->advertModel->editStart($data)){
                    $this->success('编辑广告图成功', U('app_start'));
                }else{
                    $this->error('编辑广告图失败');
                }
            }else{    //新增
                if($this->advertModel->addStart($data)){
                    $this->success('新增广告图成功', U('app_start'));
                }else{
                    $this->error('新增广告图失败');
                }
            }
        }else{
            $builder = new AdminConfigBuilder();
            $id = I('id', 0);
            if($id){        //编辑启动图
                $start = $this->advertModel->getStart($id);
                $builder->title('编辑启动图')
                    ->data($start)
                    ->keyHidden('id', "")
                    ->keyText('title', '标题')
                    ->keyOssImage('thumb', '图片')
                    ->keyRadio('status', '状态', '', array('0' => '非启动', '1' => '启动'))
                    ->buttonSubmit()
                    ->buttonBack()
                    ->display();
            }else{      //新增启动图
                $builder->title('新增启动图')
                    ->keyText('title', '标题')
                    ->keyOssImage('thumb', '图片')
                    ->keyRadio('status', '状态', '', array('0' => '非启动', '1' => '启动'))
                    ->buttonSubmit()
                    ->buttonBack()
                    ->display();
            }
        }
    }
    
    /**
     * 删除启动图
     */
    function del_start(){
        $id = I('id', 0);
        if($this->advertModel->delStart($id)){
            $this->success('删除启动图成功', U('app_start'));
        }else{
            $this->error('删除启动图失败');
        }
    }
}
