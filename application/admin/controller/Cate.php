<?php


namespace app\admin\controller;
use app\admin\model\Cate as CateModel;
use app\admin\model\Article as ArticleModel;

use catetree\Catetree;

class Cate extends Base
{
    public function lists() {
        $cate = new CateModel();
        $cateTree = new Catetree();
        if (request()->isPost()) {
            $sort = input('post.');
            // 调用排序方法
            $cateTree->cateSort($sort['sort'],$cate);
            $this->success('排序成功!','lists');

        }
        $cateRes = $cate->getCateList();
        $cateRes = $cateTree->catetree($cateRes);

        $this->assign([
            'cateRes' => $cateRes,
        ]);
        return view();
    }

    public function add() {
        $cate = new CateModel();
        $cateTree = new Catetree();

        if (request()->isPost()) {
            $data = input('post.');
            // 判断是否可以添加子类,顶级的id作为pid传递
            if (in_array($data['pid'],['1','3'])) {
                $this->error('系统分类不能作为上级分类！');
            }

            //
            if($data['pid'] == 2) {
                $data['cate_type'] = 3;
            }

            // 传入的pid是当前分类的id
            $cateID = $cate->getCatePID($data['pid']);
            // 再获取当前分类的pid
            $cateID = $cateID['pid'];
            if ($cateID == 2) {
                $this->error('此分类不能作为上级分类!');
            }

            $validate = validate('cate');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            $result = $cate->addAndeditCate($data);
            if ($result !== false) {
                $this->success('添加分类成功','lists');
            } else {
                $this->error('添加分类失败');
            }
        }
        $cateRes = $cate->getCateList();
        $cateRes = $cateTree->catetree($cateRes);
        $this->assign([
            'cateRes' => $cateRes,
        ]);
        return view();
    }

    // 修改
    public function edit($id) {
        $cate = new CateModel();
        $cateTree = new Catetree();

        if (request()->isPost()) {
            $data = input('post.');

            $validate = validate('cate');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $result = $cate->addAndeditCate($data,$data['id']);
            if ($result !== false) {
                $this->success('修改分类成功','lists');
            } else {
                $this->error('修改分类失败');
            }
        }

        // 对应id的信息
        $cateData = $cate->getCateByID($id);
        // 无限极分类
        $cateRes = $cate->getCateList();
        $cateRes = $cateTree->catetree($cateRes);




        $this->assign([
            'cateData' => $cateData,
            'cateRes' => $cateRes,
        ]);
        return view();
    }

    public function del() {
        $cate = new CateModel();
        $cateTree = new Catetree();
        $article = new ArticleModel();

        $id = input('id');
        // 获取当前id的子级
        $sonIDs = $cateTree->childrenIDs($id,$cate);
        $sonIDs[] = intval($id);
        // 系统内置分类不能删除
        $arrSys = [1,2,3];
        // 取查询出来的ids 和 系统id做交集，存在则不能删除
        $arrRes = array_intersect($arrSys,$sonIDs);
        if ($arrRes) {
            $this->error('系统内置文章分类不允许删除！');
        }

        // 删除栏目时，带对应的文章及文章下的缩略图一并删除
        foreach ($sonIDs as $k => $v) {
            $articleList = $article->getArticleListByID($v);
            foreach ($articleList as $k1 => $v1) {
                $thumbImg = IMG_UPLOADS . DS . $v1['thumb'];
                if (file_exists($thumbImg)) {
                    @unlink($thumbImg);
                }
                $article->delArticleByID($v1['id']);
            }
        }

        // 删除
        $del = $cate->delBateByID($sonIDs);
        if ($del) {
            $this->success('删除分类成功！','lists');
        } else {
            $this->error('删除分类失败！');
        }
    }

}