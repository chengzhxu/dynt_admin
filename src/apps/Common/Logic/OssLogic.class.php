<?php

namespace Common\Logic;

/**
 * 附件上传到阿里云OSS
 *
 * @author Kevin
 */
class OssLogic {

    private $oss_sdk_service;
    private $folder;

    /**
     * 
     */
    function __construct() {
        
        Vendor('Oss.ALIOSS#class');
        $this->oss_sdk_service = new \ALIOSS();
        //设置是否打开curl调试模式
        $this->oss_sdk_service->set_debug_mode(FALSE);
    }

    /**
     * 
     * @param type $data
     * @param $folder 
     */
    function save($data ,$filename, $folder = 'avatar') {
		$filename = $filename ? $filename : 'source.jpg';
        $this->folder = $folder;
        if($data) {
            
            $return = $this->upload_by_content($data,$filename);
            
            return $return;
        } else {
            return -1;
        }
    }

    //通过内容上传文件
    function upload_by_content($content,$filename){
        
        $buckets = C('UPLOAD_TYPE_CONFIG');
        $bucket = $buckets['bucket'];
        
        $object = $this->folder . '/'.$filename;
        
        $upload_file_options = array(
            'content' => $content,
            'length' => strlen($content),
            \ALIOSS::OSS_HEADERS => array(
                'Expires' => date('Y-m-d H:i:s' , NOW_TIME),
            ),
        );
        
        $response = $this->oss_sdk_service->upload_file_by_content($bucket,$object,$upload_file_options);
        
        if($response->status == 200) {
            //成功
            $return['status'] = 200;
            //替换掉
            $return['url']    = $response->header['_info']['url'];
        } else {
            //失败
            $return['status'] = 100;
        }
        return $return;
    }
}
