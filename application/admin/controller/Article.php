<?php


namespace app\admin\controller;
use app\admin\model\Article as ArticleModel;
use app\admin\model\Cate as CateModel;
use catetree\Catetree;

class Article extends Base
{
    // 主页
    public function lists() {
        $article = new ArticleModel();
        $artList = $article->getArticleList();
        $this->assign([
            'artList' => $artList,
        ]);
        return view();
    }

    // 添加
    public function add() {
        $article = new ArticleModel();
        if (Request()->isPost()) {
            $data = input('post.');
            $data['addtime'] = time();

            if (stripos($data['link_url'],'http://') === false) {
                $data['link_url'] = 'http://'.$data['link_url'];
            }

            // 获取上传图片的名称
            if ($_FILES['thumb']['name']) {
                $data['thumb'] = $this->upload();
            }

            // 验证器
            $validate = validate('Article');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            $result = $article->addAndSaveArticle($data);
            if ($result !== false) {
                $this->success('添加商品品牌成功','lists');
            } else {
                $this->error('添加商品品牌失败');
            }
        }
        $cateTree = new Catetree();
        $cate = new CateModel();

        $cateList = $cate->getCateList();
        $cateRes = $cateTree->catetree($cateList);
        $this->assign([
            'cateRes' => $cateRes,
        ]);

        return view();
    }

    // 修改，url获取$id
    public function edit($id) {
        $article = new ArticleModel();

        if (request()->isPost()) {
            $data = input('post.');
            $data['addtime'] = time();
            // 给url加前缀
            if ($data['link_url'] && stripos($data['link_url'],'http://') === false) {
                $data['link_url'] = 'http://' . $data['link_url'];
            }
            // 是否上传图片
            if ($_FILES['thumb']['name']) {
                // 获取旧照片
                $oldArt = $article->getThumbByID($id);
                // 拼接路径
                $oldartThumb = IMG_UPLOADS.DS.$oldArt['thumb'];
                if(file_exists($oldartThumb)) {
                    @unlink($oldartThumb);
                }
                $data['thumb'] = $this->upload();
            } else {
                // 获取旧照片
                $oldArt = $article->getThumbByID($id);
                // 拼接路径
                $oldartThumb = $oldArt['thumb'];
                $data['thumb'] = $oldartThumb;
            }

            // 验证
            $validate = validate('Article');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            // 执行修改
            $result = $article->addAndSaveArticle($data,$data['id']);
            if ($result !== false) {
                $this->success('修改文章成功','lists');
            } else {
                $this->error('修改文章失败');
            }
        }
        $cate = new CateModel();
        $cateTree = new Catetree();
        $cate = $cate->getCateList();
        $cateRes = $cateTree->catetree($cate);
        $artData = $article->getArticleByID($id);

        $this->assign([
            'artData'=>$artData,
            'cateRes' => $cateRes,
        ]);
        return view();
    }

    public function del($id) {
        $article = new ArticleModel();
        $thumb = $article->getThumbByID($id);
        $thumbImg = IMG_UPLOADS . DS . $thumb['thumb'];
        if (file_exists($thumbImg)) {
            @unlink($thumbImg);
        }
        $result = $article->delArticleByID($id);
        if ($result) {
            $this->success('删除文章成功','lists');
        } else {
            $this->error('删除文章失败');
        }
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('thumb');
        // 移动到框架应用根目录/uploads/ 目录下
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'uploads');
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            return $info->getSaveName();
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
            die;
        }
    }

    public function imglist() {
        $_files = my_scandir();
        $files = [];
        foreach ($_files as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k1 => $v1) {
                    $v1 = str_replace(UEDITOR,HTTP_UEDITOR,$v1);
                    $files[] = $v1;
                }
            } else {
                $v = str_replace(UEDITOR,HTTP_UEDITOR,$v);
                $files[] = $v;
            }
        }

        $this->assign([
            'imgRes' => $files,
        ]);
        return view();
    }

    public function delimg() {
        $imgSrc = input('imgsrc');
        // /static/ueditor\20200805\1596891397918183.jpg
        $imgSrc = DEL_UEDITOR . $imgSrc;
        if (file_exists($imgSrc)) {
            if (@unlink($imgSrc)) {
                echo 1;
            } else {
                echo 2;
            }
        } else {
            echo 3;
        }
    }
}