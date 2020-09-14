<?php


namespace app\admin\controller;
use app\admin\model\CategoryAd as CategoryAdModel;
use app\admin\model\Category as CategoryModel;
use app\admin\model\Brand as BrandModel;

class CategoryAd extends Base
{
    public function lists() {
        $categoryAd = new CategoryAdModel();
        $categoryAdList = $categoryAd->getcategoryAdList();

        $this->assign([
            'categoryAdList' => $categoryAdList
        ]);
        return view();
    }

    public function add() {
        $categoryAd = new CategoryAdModel();

        if (Request()->isPost()) {
            $data = input('post.');

            if ($data['position'] == 'B' || $data['position'] == 'C') {
                $result = $categoryAd->getCategoryBmC($data['position']);
                if ($result) {
                    $this->error('不能在同个栏目添加多个B，C类栏目广告');
                }
            }
            // 修正不规范url
            if ($data['link_url'] && stripos($data['link_url'],'http://') === false) {
                $data['link_url'] = 'http://'.$data['link_url'];
            }

            // 获取上传图片的名称
            if ($_FILES['img_src']['tmp_name']) {
                $data['img_src'] = $this->upload();
            } else {
                $this->error('请上传图片');
            }

//            $validate = validate('categoryAd');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $categoryAd->addcategoryAd($data);
            if ($result !== false) {
                $this->success('添加栏目广告成功','lists');
            } else {
                $this->error('添加栏目广告失败');
            }
        }
        // 分类栏目
        $category = new CategoryModel();
        $parList = $category->getPartitionList();

        $this->assign([
            'parList' => $parList,
        ]);
        return view();
    }

    public function edit($id) {
        $categoryAd = new CategoryAdModel();
        // 查询要修改的一条数据
        $categoryAdData = $categoryAd->getcategoryAdByID($id);

        if (request()->isPost()) {
            $data = input('post.');

            if ($data['position'] == 'B' || $data['position'] == 'C') {
                $result = $categoryAd->getCategoryBmC($data['position']);
                if ($result) {
                    $this->error('不能在同个栏目修改添加多个B，C类栏目广告');
                }
            }
            // 给url加前缀
            if ($data['link_url'] && stripos($data['link_url'],'http://') === false) {
                $data['link_url'] = 'http://' . $data['link_url'];
            }

            // 是否上传图片
            if ($_FILES['img_src']['tmp_name']) {
                // 拼接旧图片路径
                $oldcategoryAdImg = IMG_UPLOADS.DS.$categoryAdData['img_src'];
                if(file_exists($oldcategoryAdImg)) {
                    @unlink($oldcategoryAdImg);
                }
                $data['img_src'] = $this->upload();
            } else {
                // 获取旧照片,拼接路径
                $data['img_src'] = $categoryAdData['img_src'];
            }

            // 验证
//            $validate = validate('categoryAd');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            // 执行修改
            $result = $categoryAd->savrAeditcategoryAdByID($data,$data['id']);
            if ($result !== false) {
                $this->success('修改栏目广告成功','lists');
            } else {
                $this->error('修改栏目广告失败');
            }
        }
        // 分类栏目
        $category = new CategoryModel();
        $parList = $category->getPartitionList();

        $this->assign([
            'categoryAdData' => $categoryAdData,
            'parList' => $parList,
        ]);
        return view();
    }

    public function del($id) {
        $categoryAd = new CategoryAdModel();
        // 获取当前删除的栏目广告logo
        $Img = $categoryAd->getImgByID($id);
        $delImg = IMG_UPLOADS . DS . $Img['img_src'];
        if (file_exists($delImg)) {
            @unlink($delImg);
        }
        $result = $categoryAd->delcategoryAdByID($id);
        if ($result) {
            $this->success('删除栏目广告成功','lists');
        } else {
            $this->error('删除栏目广告失败');
        }
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('img_src');
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