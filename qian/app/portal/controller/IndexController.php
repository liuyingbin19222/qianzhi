<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use think\Request;
class IndexController extends HomeBaseController
{
    public function index()
    {
        $uid =cmf_get_current_user_id();
        $tag =Db::name('portal_tag')->where('uid',$uid)->order('create_time','desc')->column('name');
        $group = Db::name('score')->where('uid', $uid)->column('shehui,jiaoyu,yule,keji,caijin,tiyu,wenhua,zhengzhi');
        if($group){
            foreach($group as $key=>$v ){
                $group =$v;
            }
            arsort($group);
        }else{
            $group=array('shehui'=>1,'jiaoyu'=>3,'yule'=>3,'zhengzhi'=>3,'keji'=>1,'caijin'=>1,'tiyu'=>1,'wenhua'=>1);
        }
        $keyw =key($group);
        $this->assign('keyw',$keyw);
        $this->assign('cname',$group);
        $this->assign('uid',$uid);
        $this->assign('keyword',$tag);

        if (Request::instance()->isMobile()) {
            return $this->fetch(':indexmoble');
            //调用移动模板
            // return $view->fetch("/$tplName");
        }else {
            //调用PC模板
            return $this->fetch(':index');
        }
    }
}
