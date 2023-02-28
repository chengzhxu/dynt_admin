<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return array(
    'PICTURE_UPLOAD_CONFIG' => array(
        'maxSize' => 0,
        'exts' => array('jpg', 'jpeg', 'png' , 'gif' , 'mp3' , 'mp4' , '3gp' , 'avi'),
        'saveName' => array('uniqid', ''),
        'rootPath' => '/',
        'trainPath' => './uploads/',
    ),
	//'DEFAULT_MODULE' => 'Home',
//    'MODULE_ALLOW_LIST' => array('Home','Admin','Cron','Util'),
    'OSS_BUCKET' => 'niaoting-bucket',
);