<?php


namespace app\admin\controller;
use app\admin\model\CategoryBrands as CategoryBrandsModel;
use app\admin\model\Category as CategoryModel;
use app\admin\model\Brand as BrandModel;

class CategoryBrands extends Base
{
    public function lists() {
        $categoryBrands = new categoryBrandsModel();
        $categoryBrandsList = $categoryBrands->getcategoryBrandsList();

        $this->assign([
            'categoryBrandsList' => $categoryBrandsList
        ]);
        return view();
    }

    public function add() {
        $categoryBrands = new categoryBrandsModel();

        if (Request()->isPost()) {
            $data = input('post.');
            if ($data['pro_url'] && stripos($data['pro_url'],'http://') === false) {
                $data['pro_url'] = 'http://'.$data['pro_url'];
            }

            // 获取品牌
            if (isset($data['brands_id'])) {
                $data['brands_id'] = implode(',',$data['brands_id']);
            }

            // 获取上传图片的名称
            if ($_FILES['pro_img']['name']) {
                $data['pro_img'] = $this->upload();
            }

//            $validate = validate('categoryBrands');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $categoryBrands->addcategoryBrands($data);
            if ($result !== false) {
                $this->success('添加关联品牌成功','lists');
            } else {
                $this->error('添加关联品牌失败');
            }
        }
        // 分类栏目
        $category = new CategoryModel();
        $parList = $category->getPartitionList();

        // 品牌
        $brand = new BrandModel();
        $brandList = $brand->getBrandList();
        $this->assign([
            'parList' => $parList,
            'brandList' => $brandList,
        ]);
        return view();
    }

    public function edit($id) {
        $categoryBrands = new categoryBrandsModel();
        // 查询要修改的一条数据
        $categoryBrandsData = $categoryBrands->getcategoryBrandsByID($id);

        if (request()->isPost()) {
            $data = input('post.');
            // 给url加前缀
            if ($data['pro_url'] && stripos($data['pro_url'],'http://') === false) {
                $data['pro_url'] = 'http://' . $data['pro_url'];
            }

            // 将数组分割为字符串插入
            if (isset($data['brands_id'])) {
                $data['brands_id'] = implode(',',$data['brands_id']);
            } else {
                $data['brands_id'] = '';
            }

            // 是否上传图片
            if ($_FILES['pro_img']['name']) {
                // 拼接旧图片路径
                $oldcategoryBrandsImg = IMG_UPLOADS.DS.$categoryBrandsData['pro_img'];
                if(file_exists($oldcategoryBrandsImg)) {
                    @unlink($oldcategoryBrandsImg);
                }
                $data['pro_img'] = $this->upload();
            } else {
                // 获取旧照片,拼接路径
                $data['pro_img'] = $categoryBrandsData['pro_img'];
            }

            // 验证
//            $validate = validate('categoryBrands');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            // 执行修改
            $result = $categoryBrands->savrAeditcategoryBrandsByID($data,$data['id']);
            if ($result !== false) {
                $this->success('修改商品品牌成功','lists');
            } else {
                $this->error('修改商品品牌失败');
            }
        }
        // 分类栏目
        $category = new CategoryModel();
        $parList = $category->getPartitionList();

        // 品牌
        $brand = new BrandModel();
        $brandList = $brand->getBrandList();

        $this->assign([
            'categoryBrandsData' => $categoryBrandsData,
            'parList' => $parList,
            'brandList' => $brandList,
        ]);
        return view();
    }

    public function del($id) {
        $categoryBrands = new categoryBrandsModel();
        // 获取当前删除的品牌logo
        $Img = $categoryBrands->getImgByID($id);
        $delImg = IMG_UPLOADS . DS . $Img['pro_img'];
        if (file_exists($delImg)) {
            @unlink($delImg);
        }
        $result = $categoryBrands->delcategoryBrandsByID($id);
        if ($result) {
            $this->success('删除关联品牌成功','lists');
        } else {
            $this->error('删除关联品牌失败');
        }
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('pro_img');
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