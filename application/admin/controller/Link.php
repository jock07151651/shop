<?php


namespace app\admin\controller;
use app\admin\model\Link as linkModel;
use catetree\Catetree;

class Link extends Base
{
    public function lists() {
        $link = new linkModel();
        if (request()->isPost()) {
            $sort = input('post.');
            $cateTree = new Catetree();
            $cateTree->cateSort($sort['sort'],$link);
            $this->success('排序成功！');
        }
        $linkList = $link->getLinkList();
        $this->assign('linkList',$linkList);
        return view();
    }

    public function add() {
        if (Request()->isPost()) {
            $link = new linkModel();
            $data = input('post.');

            if (stripos($data['link_url'],'http://') === false) {
                $data['link_url'] = 'http://'.$data['link_url'];
            }
            // 获取上传图片的名称
            if ($_FILES['logo']['name']) {
                $data['logo'] = $this->upload();
            }

//            $validate = validate('link');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $link->addAndSaveLink($data);
            if ($result !== false) {
                $this->success('添加友情链接成功','lists');
            } else {
                $this->error('添加友情链接失败');
            }
        }
        return view();
    }

    public function edit($id) {
        $link = new linkModel();
        if (request()->isPost()) {
            $data = input('post.');
            // 验证http网址
            if ($data['link_url'] && stripos($data['link_url'],'http://') === false) {
                $data['link_url'] = 'http://' . $data['link_url'];
            }

            // 是否上传图片
            if ($_FILES['logo']['name']) {
                $oldLogo = $link->getLinkByID($id);
                $oldLogoImg = IMG_UPLOADS .DS. $oldLogo['logo'];
                if (file_exists($oldLogoImg)) {
                    @unlink($oldLogoImg);
                }
                $data['logo'] = $this->upload();
            } else {
                // 没有上传新图片
                $oldLogo = $link->getLinkByID($id);
                $data['logo'] = $oldLogo['logo'];
            }

            // 验证
//            $validate = validate('link');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            // 执行修改
            $result = $link->addAndSaveLink($data,$data['id']);
            if ($result !== false) {
                $this->success('修改链接成功','lists');
            } else {
                $this->error('修改连接失败');
            }
        }

        $linkData = $link->getLinkdByID($id);
        $this->assign('linkData',$linkData);
        return view();
    }

    public function del($id) {
        $link = new linkModel();
        $logo = $link->getLinkByID($id);
        if ($logo) {
            $delLogo = IMG_UPLOADS. DS . $logo['logo'];
            if (file_exists($delLogo)) {
                @unlink($delLogo);
            }
        }
        $result = $link->delLinkByID($id);
        if ($result) {
            $this->success('删除链接成功','lists');
        } else {
            $this->error('删除链接失败');
        }
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('logo');
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