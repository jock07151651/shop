<?php


namespace app\admin\controller;
use app\admin\model\Brand as BrandModel;

class Brand extends Base
{
    public function lists() {
        $brand = new BrandModel();
        $brandList = $brand->getBrandList();
        $this->assign('brandList',$brandList);
        return view();
    }

    public function add() {
        if (Request()->isPost()) {
            $brand = new BrandModel();
            $data = input('post.');
            if (stripos($data['brand_url'],'http://') === false) {
                $data['brand_url'] = 'http://'.$data['brand_url'];
            }
            // 获取上传图片的名称
            if ($_FILES['brand_img']['name']) {
                $data['brand_img'] = $this->upload();
            }

            $validate = validate('Brand');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            $result = $brand->addBrand($data);
            if ($result !== false) {
                $this->success('添加商品品牌成功','lists');
            } else {
                $this->error('添加商品品牌失败');
            }
        }
        return view();
    }

    public function edit($id) {
        $brand = new BrandModel();
        if (request()->isPost()) {
            $data = input('post.');
            // 给url加前缀
            if ($data['brand_url'] && stripos($data['brand_url'],'http://') === false) {
                $data['brand_url'] = 'http://' . $data['brand_url'];
            }
            // 是否上传图片
            if ($_FILES['brand_img']['name']) {
                // 获取旧照片
                $oldBrand = $brand->getImgByID($id);
                // 拼接路径
                $oldBrandImg = IMG_UPLOADS.DS.$oldBrand['brand_img'];
                if(file_exists($oldBrandImg)) {
                    @unlink($oldBrandImg);
                }
                $data['brand_img'] = $this->upload();
            } else {
                // 获取旧照片
                $oldBrand = $brand->getImgByID($id);
                // 拼接路径
                $oldBrandImg = $oldBrand['brand_img'];
                $data['brand_img'] = $oldBrandImg;
            }

            // 验证
            $validate = validate('Brand');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            // 执行修改
            $result = $brand->savrAeditBrandByID($data,$data['id']);
            if ($result !== false) {
                $this->success('修改商品品牌成功','lists');
            } else {
                $this->error('修改商品品牌失败');
            }
        }
        $brandData = $brand->getBrandByID($id);
        $this->assign('brandData',$brandData);
        return view();
    }

    public function del($id) {
        $brand = new BrandModel();
        $result = $brand->delBrandByID($id);
        if ($result) {
            $this->success('删除商品品牌成功','lists');
        } else {
            $this->error('删除商品品牌失败');
        }
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('brand_img');
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