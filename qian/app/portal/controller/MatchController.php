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
use app\portal\model\PortalTagModel;
use cmf\controller\HomeBaseController;
use think\Db;
use think\Request;


class MatchController extends HomeBaseController
{
    public function cunku(){
        $url = 'http://www.textvalve.com/htdatasub/subscribe/articles/toPublish/v2?userId=82&size=100&rnd0.456121920803368&page=1';
        $file_contents = file_get_contents($url);
        $result = json_decode($file_contents);
        $result = $result->data->list;
        import('mgc.SensitiveFilter', EXTEND_PATH,'.php');
        $fa = new \SensitiveFilter();
        import('tx.src.QcloudApi.QcloudApi', EXTEND_PATH,'.php');
        $config = array(
            'SecretId'       => 'AKIDvkHfv4F3vElFcvA1iqoaCcu053rYoBuC',
            'SecretKey'      => 'UIBsTuBIF1bvAHCwVsLkARWVJOtodHjD',
            'RequestMethod'  => 'POST',
            'DefaultRegion'  => 'gz');
        $foo = new \QcloudApi();
        $wenzhi = $foo::load($foo::MODULE_WENZHI, $config);

        foreach($result as $k =>$v){
            $url1 = 'http://www.textvalve.com/htdatasub/subscribe/articles/v2/article-'.$result[$k]->id;
            $file_contents1 = file_get_contents($url1);
            $result1 = json_decode($file_contents1);
            $result1 = $result1->data;
            $package = array(
                "title"=>$result1->title,
                "content"=>strip_tags($result1->content));
            $a = $wenzhi->TextKeywords($package);
            $b = $wenzhi->TextClassify($package);
            if($b["classes"][0]["class"]!=="未分类"){
                if ($a['keywords'][0] !== NULL ) {
                    $ids = array_column($a['keywords'], 'keyword');
                    $keyword = implode(",",$ids);
                }else{
                    $keyword="";
                }

                //dump($b["classes"][0]["class"]);
                if(false === $fa::filter($result1->title) OR false === $fa::filter(strip_tags($result1->content)) ){
                    $type = 0;
                }else{
                    $type = 1;
                }
                $article = [
                    'user_id'=>0,
                    'id'=>$result1->id,
                    'published_time'=>$result[$k]->crawl_time,
                    'update_time'=>$result[$k]->crawl_time,
                    'post_title'=>$result1->title,
                    'post_keywords'=>$keyword,
                    'post_excerpt'=>$result[$k]->description,
                    'post_source'=>$result1->source_site,
                    'post_content'=>$result1->content,
                    'fenlei'=>$b["classes"][0]["class"],
                    'image'=>$result1->image_list,
                    'post_type'=>$type,
                ];
                Db::name('portal_post')->insert($article);
            }
        }
    }

    public function fenlei(){
        $fenlei =Db::name('portal_post')->where('user_id',0)->where('post_type',1)->column('id,fenlei');
        foreach($fenlei as $k=>$v){
            switch ($v)
            {
                case "科技":
                    $class = ['post_id'=>$k,'category_id'=>5,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "刑事犯罪":
                    $class = ['post_id'=>$k,'category_id'=>10,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "互联网":
                    $class = ['post_id'=>$k,'category_id'=>5,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "财经":
                    $class = ['post_id'=>$k,'category_id'=>7,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "别的政治类别":
                    $class = ['post_id'=>$k,'category_id'=>10,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "教育":
                    $class = ['post_id'=>$k,'category_id'=>3,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "文化":
                    $class = ['post_id'=>$k,'category_id'=>13,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "别的社会类别":
                    $class = ['post_id'=>$k,'category_id'=>1,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "食品安全":
                    $class = ['post_id'=>$k,'category_id'=>1,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "意识形态":
                    $class = ['post_id'=>$k,'category_id'=>10,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "心灵鸡汤":
                    $class = ['post_id'=>$k,'category_id'=>13,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "汽车":
                    $class = ['post_id'=>$k,'category_id'=>7,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "领导人相关":
                    $class = ['post_id'=>$k,'category_id'=>10,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "明星":
                    $class = ['post_id'=>$k,'category_id'=>4,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "旅游":
                    $class = ['post_id'=>$k,'category_id'=>13,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "人物":
                    $class = ['post_id'=>$k,'category_id'=>1,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "健康":
                    $class = ['post_id'=>$k,'category_id'=>1,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "女性":
                    $class = ['post_id'=>$k,'category_id'=>13,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "房产":
                    $class = ['post_id'=>$k,'category_id'=>7,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "金融安全":
                    $class = ['post_id'=>$k,'category_id'=>7,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "贪污腐败":
                    $class = ['post_id'=>$k,'category_id'=>10,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "影视":
                    $class = ['post_id'=>$k,'category_id'=>4,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "体育":
                    $class = ['post_id'=>$k,'category_id'=>8,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "求职招聘":
                    $class = ['post_id'=>$k,'category_id'=>1,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "交通事故":
                    $class = ['post_id'=>$k,'category_id'=>1,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "幽默搞笑":
                    $class = ['post_id'=>$k,'category_id'=>13,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "美食":
                    $class = ['post_id'=>$k,'category_id'=>13,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "历史":
                    $class = ['post_id'=>$k,'category_id'=>13,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "亲子":
                    $class = ['post_id'=>$k,'category_id'=>13,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "游戏":
                    $class = ['post_id'=>$k,'category_id'=>6,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "音乐":
                    $class = ['post_id'=>$k,'category_id'=>4,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "军事":
                    $class = ['post_id'=>$k,'category_id'=>1,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                case "政治":
                    $class = ['post_id'=>$k,'category_id'=>10,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
                    break;
                default:
                    $class = ['post_id'=>$k,'category_id'=>1,'status'=>1];
                    Db::name('portal_category_post')->insert($class);
            }
        }
        //dump($fenlei);
    }
}
