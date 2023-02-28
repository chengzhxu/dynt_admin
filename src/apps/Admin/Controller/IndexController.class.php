<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class IndexController extends AdminController
{

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index()
    {
        if (UID) {
            if(IS_POST){
                $count_day=I('post.count_day', C('COUNT_DAY'),'intval');
                if(D('Config')->where(array('name'=>'COUNT_DAY'))->setField('value',$count_day)===false){
                    $this->error('设置失败。');
                }else{
                   S('DB_CONFIG_DATA',null);
                    $this->success('设置成功。','refresh');
                }

            }else{
                $this->meta_title = '管理首页';
                $this->display();
            }
        } else {
            $this->redirect('Public/login');
        }
    }

}
