<?php

namespace Cron\Controller;

/**
 * Description of IndexController
 *
 * @author Kevin
 */
class IndexController {
    
    function index(){
        echo 'index';exit;
    }
    
    /**
     * 马甲号
     */
    function insert_member(){
        for($i = 0; $i < 100; $i++){
            $mobile = 19912345670 + $i;
            if(!$this->check_mobile($mobile)){
                 //注册用户
                $salt = rand(100000, 999999); //随机码
                $gender = rand(0, 1);
                $account = array(
                    'nickname' => '用户'.$salt,
                    'mobile' => $mobile,
                    'password' => md5('123456' . $salt),
                    'salt' => $salt,
                    'headimg' => $this->getDefaultHeadimg($gender),
                    'gender' => $gender,
                    'regions' => 1,
                    'dateline' => time()
                );
                M('member_account')->add($account);
            }
        }
    }
    
    function check_mobile($mobile){
        if(M('member_account')->where(array('mobile' => $mobile))->find()){
            return true;
        }else{
            return false;
        }
    }
    
    /**
    * 返回默认头像
    */
   function getDefaultHeadimg($gender = 1){
       $b_headimg_arr = array(
           '0' => 'http://niaoting-bucket.oss-cn-shanghai.aliyuncs.com/default_headimg/man1.png',
           '1' => 'http://niaoting-bucket.oss-cn-shanghai.aliyuncs.com/default_headimg/man2.png',
           '2' => 'http://niaoting-bucket.oss-cn-shanghai.aliyuncs.com/default_headimg/man3.png',
           '3' => 'http://niaoting-bucket.oss-cn-shanghai.aliyuncs.com/default_headimg/man4.png',
           '4' => 'http://niaoting-bucket.oss-cn-shanghai.aliyuncs.com/default_headimg/man5.png',
           '5' => 'http://niaoting-bucket.oss-cn-shanghai.aliyuncs.com/default_headimg/man6.png'
       );
       $g_headimg_arr = array(
           '0' => 'http://niaoting-bucket.oss-cn-shanghai.aliyuncs.com/default_headimg/women1.png',
           '1' => 'http://niaoting-bucket.oss-cn-shanghai.aliyuncs.com/default_headimg/women2.png',
           '2' => 'http://niaoting-bucket.oss-cn-shanghai.aliyuncs.com/default_headimg/women3.png',
           '3' => 'http://niaoting-bucket.oss-cn-shanghai.aliyuncs.com/default_headimg/women4.png',
           '4' => 'http://niaoting-bucket.oss-cn-shanghai.aliyuncs.com/default_headimg/women5.png',
           '5' => 'http://niaoting-bucket.oss-cn-shanghai.aliyuncs.com/default_headimg/women6.png'
       );
       $rand = mt_rand(0, 5);
       if($gender == 0){    //女
           $headimg = $g_headimg_arr[$rand];
       }else{
           $headimg = $b_headimg_arr[$rand];
       }
       return $headimg;
   }
   
   /**
    * 马甲号相互关注
    */
   function follow_member(){
       $map['uid'] = array('gt', 9);
       $member_list = M('member_account')->where($map)->select();
       $i= 1;
       foreach ($member_list as $key => $value) {
           foreach ($member_list as $k => $val) {
               if($val['uid'] != $value['uid']){
                   
                   $is_muttual = 0;
                   
                   $mut_map['fid'] = $value['uid'];
                   $mut_map['uid'] = $val['uid'];
                   if(M('sns_follow')->where($mut_map)->find()){
                       $is_muttual = 1;
                   }
                   
                   $sns_map['uid'] = $value['uid'];
                   $sns_map['fid'] = $val['uid'];
                   
                   if(!M('sns_follow')->where($sns_map)->find()){
                        $sns_arr = array(
                            'uid' => $value['uid'],
                            'fid' => $val['uid'],
                            'muttual' => $is_muttual,
                            'dateline' => NOW_TIME
                        );
                        if(M('sns_follow')->add($sns_arr)){
                            if($is_muttual == 1){
                                M('sns_follow')->where($mut_map)->setField('muttual', 1);
                            }
                        }
                   }
               }
           }
           print_r('第' . $i . '个用户关注完毕');
           print_r('<br/>');
           $i++;
       }
       print_r('关注完毕');exit;
   }
}
