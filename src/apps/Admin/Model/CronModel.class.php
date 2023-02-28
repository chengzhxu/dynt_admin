<?php
/**
 * Created by PhpStorm.
 * User: aslan
 * Date: 15/6/15
 * Time: 下午3:35
 */

namespace Admin\Model;

use Think\Model;


class CronModel extends Model {

    protected $autoCheckFields = false;

    /**
     * 计划任务列表
     */
    public function getCronList() {
        import('ORG.Util.Page');

        $table = M('common_cron');

        $count = $table->count();
        $Page = new \Think\Page($count, 20);
        $pagination = $Page->show();

        $order['id'] = 'DESC';
        $list = $table->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        
        return array('list' => $list, 'pagination' => $pagination, 'count' => $count);
    }

} 