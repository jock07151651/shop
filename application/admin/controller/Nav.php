<?php


namespace app\admin\controller;
use app\admin\model\Nav as NavModel;
use catetree\Catetree;

class Nav extends Base
{
    public function lists() {
        if (request()->isPost()) {
            $cateTree = new Catetree();
            $nav = new NavModel;
            $sort = input('post.');

            $cateTree->cateSort($sort['sort'],$nav);
            $this->success('排序成功！');
        }
        $nav = new NavModel();
        $navList = $nav->getNavListPage();
        $this->assign('navList',$navList);
        return view();
    }

    public function add() {
        if (Request()->isPost()) {
            $nav = new NavModel();
            $data = input('post.');
            if (stripos($data['nav_url'],'http://') === false) {
                $data['nav_url'] = 'http://'.$data['nav_url'];
            }

//            $validate = validate('nav');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $nav->savrAeditNavByID($data);
            if ($result !== false) {
                $this->success('添加导航成功','lists');
            } else {
                $this->error('添加导航失败');
            }
        }
        return view();
    }

    public function edit($id) {
        $nav = new navModel();
        if (request()->isPost()) {
            $data = input('post.');
            // 给url加前缀
            if ($data['nav_url'] && stripos($data['nav_url'],'http://') === false) {
                $data['nav_url'] = 'http://' . $data['nav_url'];
            }


            // 验证
//            $validate = validate('nav');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            // 执行修改
            $result = $nav->savrAeditnavByID($data,$data['id']);
            if ($result !== false) {
                $this->success('修改导航成功','lists');
            } else {
                $this->error('修改导航失败');
            }
        }
        $navData = $nav->getnavByID($id);
        $this->assign('navData',$navData);
        return view();
    }

    public function del($id) {
        $nav = new navModel();
        $result = $nav->delnavByID($id);
        if ($result) {
            $this->success('删除导航成功','lists');
        } else {
            $this->error('删除导航失败');
        }
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('nav_img');
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