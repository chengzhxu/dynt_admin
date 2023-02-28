<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class FileController extends AdminController {
    /* 文件上传 */

    public function upload() {
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');
        /* 调用文件上传组件上传文件 */
        $File = D('File');
        $file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
        $info = $File->upload(
            $_FILES, C('DOWNLOAD_UPLOAD'), C('DOWNLOAD_UPLOAD_DRIVER'), C("UPLOAD_{$file_driver}_CONFIG")
        );

        /* 记录附件信息 */
        if ($info) {
            $return['data'] = think_encrypt(json_encode($info['download']));
            $return['info'] = $info['download']['name'];
        } else {
            $return['status'] = 0;
            $return['info'] = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

    /* 下载文件 */

    public function download($id = null) {
        if (empty($id) || !is_numeric($id)) {
            $this->error('参数错误！');
        }

        $logic = D('Download', 'Logic');
        if (!$logic->download($id)) {
            $this->error($logic->getError());
        }
    }

    /**
     * 上传图片
     * @author huajie <banhuajie@163.com>
     */
    public function uploadPicture() {
        //TODO: 用户登录检测

        /* 返回标准数据 */
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');
        
        //$oss = D('Common/Oss','Logic');
        
        
        //$this->ajaxReturn($return);
        /* 调用文件上传组件上传文件 */
        $Picture = D('Picture');
//        $pic_driver = C('PICTURE_UPLOAD_DRIVER'); 
//        $info = $Picture->upload($_FILES, C('PICTURE_UPLOAD'), C('PICTURE_UPLOAD_DRIVER'), C("UPLOAD_{$pic_driver}_CONFIG")); //TODO:上传到远程服务器
        
        $fieldname = $_GET['fieldname'];
        $path      = $_GET['path'];
        if($path) {
            $uptype = 'Local';
            
            C('PICTURE_UPLOAD_CONFIG.rootPath' , C('PICTURE_UPLOAD_CONFIG.trainPath') . str_replace('-' , '/' , $path) .'/');
            C('PICTURE_UPLOAD_CONFIG.autoSub' , false);
            //创建目录
            mkdir(C('PICTURE_UPLOAD_CONFIG.rootPath'), 0777 , true);
        } else {
            $uptype = 'Oss';
        }
        
        $info = $Picture->upload($_FILES,C('PICTURE_UPLOAD_CONFIG'),$uptype);
        /* 记录图片信息 */
        if ($info) {
            $return['status'] = 1;
            empty($info['download']) && $info['download'] = $info['file'];
            $return = array_merge($info['download'], $return);
        } else {
            $return['status'] = 0;
            $return['info'] = $Picture->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }
    
    
    
    /**
     * 上传图片到oss
     */
    function uploadThumb(){
        $oss = D('Oss' , 'Logic');
        
        $filename = time().mt_rand(1000, 9999) . '.jpg';
        if(move_uploaded_file($_FILES["file"]["tmp_name"],'uploads/' . $filename)){
            $return = array('status' => 200, 'url' => 'http://www.danyangniaoting.com/uploads/'.$filename);
        }else{
            $return = array('status' => 0);
        }
        
//        compress_image($_FILES["file"]["tmp_name"]);
        
//        $return = $oss->upload_by_file($filename, $_FILES["file"]["tmp_name"]);
//        $return = $oss->upload_by_multi_part($_FILES["file"]["tmp_name"], 'upload/'.date('Y-m-d', NOW_TIME),'',$_FILES['file']['size']);
        $this->ajaxReturn($return);
    }

}
