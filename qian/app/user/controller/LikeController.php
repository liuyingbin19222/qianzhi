<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use app\user\model\CommentModel;
use cmf\controller\UserBaseController;
use app\user\model\UserModel;
use think\Db;

class LikeController extends UserBaseController
{
    /**
     * 个人中心我的评论列表
     */
    public function index()
    {
        $user = cmf_get_current_user();
        $uid = cmf_get_current_user_id();
        $tag = Db::name('portal_post')->where('uid',$user['id'])->distinct(true)->order('time', 'desc')->column('tag');

        //     dump($tag);
        $score = Db::name('score')->where('uid',$uid)->find();
        $this->assign($user);
        $this->assign('score',$score);
        $this->assign('tag',$tag);
        return $this->fetch();
    }
    /**
     * 用户删除评论
     */
    public function delete1()
    {
        $uid = cmf_get_current_user_id();
        $name                = $this->request->param("name");
        $find = Db::name('portal_tag')->where('uid',$uid)->where('name',$name)->find();
        if($find){
            $data = Db::name('portal_tag')->where('uid',$uid)->where('name',$name)->delete();
            if ($data) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    public function delete()
    {
        $uid = cmf_get_current_user_id();
        $data                = $this->request->param("keyword");
        $kew = explode(",", $data);
        $kew =array_filter($kew);
        foreach($kew as $k=>$v){
            $find = Db::name('portal_tag')->where('uid',$uid)->where('name',$v)->find();
            if($find){
                Db::name('portal_tag')->where('uid',$uid)->where('name',$v)->delete();
            }
        }
    }
}