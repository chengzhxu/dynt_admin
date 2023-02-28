<?php

namespace Admin\Model;

/**
 * Description of AdvertModel
 *
 * @author kevin
 */
class AdvertModel extends \Think\Model{
    protected $autoCheckFields = false;
    
    /**
     * 获取app启动图列表
     */
    function getAppStartList($title){
        import('ORG.Util.Page');
        $table = M('app_start');
        
        $map['deleted'] = 0;
        if($title){
            $map['title'] = array('like', '%' . (string) $title . '%');
        }
        
        $count = $table->where($map)->count();
        $Page = new \Think\Page($count, 20);
        $pagination = $Page->show();
        $list = $table->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        
        return array('list' => $list, 'page' => $pagination);
    }
    
    /**
     * 新增启动图
     */
    function addStart($data){
        if($data){
            return M('app_start')->add($data);
        }
    }
    
    /**
     * 获取指定启动图
     */
    function getStart($id = 0){
        if($id){
            return M('app_start')->where(array('id' => $id))->find();
        }
    }
    
    function editStart($data){
        if($data){
            if(M('app_start')->where(array('id' => $data['id']))->save($data)){
                return true;
            }
        }
    }
    
    /**
     * 删除指定启动图
     */
    function delStart($id = 0){
        if($id){
            if(M('app_start')->where(array('id' => $id))->setField('deleted', 1)){
                return true;
            }
        }
    }
}
