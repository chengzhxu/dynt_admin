<?php

/**
 * 验证手机号合法性
 * @param type $mobile
 * @return boolean
 */
function validate_mobile($mobile) {
    $search = '/1[0-9]{1}\d{9}$/';
    if (preg_match($search, $mobile)) {
        return true;
    } else {
        return false;
    }
}
/**
 * 验证QQ号合法性
 * @param type $qq
 * @return boolean
 */
function validate_QQ($qq) {
    $search = '/^\d{5,15}$/';
    if (preg_match($search, $qq)) {
        return true;
    } else {
        return false;
    }
}
/**
 * 验证电子邮件合法性
 * @param type $email
 * @return boolean
 */
function validate_Email($email) {
    $search = '/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/';
    if (preg_match($search, $email)) {
        return true;
    } else {
        return false;
    }
}
/**
 * 获取用户个人信息
**/
function getUserInfo($uid,$type=1){
	return D('Home/Member')->getUserInfo($uid,$type);
}
/**
 * 计算两个时间点之间的时间
**/
function timediff($begin_time,$end_time)
{
      if($begin_time < $end_time){
         $starttime = $begin_time;
         $endtime = $end_time;
      }else{
         $starttime = $end_time;
         $endtime = $begin_time;
      }

      //计算天数
      $timediff = $endtime-$starttime;
      $days = intval($timediff/86400);
      //计算小时数
      $remain = $timediff%86400;
      $hours = intval($remain/3600);
      //计算分钟数
      $remain = $remain%3600;
      $mins = intval($remain/60);
      //计算秒数
      $secs = $remain%60;
      $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
      return $res;
}
/**
 * 数字转换成汉字的数字
**/
function ToChinaseNum($num){
    $char = array("零","一","二","三","四","五","六","七","八","九");
    $dw = array("","十","百","千","万","亿","兆");
    $retval = "";
    $proZero = false;
    for($i = 0;$i < strlen($num);$i++)
    {
        if($i > 0)    $temp = (int)(($num % pow (10,$i+1)) / pow (10,$i));
        else $temp = (int)($num % pow (10,1));
        
        if($proZero == true && $temp == 0) continue;
        
        if($temp == 0) $proZero = true;
        else $proZero = false;
        
        if($proZero)
        {
            if($retval == "") continue;
            $retval = $char[$temp].$retval;
        }
        else $retval = $char[$temp].$dw[$i].$retval;
    }
    if($retval == "一十") $retval = "十";
    return $retval;
 }