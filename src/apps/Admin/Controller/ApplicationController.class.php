<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;

/**
 * Description of ApplicationController
 *
 * @author floy
 */
class ApplicationController extends AdminController {

    function _initialize() {
        parent::_initialize();
    }

    function index() {
        $map = array();
        $list = $this->lists('CommonApps', $map, 'appid');
        $this->assign('list', $list);
        $this->meta_title = '应用 管理';
        $this->display();
    }

    function add() {
        $this->edit();
    }

    function edit() {
        if (IS_POST) {
            $post = I('post.');
            
            if ($post['appid']) {
                $rt = M('CommonApps')->where()->data($post)->save();
            } else {
                $post['dateline'] = NOW_TIME;
                $rt = M('CommonApps')->data($post)->add();
            }
            if($rt){
                $this->success('数据保存成功！','Application/index');
            }else{
                $this->error('数据保存失败！');
            }
        } else {
            $appid = I('get.appid', 0);
            $data = array();
            if (intval($appid)) {
                $data = M('CommonApps')->find($appid);
            }
            $build = new AdminConfigBuilder();
            $build->title('应用编辑')
                ->data($data)
                ->keyHidden('appid', 'appid')
                ->keyText('app_name', '应用名称')
                ->keyText('app_key', '应用KEY')
                ->keyTextArea('description', '应用描述')
                ->buttonSubmit()->buttonBack()
                ->display();
        }
    }

    function remove() {
        
    }

}
