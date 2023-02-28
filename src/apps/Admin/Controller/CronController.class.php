<?php

/**
 * @author Kevin
 */

namespace Admin\Controller;

/**
 * 定时任务
 */
class CronController extends AdminController {

    public function _initialize() {
        parent::_initialize();
    }
    /**
     * 任务列表
     */
    public function index() {
        
        $cron = D('Cron');
        $records = $cron->getCronList();
        
        $this->assign('list', $records['list']);
        $this->assign('pagination', $records['pagination']);
        $this->assign('count', $records['count']);
        
        $this->display();
    }
    
    /**
     * 添加定时任务
     */
    function addCron() {
        
        if(IS_POST) {
            
            $id = I('post.id' , 0 );
            $objtype = I('post.objtype');
            $runtime = I('post.runtime');
            $post_id = I('post.post_id');
            if($objtype == 1 || $objtype == 3) {
                if(!$runtime || $runtime == '0000-00-00 00:00:00' || !$post_id)
                    $this->error('活动或专题时，必须填写执行时间和ID');
            }
            
            $data = array(
                'title'   => I('post.title'),
                'runtime' => $runtime,
                'objtype' => $objtype,
                'post_id' => $post_id,
                'content' => I('post.content'),
                'post_title' => I('post.post_title'),
                'num'        => I('post.num'),
                'addtime' => NOW_TIME,
            );
            if($id) {
                //修改
                $rt = M('common_cron')->where(array('id' => $id))->save($data);
            } else {
                //添加
                $rt = M('common_cron')->add($data);
            }
            if($rt){
                $this->success('操作成功' , U('cron/index'));
            } else {
                $this->error('操作失败' , U('cron/index'));
            }
        } else {
            
            $id = I('get.id' , 0);
            
            if($id) {
                $result = M('common_cron')->where(array('id' => $id))->find();
                $this->assign('info' , $result);
            }
            $this->display();
        }
        
    }
    
    /**
     * 删除定时任务
     */
    function delCron() {
        
        $id = I('get.id' , 0);
        
        if($id) {
            
            $rt = M('common_cron')->where(array('id' => $id))->delete();
            if($rt)
                $this->success('删除成功' , U('cron/index'));
            else 
                $this->error('删除失败' , U('cron/index'));
        } else 
            $this->error('参数错误' , U('cron/index'));
    }

}
