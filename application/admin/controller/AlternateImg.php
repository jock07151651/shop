<?php


namespace app\admin\controller;
use app\admin\model\AlternateImg as AlternateImgModel;
use catetree\Catetree;

class AlternateImg extends Base
{
    public function lists() {
        $alternateImg = new AlternateImgModel();
        if (request()->isPost()) {
            $data = input('post.');
            $cateTree = new Catetree();
            $cateTree->cateSort($data['sort'],$alternateImg);
            $this->success('排序成功');
        }
        $alternateImgList = $alternateImg->getalternateImgList(4);
        $this->assign('alternateImgList',$alternateImgList);
        return view();
    }

    public function add() {
        if (Request()->isPost()) {
            $alternateImg = new AlternateImgModel();
            $data = input('post.');
            if (stripos($data['link_url'],'http://') === false) {
                $data['link_url'] = 'http://'.$data['link_url'];
            }
            // 获取上传图片的名称
            if ($_FILES['img_src']['tmp_name']) {
                $data['img_src'] = $this->upload();
            }

//            $validate = validate('alternateImg');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $alternateImg->addalternateImg($data);
            if ($result !== false) {
                $this->success('添加轮播图成功','lists');
            } else {
                $this->error('添加轮播图失败');
            }
        }
        return view();
    }

    public function edit($id) {
        $alternateImg = new AlternateImgModel();
        if (request()->isPost()) {
            $data = input('post.');
            // 给url加前缀
            if ($data['link_url'] && stripos($data['link_url'],'http://') === false) {
                $data['link_url'] = 'http://' . $data['link_url'];
            }

            // 是否上传图片
            if ($_FILES['img_src']['name']) {
                // 获取旧照片
                $oldalternateImg = $alternateImg->getImgByID($id);
                // 拼接存放图片的路径
                $oldalternateImgImg = IMG_UPLOADS.DS.$oldalternateImg['img_src'];
                if(file_exists($oldalternateImgImg)) {
                    // 删除
                    @unlink($oldalternateImgImg);
                }
                // 重新上传
                $data['img_src'] = $this->upload();
            } else {
                // 获取旧照片
                $oldalternateImg = $alternateImg->getImgByID($id);
                // 拼接路径
                $oldalternateImgImg = $oldalternateImg['img_src'];
                $data['img_src'] = $oldalternateImgImg;
            }

            // 验证
//            $validate = validate('alternateImg');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            // 执行修改
            $result = $alternateImg->savrAeditalternateImgByID($data,$data['id']);
            if ($result !== false) {
                $this->success('修改轮播图成功','lists');
            } else {
                $this->error('修改轮播图失败');
            }
        }
        $alternateImgData = $alternateImg->getalternateImgByID($id);
        $this->assign('alternateImgData',$alternateImgData);
        return view();
    }

    public function del($id) {
        $alternateImg = new AlternateImgModel();
        $imgSrc = $alternateImg->getImgByID($id);
        $delImg = IMG_UPLOADS . DS . $imgSrc['img_src'];
        if (file_exists($delImg)) {
            @unlink($delImg);
        }
        $result = $alternateImg->delalternateImgByID($id);
        if ($result) {
            $this->success('删除商品品牌成功','lists');
        } else {
            $this->error('删除商品品牌失败');
        }
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('img_src');
        // 移动到框架应用根目录/uploads/ 目录下
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'uploads');
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $newInfo = str_replace('\\','/',$info->getSaveName());
            return $newInfo;

        }else{
            // 上传失败获取错误信息
            echo $file->getError();
            die;
        }
    }
}