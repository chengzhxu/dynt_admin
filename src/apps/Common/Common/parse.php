<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 4/2/14
 * Time: 2:46 PM
 */


function parse_expression($content)
{
    return preg_replace_callback("/(\\[.+?\\])/is", 'parse_expression_callback', $content);
}

function parse_expression_callback($data)
{

    if (preg_match("/#.+#/i", $data[0])) {
        return $data[0];
    }
    $allexpression = D('Core/Expression')->getAll();
    /*    if(!stristr($data[0],":")){
            $data[0] = str_replace(']',':miniblog]',$data[0]);
        }*/
    $info = $allexpression[$data[0]];
    if ($info) {
        return preg_replace("/\\[.+?\\]/i", "<img src='" . $info['src'] . "' />", $data[0]);
    } else {
        return $data[0];
    }
}

/**
 * 限制字符串长度
 * @param        $str
 * @param int $length
 * @param string $ext
 * @return string
 */
function getShort($str, $length = 40, $ext = '')
{
    $str = htmlspecialchars($str);
    $str = strip_tags($str);
    $str = htmlspecialchars_decode($str);
    $strlenth = 0;
    $out = '';
    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/", $str, $match);
    foreach ($match[0] as $v) {
        preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $v, $matchs);
        if (!empty($matchs[0])) {
            $strlenth += 1;
        } elseif (is_numeric($v)) {
            //$strlenth +=  0.545;  // 字符像素宽度比例 汉字为1
            $strlenth += 0.5; // 字符字节长度比例 汉字为1
        } else {
            //$strlenth +=  0.475;  // 字符像素宽度比例 汉字为1
            $strlenth += 0.5; // 字符字节长度比例 汉字为1
        }

        if ($strlenth > $length) {
            $output .= $ext;
            break;
        }

        $output .= $v;
    }
    return $output;
}


/**带省略号的限制字符串长
 * @param $str
 * @param $num
 * @return string
 */
function getShortSp($str, $num)
{
    if (utf8_strlen($str) > $num) {
        $tag = '...';
    }
    $str = getShort($str, $num) . $tag;
    return $str;
}

function utf8_strlen($string = null)
{
// 将字符串分解为单元
    preg_match_all("/./us", $string, $match);
// 返回单元个数
    return count($match[0]);
}


/**
 * 添加magnific效果
 * @param $content
 * @return mixed|string
 * autor:xjw129xjt
 */
function parse_popup($content)
{
    $content = replace_attr($content);
    preg_match_all('/<img src=\"(.*?)\"/', $content, $img_src);
    preg_match_all('/<img src=\".*?\/>/', $content, $img_tag);
    foreach ($img_tag[0] as $k => &$v) {
        $content = str_replace($v, '<a class="popup" href="' . $img_src[1][$k] . '" title="点击查看大图">' . $v . '</a>', $content);
    }
    $content = '  <div class="popup-gallery">' . $content . '</div>';

    return $content;
}

function replace_attr($content)
{
    $content = preg_replace("/class=\".*?\"/si", "", $content);
    $content = preg_replace("/id=\".*?\"/si", "", $content);
    $content = closetags($content);
    return $content;

}

function closetags($html)
{
    preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
    $openedtags = $result[1];

    preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closedtags = $result[1];
    $len_opened = count($openedtags);

    if (count($closedtags) == $len_opened) {
        return $html;
    }
    $openedtags = array_reverse($openedtags);

    for ($i = 0; $i < $len_opened; $i++) {
        if (!in_array($openedtags[$i], $closedtags)) {
            $html .= '</' . $openedtags[$i] . '>';
        } else {
            unset($closedtags[array_search($openedtags[$i], $closedtags)]);
        }
    }
    return $html;
}

/**
 * checkImageSrc  判断链接是否为图片
 * @param $file_path
 * @return bool
 * @author:xjw129xjt xjt@ourstu.com
 */
function checkImageSrc($file_path)
{
    if (!is_bool(strpos($file_path, 'http://'))) {
        $header = curl_get_headers($file_path);
        $res = strpos($header['Content-Type'], 'image/');
        return is_bool($res) ? false : true;
    } else {
        return true;
    }
}

/**
 * filterImage  对图片src进行安全过滤
 * @param $content
 * @return mixed
 * @author:xjw129xjt xjt@ourstu.com
 */
function filterImage($content)
{
    preg_match_all("/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/", $content, $arr); //匹配所有的图片
    if ($arr[1]) {
        foreach ($arr[1] as $v) {
            $check = checkImageSrc($v);
            if (!$check) {
                $content = str_replace($v, '', $content);
            }
        }
    }
    return $content;
}

/**
 * checkHtmlTags  判断是否存在指定html标签
 * @param $content
 * @param $tags
 * @return bool
 * @author:xjw129xjt xjt@ourstu.com
 */
function checkHtmlTags($content, $tags = array())
{
    $tags = is_array($tags) ? $tags : array($tags);
    if (empty($tags)) {
        $tags = array('script', '!DOCTYPE', 'meta', 'html', 'head', 'title', 'body', 'base', 'basefont', 'noscript', 'applet', 'object', 'param', 'style', 'frame', 'frameset', 'noframes', 'iframe');
    }
    foreach ($tags as $v) {
        $res = strpos($content, '<' . $v);
        if (!is_bool($res)) {
            return true;
        }
    }
    return false;
}

/**
 * filterBase64   对内容进行base64过滤
 * @param $content
 * @return mixed
 * @author:xjw129xjt xjt@ourstu.com
 */
function filterBase64($content)
{
    preg_match_all("/data:.*?,(.*?)\"/", $content, $arr); //匹配base64编码
    if ($arr[1]) {
        foreach ($arr[1] as $v) {
            $base64_decode = base64_decode($v);
            $check = checkHtmlTags($base64_decode);
            if ($check) {
                $content = str_replace($v, '', $content);
            }
        }
    }
    return $content;
}

/**
 * filter_video  过滤视频
 * @param $content
 * @return mixed
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function filter_video($content)
{
    $content = D('ContentHandler')->filterVideo($content);
    return $content;
}

/**
 * filter_content  过滤内容，主要用于过滤视频
 * @param $content
 * @return mixed
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function filter_content($content){
    $content = D('ContentHandler')->filterHtmlContent($content);
    return $content;
}