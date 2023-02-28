<?php

/**
 * @author Kevin
 */

namespace Admin\Controller;

/**
 * 缓存管理
 */
class CacheController extends AdminController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index() {
        
        $title = I('get.title');
        $type = I('get.type');
        if($title) {
            
            if($type == 'del') {
                emptyMemberCache($title);
                $this->assign('data' , '删除成功');
            } else {
                $data = getMemberCache($title);
                $this->assign('data' , $data);
            }
            $this->assign('title' , $title);
            
        }
        $this->display();
    }
    
}
