<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;
use Admin\Model\ClientModel;
/**
 * Description of ClientController
 *
 * @author Kevin
 */
class ClientController extends AdminController{
    /**
     * 客户端错误日志
     */
    public function crashlog(){
        $errorinfo = I('errorinfo');
        $map['error_string'] = array('like', '%' . (string) $errorinfo . '%');
        $list = $this->lists('crash_log', $map);
        
        $this->assign('_list', $list);
        $this->meta_title = '错误日志';
        $this->display();
    }
}
