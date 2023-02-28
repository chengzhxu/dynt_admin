<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */
/* 解析列表定义规则 */

function get_list_field($data, $grid, $model) {

    // 获取当前字段数据
    foreach ($grid['field'] as $field) {
        $array = explode('|', $field);
        $temp = $data[$array[0]];
        // 函数支持
        if (isset($array[1])) {
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]] = $temp;
    }
    if (!empty($grid['format'])) {
        $value = preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data2) {
            return $data2[$match[1]];
        }, $grid['format']);
    } else {
        $value = implode(' ', $data2);
    }

    // 链接支持
    if (!empty($grid['href'])) {
        $links = explode(',', $grid['href']);
        foreach ($links as $link) {
            $array = explode('|', $link);
            $href = $array[0];
            if (preg_match('/^\[([a-z_]+)\]$/', $href, $matches)) {
                $val[] = $data2[$matches[1]];
            } else {
                $show = isset($array[1]) ? $array[1] : $value;
                // 替换系统特殊字符串
                $href = str_replace(
                    array('[DELETE]', '[EDIT]', '[MODEL]'), array('del?ids=[id]&model=[MODEL]', 'edit?id=[id]&model=[MODEL]', $model['id']), $href);

                // 替换数据变量
                $href = preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data) {
                    return $data[$match[1]];
                }, $href);

                $val[] = '<a href="' . U($href) . '">' . $show . '</a>';
            }
        }
        $value = implode(' ', $val);
    }
    return $value;
}

// 获取模型名称
function get_model_by_id($id) {
    return $model = M('Model')->getFieldById($id, 'title');
}

// 获取属性类型信息
function get_attribute_type($type = '') {
    // TODO 可以加入系统配置
    static $_type = array(
        'num' => array('数字', 'int(10) UNSIGNED NOT NULL'),
        'string' => array('字符串', 'varchar(255) NOT NULL'),
        'textarea' => array('文本框', 'text NOT NULL'),
        'datetime' => array('时间', 'int(10) NOT NULL'),
        'bool' => array('布尔', 'tinyint(2) NOT NULL'),
        'select' => array('枚举', 'char(50) NOT NULL'),
        'radio' => array('单选', 'char(10) NOT NULL'),
        'checkbox' => array('多选', 'varchar(100) NOT NULL'),
        'editor' => array('编辑器', 'text NOT NULL'),
        'picture' => array('上传图片', 'int(10) UNSIGNED NOT NULL'),
        'file' => array('上传附件', 'int(10) UNSIGNED NOT NULL'),
    );
    return $type ? $_type[$type][0] : $_type;
}

/**
 * 获取对应状态的文字信息
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_status_title($status = null) {
    if (!isset($status)) {
        return false;
    }
    switch ($status) {
        case -1 : return '已删除';
            break;
        case 0 : return '禁用';
            break;
        case 1 : return '正常';
            break;
        case 2 : return '待审核';
            break;
        default : return false;
            break;
    }
}

// 获取数据的状态操作
function show_status_op($status) {
    switch ($status) {
        case 0 : return '启用';
            break;
        case 1 : return '禁用';
            break;
        case 2 : return '审核';
            break;
        default : return false;
            break;
    }
}

/**
 * 获取文档的类型文字
 * @param string $type
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_document_type($type = null) {
    if (!isset($type)) {
        return false;
    }
    switch ($type) {
        case 1 : return '目录';
            break;
        case 2 : return '主题';
            break;
        case 3 : return '段落';
            break;
        default : return false;
            break;
    }
}

/**
 * 获取配置的类型
 * @param string $type 配置类型
 * @return string
 */
function get_config_type($type = 0) {
    $list = C('CONFIG_TYPE_LIST');
    return $list[$type];
}

/**
 * 获取配置的分组
 * @param string $group 配置分组
 * @return string
 */
function get_config_group($group = 0) {
    $list = C('CONFIG_GROUP_LIST');
    return $group ? $list[$group] : '';
}

/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map  映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array
 *
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data, $map = array('status' => array(1 => '正常', -1 => '删除', 0 => '禁用', 2 => '未审核', 3 => '草稿'))) {
    if ($data === false || $data === null) {
        return $data;
    }
    $data = (array) $data;
    foreach ($data as $key => $row) {
        foreach ($map as $col => $pair) {
            if (isset($row[$col]) && isset($pair[$row[$col]])) {
                $data[$key][$col . '_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
 * 动态扩展左侧菜单,base.html里用到
 * @author 朱亚杰 <zhuyajie@topthink.net>
 */
function extra_menu($extra_menu, &$base_menu) {
    foreach ($extra_menu as $key => $group) {
        if (isset($base_menu['child'][$key])) {
            $base_menu['child'][$key] = array_merge($base_menu['child'][$key], $group);
        } else {
            $base_menu['child'][$key] = $group;
        }
    }
}

/**
 * 获取参数的所有父级分类
 * @param int $cid 分类id
 * @return array 参数分类和父类的信息集合
 * @author huajie <banhuajie@163.com>
 */
function get_parent_category($cid) {
    if (empty($cid)) {
        return false;
    }
    $cates = M('Category')->where(array('status' => 1))->field('id,title,pid')->order('sort')->select();
    $child = get_category($cid); //获取参数分类的信息
    $pid = $child['pid'];
    $temp = array();
    $res[] = $child;
    while (true) {
        foreach ($cates as $key => $cate) {
            if ($cate['id'] == $pid) {
                $pid = $cate['pid'];
                array_unshift($res, $cate); //将父分类插入到数组第一个元素前
            }
        }
        if ($pid == 0) {
            break;
        }
    }
    return $res;
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1) {
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * 获取当前分类的文档类型
 * @param int $id
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_type_bycate($id = null) {
    if (empty($id)) {
        return false;
    }
    $type_list = C('DOCUMENT_MODEL_TYPE');
    $model_type = M('Category')->getFieldById($id, 'type');
    $model_type = explode(',', $model_type);
    foreach ($type_list as $key => $value) {
        if (!in_array($key, $model_type)) {
            unset($type_list[$key]);
        }
    }
    return $type_list;
}

/**
 * 获取当前文档的分类
 * @param int $id
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_cate($cate_id = null) {
    if (empty($cate_id)) {
        return false;
    }
    $cate = M('Category')->where('id=' . $cate_id)->getField('title');
    return $cate;
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

// 获取子文档数目
function get_subdocument_count($id = 0) {
    return M('Document')->where('pid=' . $id)->count();
}

// 分析枚举类型字段值 格式 a:名称1,b:名称2
// 暂时和 parse_config_attr功能相同
// 但请不要互相使用，后期会调整
function parse_field_attr($string) {
    if (0 === strpos($string, ':')) {
        // 采用函数定义
        return eval(substr($string, 1) . ';');
    }
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

/**
 * 获取行为数据
 * @param string $id 行为id
 * @param string $field 需要获取的字段
 * @author huajie <banhuajie@163.com>
 */
function get_action($id = null, $field = null) {
    if (empty($id) && !is_numeric($id)) {
        return false;
    }
    $list = S('action_list');
    if (empty($list[$id])) {
        $map = array('status' => array('gt', -1), 'id' => $id);
        $list[$id] = M('Action')->where($map)->field(true)->find();
    }
    return empty($field) ? $list[$id] : $list[$id][$field];
}

/**
 * 根据条件字段获取数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @author huajie <banhuajie@163.com>
 */
function get_document_field($value = null, $condition = 'id', $field = null) {
    if (empty($value)) {
        return false;
    }

    //拼接参数
    $map[$condition] = $value;
    $info = M('Model')->where($map);
    if (empty($field)) {
        $info = $info->field(true)->find();
    } else {
        $info = $info->getField($field);
    }
    return $info;
}

/**
 * 获取行为类型
 * @param intger $type 类型
 * @param bool $all 是否返回全部类型
 * @author huajie <banhuajie@163.com>
 */
function get_action_type($type, $all = false) {
    $list = array(
        1 => '系统',
        2 => '用户',
    );
    if ($all) {
        return $list;
    }
    return $list[$type];
}

function get_message_type($type) {
    $msg_type = C('MESSAGE_TYPES');
    return isset($msg_type[$type]) ? $msg_type[$type] : '未知类型';
}

/**
 * 拆分管理员小号
 * 格式如
 *  25330:25,26,27,28,29
    25331:60,61,62,63,64
    25332:80,81,82,83,84
    @param $key要获取的键值，如果传了表示获取这个用户的，如果没有就取当前用户
    
    如果存在返回array，否则为空
*/
function get_vest_account($key = 0) {
    
    $key = $key ? $key : UID; //如果没传的话就是当前登录用户
    
    $accounts = explode("\n" , C('VEST_ACCOUNT'));
    if($accounts) {
        foreach($accounts as $k => $v) {
            $value = explode(':' , $v);
            
            if($value[0] == $key)
                return explode(',' , $value[1]);
        }
        return '';
    } else 
        return '';
}


/**
 * 点赞，评论
 * @param unknown $data
 * @return unknown
 * 1文章活动 2皮肤测试 3功效测试 4用户动态 5福利社
 */
function bindTable($data) {

    //根据类型来从不同的表取数据
    switch ($data['objtype']) {

        case 4: //用户动态
            $table = 'post';
            break;
        default:
            break;
    }

    return $table;
}

/**
 * 根据后台配置的枚举类型，分割成数组
 * @param unknown $data
 * @param $returnvalue -1的话表示返回一个数据，其它值表示取这个值对应的文字
 */
function get_select($name = '' , $returnvalue = -1) {
    
    $return = array();
    $data = D('Config')->where(array('status' => 1, 'name' => $name))->field('id,name,title,extra,value,remark,type')->order('sort')->find();
    
    if($data) {
        if($data['extra']) {
            $res = explode("\n" , $data['extra']);
            foreach($res as $value) {
                $datas = explode(':' , $value);
                
                if($returnvalue > -1) {
                    if($datas[0] == $returnvalue)
                        return $datas[1];
                }
                $return[$datas[0]] = $datas[1];
            }
        }
    }
    
    return $return;
}

/**
 * 自动生成分享图片
 */
function create_ShareImg() {
    
    $tmp = C('TMPL_PARSE_STRING');
    $path = $_SERVER['DOCUMENT_ROOT'] . $tmp['__IMG__'] . '/plugin.png';
    
    $im = @imagecreatefrompng($path);    //从图片建立文件，此处以png文件格式为例  imagecreatefromjpeg
    
    //颜色
    $grey  =  imagecolorallocate ( $im ,  128 ,  128 ,  128 );
    $black  =  imagecolorallocate ( $im ,  0 ,  0 ,  0 );
    
    //写入文字
    imagettftext($im, 20, 0, 300, 50, $grey, '', '');
    imagettftext($im, 20, 0, 10, 20, $black, '', '');
    imagettftext($im, 20, 0, 9, 19, $grey, '', '');
    
    imagepng($im);
    imagedestroy($im);
    
}

/**
     * 导出数据为Excel
     */
    function exportexcel($data,$headArr,$fileName){
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        import("Util.PHPExcel");
        import("Util.PHPExcel.Writer.Excel5");
        import("Util.PHPExcel.IOFactory.php");
        
        $date = date("Y_m_d",time());
        $fileName .= "_{$date}.xls";
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();
        //设置表头
        $key = ord("A");
        //print_r($headArr);exit;
        foreach($headArr as $v){
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();

//        print_r($data);exit;
        $on = "1";
        $t = "show";
        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            if($rows['order_num1'] != $on){
                $on = $rows['order_num1'];
                $t = "show";
                //show
            }else{
                $t = "hidden";
                //hidden
            }
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j.$column, " " . $value);
                $span++;
            }
            $column++;
        }

        //重命名表
        //$objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
//         header('Content-Type: application/vnd.ms-excel');
//         header("Content-Disposition: attachment;filename=\"$fileName\"");
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }
    
    
    //新增消息
    function add_message($data){
        if($data){
            return M('common_message')->add($data);
        }
    }
    
    /**
     * 压缩图片
     */
    function compress_image($file){
        header("Content-type: image/jpeg");
        $percent = 1.5;  //图片压缩比
        list($width, $height) = getimagesize($file); //获取原图尺寸
        //缩放尺寸
        $newwidth = $width * $percent;
        $newheight = $height * $percent;
        $src_im = imagecreatefromjpeg($file);
        $dst_im = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        imagejpeg($dst_im); //输出压缩后的图片
//        imagedestroy($dst_im);
//        imagedestroy($src_im);
        exit;
//        return $new_img;
    }
    
    
    /**
     * 获取指定用户缓存
     */
    function getMemberCache($name){
        $url = "http://api.danyangniaoting.com/util/cache/getMemberCache";
        $post_data = array ("name" => $name);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($output);
        return $output;
    }
    
    /**
     * 清空指定用户缓存
     */
    function emptyMemberCache($name){
        $url = "http://api.danyangniaoting.com/util/cache/emptyMemberCache";
        $post_data = array ("name" => $name);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($output);
        if($result['code'] == 200){
            return true;
        }
    }
    
    /**
     * 清空用户缓存
     */
    function deleteMemberCache($name, $type = 0){
        if($name){
            if($type == 0){           //根据uid清空缓存
                $mobile = M('member_account')->where(array('uid' => $name))->getField('mobile');
                emptyMemberCache('uid' . $name);
                emptyMemberCache('member' . $mobile);
            }else{          //根据手机号清空缓存
                $uid = M('member_account')->where(array('mobile' => $name))->getField('uid');
                emptyMemberCache('uid' . $uid);
                emptyMemberCache('member' . $name);
            }
        }
    }
    
    /**
   * 完整词的截取
   *
   * @param $str
   * @param $start
   * @param $length
   *
   * @return string
   */
    function usubstr($str, $start, $length = null){
        // 先正常截取一遍.
        $res = substr($str, $start, $length);
        $strlen = strlen($str);

        /* 接着判断头尾各6字节是否完整(不残缺) */
        // 如果参数start是正数
        if ($start >= 0) {
          // 往前再截取大约6字节
          $next_start = $start + $length; // 初始位置
          $next_len = $next_start + 6 <= $strlen ? 6 : $strlen - $next_start;
          $next_segm = substr($str, $next_start, $next_len);
          // 如果第1字节就不是 完整字符的首字节, 再往后截取大约6字节
          $prev_start = $start - 6 > 0 ? $start - 6 : 0;
          $prev_segm = substr($str, $prev_start, $start - $prev_start);
        } // start是负数
        else {
          // 往前再截取大约6字节
          $next_start = $strlen + $start + $length; // 初始位置
          $next_len = $next_start + 6 <= $strlen ? 6 : $strlen - $next_start;
          $next_segm = substr($str, $next_start, $next_len);

          // 如果第1字节就不是 完整字符的首字节, 再往后截取大约6字节.
          $start = $strlen + $start;
          $prev_start = $start - 6 > 0 ? $start - 6 : 0;
          $prev_segm = substr($str, $prev_start, $start - $prev_start);
        }
        // 判断前6字节是否符合utf8规则
        if (preg_match('@^([x80-xBF]{0,5})[xC0-xFD]?@', $next_segm, $bytes)) {
          if (!empty($bytes[1])) {
            $bytes = $bytes[1];
            $res .= $bytes;
          }
        }
        // 判断后6字节是否符合utf8规则
        $ord0 = ord($res[0]);
        if (128 <= $ord0 && 191 >= $ord0) {
          // 往后截取 , 并加在res的前面.
          if (preg_match('@[xC0-xFD][x80-xBF]{0,5}$@', $prev_segm, $bytes)) {
            if (!empty($bytes[0])) {
              $bytes = $bytes[0];
              $res = $bytes . $res;
            }
          }
        }
        if (strlen($res) < $strlen) {
          $res = $res . '...';
        }
        return $res;
    }
    
    function saveengsubstr($str, $start, $length = 10){
        $encoding = 'utf-8';
        $res = mb_substr($str,$start,$length,$encoding);
        $last_str = mb_substr($res, -1, 1, $encoding);
        if(!preg_match('/^[\x{4E00}-\x{9FA5}]+$/u', $last_str)) {
            if(mb_strlen($str) > mb_strlen($res)){
                saveengsubstr($str, $start, $length+1);
            }
        }
        return $res . '...';
    }
    
    
    /**
 * emoij图片转成ubb
 * @param unknown $content
 * @return string|mixed
 */
function emoij_to_ubb($content) {
    
    if(!$content)
        return '';
    
    $tmpStr = json_encode($content); //暴露出unicode
    $tmpStr = preg_replace("#(\\\ud[0-9a-f]{3})#ie","addslashes('\\1')",$tmpStr); //将emoji的unicode留下，其他不动
    $text = json_decode($tmpStr);
    return $text;
}
/**
 * ubb 转成 emoij图片
 * @param unknown $content
 * @return string|mixed
 */
function ubb_to_emoij($content) {
    
    if(!$content)
        return '';

	if(preg_match('/\\\\ud/' , $content)) {
		//echo $content;

		preg_match_all("#(\\\ud[0-9a-f]{3})#ie" , $content , $matchs);
		
		$par = '';
		if($matchs[0]) {
			foreach($matchs[0] as $v) {
                            if(!strstr($par, $v)){
                                if($par != '' && strstr($v, 'ud83')){
                                    $replace = "\"" . $par ."\"";
                                    $content = str_replace($par , json_decode($replace) , $content);
                                    $par = '';
                                }
                                $par .= $v;
                            }else{
                                $replace = "\"" . $par ."\"";
                                $content = str_replace($par , json_decode($replace) , $content);
                                $par = '';
                                $par .= $v;
                            }
			}
		}

		$replace = "\"" . $par ."\"";
		
		$text = str_replace($par , json_decode($replace) , $content);

	} else {
		$text = $content;
	}
    
    return $text;
}