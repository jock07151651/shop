<?php


namespace app\admin\controller;
use app\admin\model\CategoryWords as CategoryWordsModel;
use app\admin\model\Category as CategoryModel;

class CategoryWords extends Base
{
    public function lists() {
        $categoryWords = new CategoryWordsModel();
        $categoryWordsList = $categoryWords->getcategoryWordsList();
        $this->assign('categoryWordsList',$categoryWordsList);
        return view();
    }

    public function add() {
        if (Request()->isPost()) {
            $categoryWords = new CategoryWordsModel();

            // 过滤url
            $data = input('post.');
            if (stripos($data['link_url'],'http://') === false) {
                $data['link_url'] = 'http://'.$data['link_url'];
            }


//            $validate = validate('categoryWords');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $categoryWords->savrAeditCategoryWordsByID($data);
            if ($result !== false) {
                $this->success('添加推荐词成功','lists');
            } else {
                $this->error('添加推荐词失败');
            }
        }
        // 获取分类表，顶级分类
        $category = new CategoryModel();
        $parList = $category->getPartitionList();
        $this->assign([
            'parList' => $parList,
        ]);
        return view();
    }

    public function edit($id) {
        $categoryWords = new CategoryWordsModel();
        if (Request()->isPost()) {
            // 过滤url
            $data = input('post.');
            if (stripos($data['link_url'],'http://') === false) {
                $data['link_url'] = 'http://'.$data['link_url'];
            }


//            $validate = validate('categoryWords');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $categoryWords->savrAeditCategoryWordsByID($data,$data['id']);
            if ($result !== false) {
                $this->success('修改推荐词成功','lists');
            } else {
                $this->error('修改推荐词失败');
            }
        }
        // 获取分类表，顶级分类
        $category = new CategoryModel();
        $parList = $category->getPartitionList();
        // 获取推荐词对应数据
        $cateWordData = $categoryWords->getCategoryWordsByID($id);
        $this->assign([
            'parList' => $parList,
            'cateWordData' =>$cateWordData,
        ]);
        return view();
    }

    public function del($id) {
        $categoryWords = new categoryWordsModel();
        $result = $categoryWords->delcategoryWordsByID($id);
        if ($result) {
            $this->success('删除推荐词成功','lists');
        } else {
            $this->error('删除推荐词失败');
        }
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('categoryWords_img');
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
}