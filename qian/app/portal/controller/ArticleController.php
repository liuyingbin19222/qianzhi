<?php

namespace app\portal\controller;
use app\portal\model\PortalTagModel;
use cmf\controller\HomeBaseController;
use think\Db;
use think\Request;


class ArticleController extends HomeBaseController
{
    public function index()
    {

        $articleId  = $this->request->param('id');
        $article =Db::name('portal_post')->where('id',$articleId)->find();
        $cid = Db::name('portal_category_post')->where('post_id',$articleId)->value('category_id');
        $tplName = 'article';
        $tplName = empty($article['more']['template']) ? $tplName : $article['more']['template'];
        $this->assign('article', $article);
        $this->assign('aid', $articleId);
        $this->assign('cid', $cid);
        if (Request::instance()->isMobile()) {
            return $this->fetch(':articlemoble');
            //调用移动模板
            // return $view->fetch("/$tplName");
        }else {
            //调用PC模板
            return $this->fetch("/$tplName");
        }
    }
    // 文章点赞
    public function doLike()
    {
        $this->checkUserLogin();
        $articleId = $this->request->param('id', 0, 'intval');


        $canLike = cmf_check_user_action("posts$articleId", 1);

        if ($canLike) {
            Db::name('portal_post')->where(['id' => $articleId])->setInc('post_like');

            $this->success("赞好啦！");
        } else {
            $this->error("您已赞过啦！");
        }
    }

    public function cy()
    {
        $this->checkUserLogin();
        $articleId = $this->request->param('id');
        $article = Db::name('portal_post')->where('id',$articleId)->find();
        $this->assign('article', $article);
        return $this->fetch(':cy');

    }

    public function unLike()
    {
        $this->checkUserLogin();
        $articleId = $this->request->param('id');
        $canLike = cmf_check_user_action("posts$articleId", 1);
        if ($canLike) {
            $this->success("点踩成功，将为您减少此类推荐");
        } else {
            $this->error("您已踩过啦！");
        }
    }
    public function addscore()
    {
        $uid = cmf_get_current_user_id();
        $articleId = $this->request->param('id');
        $cid = Db::name('portal_category_post')->where('post_id',$articleId)->value('category_id');
        $cname = Db::name('portal_category')->where('id',$cid)->value('description');
        //dump($cname);
        $score = $this->request->param('score', 0, 'intval');
        $canLike = cmf_check_user_action("posts$articleId", 1);
        if ($canLike) {
            Db::name('score')
                ->where('uid', $uid)
                ->setInc( $cname, $score);
        }else{
            $this->error("您已赞过啦！");
        }
    }
    public function addtag(){
        $uid = cmf_get_current_user_id();
        if($uid){
            $data   = $this->request->param('keyword');
            $aid   = $this->request->param('aid');
            $find = Db::name('portal_tag')->where('uid',$uid)->where('name',$data)->find();
            //dump($kew);exit;
            if($find){
                Db::name('portal_tag')
                    ->where('uid', $uid)
                    ->where('name',$data)
                    ->update(['create_time' => time(),'aid'=>$aid]);
            }else{
                $name = ['name'=>$data,'create_time'=> time(),'uid'=>$uid,'aid'=>$aid];
                Db::name('portal_tag')->insert($name);
            }

        }
    }
    public function cutscore()
    {
        $uid = cmf_get_current_user_id();
        $articleId = $this->request->param('id');
        $cid = Db::name('portal_category_post')->where('post_id',$articleId)->value('category_id');
        $cname = Db::name('portal_category')->where('id',$cid)->value('description');
        $canLike = cmf_check_user_action("posts$articleId",0);
        if ($canLike) {;
            $score = $this->request->param('score', 0, 'intval');
            $user = Db::name('score')->where('uid', $uid)->find();
            if ($user !== null) {
                Db::name('score')
                    ->where('uid', $uid)
                    ->setDec( $cname, $score);
            } else {
                $user = ['uid' => $uid, $cname=> $score];
                Db::name('score')->insert($user);
            }
        } else {
            $this->error("您已操作啦！");
        }
    }

}
