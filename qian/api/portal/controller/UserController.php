<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuwu <15093565100@163.com>
// +----------------------------------------------------------------------
namespace api\portal\controller;

use api\portal\model\PortalPostModel;
use cmf\controller\RestBaseController;
use think\Db;

class UserController extends RestBaseController
{
    protected $postModel;

    public function __construct(PortalPostModel $postModel)
    {
        parent::__construct();
        $this->postModel = $postModel;
    }

    /**
     * 会员文章列表
     */
    public function articles()
    {
        $userId   = $this->request->param('user_id', 0, 'intval');

        if(empty($userId)){
            $this->error('用户id不能空！');
        }

        $data     = $this->request->param();
        $articles = $this->postModel->setCondition($data)->where(['user_id' => $userId])->select();

        if (count($articles) == 0) {
            $this->error('没有数据');
        } else {
            $this->success('ok', ['list' => $articles]);
        }

    }
    public function group()
    {
        $userId   = cmf_get_current_user_id();

        if(empty($userId)){
            $this->error('用户id不能空！');
        }
        $group = Db::name('group')->where('uid',$userId)->find();
        if (count($group) == 0) {
            $this->error('没有数据');
        } else {
            if (in_array("hot", $group)) {
                $group['group']='国内焦点';
            }
            if (in_array("edu", $group)) {
                $group['group']='教育最新';
            }
            if (in_array("entertainment", $group)) {
                $group['group']='娱乐最新';
            }
            if (in_array("science", $group)) {
                $group['group']='科技焦点';
            }
            if (in_array("military", $group)) {
                $group['group']='军事焦点';
            }
            if (in_array("pe", $group)) {
                $group['group']='综合体育最新';
            }
            if (in_array("history", $group)) {
                $group['group']='科普最新';
            }
            if (in_array("game", $group)) {
                $group['group']='游戏最新';
            }
            $this->success('ok', ['list' => $group]);
        }
    }

}
