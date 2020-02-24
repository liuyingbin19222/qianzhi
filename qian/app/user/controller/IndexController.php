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

use app\user\model\UserModel;
use cmf\controller\HomeBaseController;
use app\admin\model\ThemeModel;
use app\portal\model\PortalPostModel;
use think\Db;
class IndexController extends HomeBaseController
{

    /**
     * 前台用户首页(公开)
     */
    public function index()
    {
        $id        = $this->request->param("id", 0, "intval");
        $userModel = new UserModel();
        $user      = $userModel->where('id', $id)->find();
        if (empty($user)) {
            $this->error("查无此人！");
        }
        $this->assign($user->toArray());
        $this->assign('user',$user);
        return $this->fetch(":index");
    }

    /**
     * 前台ajax 判断用户登录状态接口
     */
    function isLogin()
    {
        if (cmf_is_user_login()) {
            $this->success("用户已登录",null,['user'=>cmf_get_current_user()]);
        } else {
            $this->error("此用户未登录!");
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session("user", null);//只有前台用户退出
        return redirect($this->request->root() . "/");
    }
    public function chuanyan()
    {
        $user              = cmf_get_current_user();
        $articles=Db::name('portal_post')->where('user_id',$user['id'])->where('delete_time','=',"")->paginate(8);
        $this->assign('articles',$articles);
        $this->assign($user);
        $this->assign("page", $articles->render());
        return $this->fetch(':chuanyan');
    }
    public function cy()
    {
        return $this->fetch(':cy');
    }

    public function add()
    {
        $themeModel        = new ThemeModel();
        $articleThemeFiles = $themeModel->getActionThemeFiles('portal/Article/index');
        $this->assign('article_theme_files', $articleThemeFiles);
        return $this->fetch(':add');
    }

    public function addPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $post   = $data['post'];
            $result = $this->validate($post, 'AdminArticle');
            if ($result !== true) {
                $this->error($result);
            }

            $portalPostModel = new PortalPostModel();

            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }


            $portalPostModel->AddArticle($data['post'],12);

            $data['post']['id'] = $portalPostModel->id;
            $hookParam          = [
                'is_add'  => true,
                'article' => $data['post']
            ];
            hook('portal_admin_after_save_article', $hookParam);


            $this->success('添加成功!', url('user/index/chuanyan'));
        }

    }
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        $portalPostModel = new PortalPostModel();
        $post            = $portalPostModel->where('id', $id)->find();
        $postCategories  = $post->categories()->alias('a')->column('a.name', 'a.id');
        $postCategoryIds = implode(',', array_keys($postCategories));

        $themeModel        = new ThemeModel();
        $articleThemeFiles = $themeModel->getActionThemeFiles('portal/Article/index');
        $this->assign('article_theme_files', $articleThemeFiles);
        $this->assign('post', $post);
        $this->assign('post_categories', $postCategories);
        $this->assign('post_category_ids', $postCategoryIds);

        return $this->fetch(':edit');
    }

    /**
     * 编辑文章提交
     * @adminMenu(
     *     'name'   => '编辑文章提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑文章提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {

        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $post   = $data['post'];
            $result = $this->validate($post, 'AdminArticle');
            if ($result !== true) {
                $this->error($result);
            }

            $portalPostModel = new PortalPostModel();

            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }

            $portalPostModel->EditArticle($data['post'], 12);

            $hookParam = [
                'is_add'  => false,
                'article' => $data['post']
            ];
            hook('portal_admin_after_save_article', $hookParam);

            $this->success('保存成功!');

        }
    }

    public function delete()
    {
        $param = $this->request->param();
        $portalPostModel = new PortalPostModel();

        if (isset($param['id'])) {
            $id = $this->request->param('id', 0, 'intval');
            $result = $portalPostModel->where(['id' => $id])->find();
            $data = [
                'object_id' => $result['id'],
                'create_time' => time(),
                'table_name' => 'portal_post',
                'name' => $result['post_title'],
                'user_id' => cmf_get_current_user_id()
            ];
            $resultPortal = $portalPostModel
                ->where(['id' => $id])
                ->update(['delete_time' => time()]);
            if ($resultPortal) {
                Db::name('portal_category_post')->where(['post_id' => $id])->update(['status' => 0]);
                Db::name('portal_tag_post')->where(['post_id' => $id])->update(['status' => 0]);

                Db::name('recycleBin')->insert($data);
            }
            $this->success("删除成功！", '');

        }
    }

}
