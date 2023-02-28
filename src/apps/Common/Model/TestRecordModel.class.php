<?php

namespace Common\Model;

use Think\Model;

/**
 * Description of TestRecordModel
 *
 * @author floy
 */
class TestRecordModel extends Model {

    function _initialize() {
        $uid = session('TESTRECORD_UID');
        //dump($uid);exit;
        if (!$uid) {
            return false;
        }
        $this->tableName = calc_hash_db($uid, 'test');
    }

    private function newAutoId() {
        return M('TestRecordId')->data(array('nofield' => false))->add();
    }

    function insert($data) {
        $data['id'] = $this->newAutoId();
        if (!$data['id']) {
            return false;
        }
        return $this->data($data)->add();
    }
    
    function update($data, $map){
        return $this->data($data)->where($map)->save();
    }
    
    function delRow($map){
        return $this->where($map)->delete();
    }

    function getSearch($map) {
        return $this->where($map)->select();
    }

    function getRow($map) {
        return $this->where($map)->find();
    }

}
