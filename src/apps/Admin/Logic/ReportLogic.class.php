<?php
/**
 * User: Kevin
 */

namespace Admin\Logic;


class ReportLogic {

    /**
     * 获取真实用户的数据，排除内部人员发的信息
     */
    function getRealData($begin_time , $end_time) {
        
        //时间
        if($begin_time && $end_time) {
            $starttime = strtotime($begin_time);
            $endtime   = strtotime($end_time);
        } else {
            $starttime = strtotime(date('Y-m-d' , NOW_TIME));
            $endtime   = $starttime + 86400;
        }
        
        $where['b.source'] = array(array('neq','vest'),array('neq','vest1'), 'and');
        $where['a.dateline'] = array('between' , array($starttime , $endtime));
        
        $skinTestCount = M('test_skin')->alias('a')->field('COUNT(a.id) as testcount,count(distinct a.uid) as usercount')
                        ->join(C('DB_PREFIX') . 'member_profile as b on a.uid = b.uid')
                        ->where($where)->find();
        $effectTestCount = M('test_effect')->alias('a')->field('COUNT(a.id) as testcount,count(distinct a.uid) as usercount')
                        ->join(C('DB_PREFIX') . 'member_profile as b on a.uid = b.uid')
                        ->where($where)->find();
        
        //测试人次
        $testPerson = M()->query("select count(DISTINCT uid) as countuser from (select a.uid from hjy_test_skin a inner join hjy_member_profile as b on a.uid = b.uid where b.source <> 'vest' and b.source <> 'vest1' AND a.dateline BETWEEN ".$starttime." AND ".$endtime."
union all select a.uid from hjy_test_effect a inner join hjy_member_profile as b on a.uid = b.uid where b.source <> 'vest' and b.source <> 'vest1' AND a.dateline BETWEEN ".$starttime." AND ".$endtime.") a");
        
        $result['testcount'] = $skinTestCount['testcount'] + $effectTestCount['testcount'];
        $result['testperson']= $testPerson[0]['countuser'];
        
        $result['skintestcount'] = $skinTestCount['testcount'];
        $result['skintestperson'] = $skinTestCount['usercount'];
        
        $result['effecttestcount'] = $effectTestCount['testcount'];
        $result['effecttestperson'] = $effectTestCount['usercount'];
        
        //圈儿及专题数据
        $commentcount = M('common_comment')->alias('a')->field('COUNT(a.id) as commentcountcount')
                        ->join(C('DB_PREFIX') . 'member_profile as b on a.from_uid = b.uid')
                        ->where($where)->find();
        
        $praisecount  = M('common_praise')->alias('a')->field('COUNT(a.id) as praisecount')
                        ->join(C('DB_PREFIX') . 'member_profile as b on a.from_uid = b.uid')
                        ->where($where)->find();
        
        $sharecount   = M('common_share')->alias('a')->field('COUNT(a.share_id) as sharecount')
                        ->join(C('DB_PREFIX') . 'member_profile as b on a.uid = b.uid')
                        ->where($where)->find();
        
        $result['topiccount'] = $commentcount['commentcountcount'] + $praisecount['praisecount'] + $sharecount['sharecount'];
        
        //参与人次
        $topPerson = M()->query("select count(DISTINCT uid) as countuser from (
            select a.from_uid as uid from hjy_common_comment a inner join hjy_member_profile as b on a.from_uid = b.uid where b.source <> 'vest' AND a.dateline BETWEEN ".$starttime." AND ".$endtime."
union all select a.from_uid as uid from hjy_common_praise a inner join hjy_member_profile as b on a.from_uid = b.uid where b.source <> 'vest' and b.source <> 'vest1' and b.source <> 'vest1' AND a.dateline BETWEEN ".$starttime." AND ".$endtime." 
union all select a.uid from hjy_common_share a inner join hjy_member_profile as b on a.uid = b.uid where b.source <> 'vest' and b.source <> 'vest1' AND a.dateline BETWEEN ".$starttime." AND ".$endtime.") a");
        
        $result['topperson'] = $topPerson[0]['countuser'];
        
        return $result;
    }

} 