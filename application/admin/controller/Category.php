<?php


namespace app\admin\controller;
use app\admin\model\Category as CategoryModel;
use app\admin\model\Recpos as RecposModel;
use app\admin\model\RecItem as RecItemModel;
use catetree\Catetree;


class Category extends Base
{
    // 主页
    public function lists() {
        $category = new CategoryModel();
        $cateTree = new Catetree();
        if (request()->isPost()) {
            $sort = input('post.');
            $cateTree->cateSort($sort['sort'],$category);
            $this->success('排序成功!');
        }
        $categorys = $category->getcategoryList();
        $categoryList = $cateTree->catetree($categorys);
        $this->assign([
            'categoryList' => $categoryList,
        ]);
        return view();
    }

    // 添加
    public function add() {
        $category = new CategoryModel();
        if (Request()->isPost()) {
            $data = input('post.');

            // 获取上传图片的名称
            if ($_FILES['cate_img']['name']) {
                $data['cate_img'] = $this->upload();
            }

            // 验证器
//            $validate = validate('Category');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $category->addAndSavecategory($data);
            if ($result !== false) {
                $this->success('添加商品分类成功','lists');
            } else {
                $this->error('添加商品分类失败');
            }
        }

        $cateTree = new Catetree();
        $cateList = $category->getCategoryList();
        // 无限极分类
        $cateRes = $cateTree->catetree($cateList);

        // 分类对应的推荐位
        $recpos = new RecposModel();
        $recposList = $recpos->getRecposList(2);
        $this->assign([
            'cateRes' => $cateRes,
            'recposList' => $recposList,
        ]);

        return view();
    }

    // 修改，url获取$id
    public function edit($id) {
        $category = new categoryModel();
        if (request()->isPost()) {
            $data = input('post.');

            // 是否上传图片
            if ($_FILES['cate_img']['name']) {
                // 获取照片字段
                $oldArt = $category->getcategoryImgByID($id);
                // 拼接路径
                $oldartcateImg = IMG_UPLOADS.DS.$oldArt['cate_img'];
                if(file_exists($oldartcateImg)) {
                    @unlink($oldartcateImg);
                }
                $data['cate_img'] = $this->upload();
            } else {
                // 获取照片字段
                $oldcateImg = $category->getcategoryImgByID($id);
                // 拼接路径
                $data['cate_img'] = $oldcateImg['cate_img'];
            }

            // 验证
//            $validate = validate('category');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            // 执行修改
            $result = $category->addAndSavecategory($data,$data['id']);
            if ($result !== false) {
                $this->success('修改商品分类成功','lists');
            } else {
                $this->error('修改商品分类失败');
            }
        }
        $cateTree = new Catetree();
        $cate = $category->getCategoryList();
        // 排序
        $categoryTree = $cateTree->catetree($cate);
        $categoryData = $category->getcategoryByID($id);

        // 分类对应的推荐位
        $recpos = new RecposModel();
        $recposList = $recpos->getRecposList(2);

        // 查询修改分类对应的推荐
        $recItem = new RecItemModel();
        $_myCategoryRecList = $recItem->getMyGoodsRecList($id,2);

        // 将相同的recpos_id作为下标，存入数组
        $myCategoryRecList = [];
        foreach ($_myCategoryRecList as $k => $v) {
            $myCategoryRecList[$v['recpos_id']] = $v['recpos_id'];
        }


        $this->assign([
            'categoryData'=>$categoryData,
            'categoryTree' => $categoryTree,
            'recposList' => $recposList,
            'myCategoryRecList' => $myCategoryRecList,
        ]);
        return view();
    }

    public function del($id) {
        $category = new categoryModel();
        $cateTree = new Catetree();
        // 获取当前分类的子分类的id
        $sonIDs = $cateTree->childrenIDs($id,$category);
        $sonIDs[] = intval($id);

        // 删除对应分类的推荐位
        $recItem = new RecItemModel();
        foreach ($sonIDs as $k => $v) {
            $recItem->delCategoryWordsByID(2,$v);
        }

        // 遍历里面的分类id
        foreach ($sonIDs as $k => $v) {
            $categoryList = $category->getcategoryListByID($v);
            foreach ($categoryList as  $k1=>$v1) {
                $thumbImg = IMG_UPLOADS . DS . $v1['cate_img'];
                if (file_exists($thumbImg)) {
                    @unlink($thumbImg);
                }
                $category->delcategoryByID($v1['id']);
            }
        }
        $this->success('删除商品分类成功','lists');
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('cate_img');
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