<?php
/**
 * Created by PhpStorm.
 * User: aslan
 * Date: 15/6/11
 * Time: 下午4:50
 */

namespace Admin\Logic;


class UtilLogic {

    /**
     * 根据id，获取图片路径等信息
     */
    function getImgUrlById($id = 0) {
        if (intval($id) > 0) {
            return M('CommonPicture')->where('id = ' . $id)->find();
        }
    }

} 