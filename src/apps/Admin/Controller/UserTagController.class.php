<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-4-13
 * Time: 下午2:05
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;

class UserTagController extends AdminController
{
    protected $userTagModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->userTagModel=D('Ucenter/UserTag');
    }
    /**
     * 标签分类
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function userTag()
    {
        //显示页面
        $builder = new AdminTreeListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';

        $tree = $this->userTagModel->getTree(0, 'id,title,sort,pid,status');

        $builder->title('用户标签管理')
            ->suggest('新增标签后要到“角色列表》默认信息配置》可拥有标签配置”中操作后才会在前台显示吆')
            ->buttonNew(U('UserTag/add'))->button('回收站',array('href'=>U('UserTag/TagTrash')))
            ->data($tree)
            ->display();
    }

    /**
     * 分类添加
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function add($id=0,$pid=0)
    {
        if (IS_POST) {
            if ($id != 0) {
                $result=$this->userTagModel->saveData();
                if ($result) {
                    $this->success('编辑成功。', U('UserTag/userTag'));
                } else {
                    $this->error('编辑失败。'.$this->userTagModel->getError());
                }
            } else {
                $result=$this->userTagModel->addData();
                if ($result) {
                    $this->success('新增成功。');
                } else {
                    $this->error('新增失败。'.$this->userTagModel->getError());
                }
            }
        } else {
            $builder = new AdminConfigBuilder();
            $opt = array();
            if ($id != 0) {
                $category = $this->userTagModel->find($id);
                if($category['pid']!=0){
                    $categorys = $this->userTagModel->where(array('pid'=>0))->select();
                    foreach ($categorys as $cate) {
                        $opt[$cate['id']] = $cate['title'];
                    }
                }
            } else {
                $category = array('pid' => $pid, 'status' => 1);
                $father_category_pid=$this->userTagModel->where(array('id'=>$pid))->getField('pid');
                if($father_category_pid!=0){
                    $this->error('分类不能超过二级！');
                }
                $categorys = $this->userTagModel->where(array('pid'=>0))->select();
                foreach ($categorys as $cate) {
                    $opt[$cate['id']] = $cate['title'];
                }
            }
            if($pid!=0){
                $builder->title('新增标签');
            }else{
                $builder->title('新增分类');
            }
            $builder->keyId()->keyText('title', '标题')->keySelect('pid', '父分类', '选择父级分类', array('0' => '顶级分类') + $opt)
                ->keyStatus()
                ->data($category)
                ->buttonSubmit(U('UserTag/add'))->buttonBack()->display();
        }

    }

    /**
     * 分类回收站
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function tagTrash($page = 1, $r = 20)
    {
        $builder = new AdminListBuilder();
        //读取微博列表
        $map = array('status' => -1);
        $list = $this->userTagModel->where($map)->page($page, $r)->select();
        $totalCount = $this->userTagModel->where($map)->count();

        //显示页面

        $builder->title('标签分类回收站')
            ->setStatusUrl(U('setStatus'))->buttonRestore()->buttonClear(U('UserTag/userTagClear'))
            ->keyId()->keyText('title', '标题')->keyText('pid','父分类id')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function userTagClear($ids)
    {
        $builder=new AdminListBuilder();
        $builder->doClear('UserTag',$ids);
    }

    /**
     * 设置商品分类状态：删除=-1，禁用=0，启用=1
     * @param $ids
     * @param $status
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        if($status==-1){
            $id = array_unique((array)$ids);
            $rs=M('UserTag')->where(array('pid' => array('in', $id)))->save(array('status' => $status));
        }
        $builder->doSetStatus('UserTag', $ids, $status);
    }

    //分类、标签end
} 