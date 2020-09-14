<?php


namespace app\admin\controller;
use app\admin\model\Recpos as RecposModel;
use catetree\Catetree;

class Recpos extends Base
{
    public function lists() {
        if (request()->isPost()) {
            $cateTree = new Catetree();
            $Recpos = new RecposModel;
            $sort = input('post.');

            $cateTree->cateSort($sort['sort'],$Recpos);
            $this->success('排序成功！');
        }
        $Recpos = new RecposModel();
        $RecposList = $Recpos->getRecposListPage();
        $this->assign('recposList',$RecposList);
        return view();
    }

    public function add() {
        if (Request()->isPost()) {
            $Recpos = new RecposModel();
            $data = input('post.');

//            $validate = validate('Recpos');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $Recpos->savrAeditRecposByID($data);
            if ($result !== false) {
                $this->success('添加推荐位成功','lists');
            } else {
                $this->error('添加推荐位失败');
            }
        }
        return view();
    }

    public function edit($id) {
        $Recpos = new RecposModel();
        if (request()->isPost()) {
            $data = input('post.');

            // 验证
//            $validate = validate('Recpos');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            // 执行修改
            $result = $Recpos->savrAeditRecposByID($data,$data['id']);
            if ($result !== false) {
                $this->success('修改推荐位成功','lists');
            } else {
                $this->error('修改推荐位失败');
            }
        }
        $RecposData = $Recpos->getRecposByID($id);
        $this->assign('recposData',$RecposData);
        return view();
    }

    public function del($id) {
        $Recpos = new RecposModel();
        $result = $Recpos->delRecposByID($id);
        if ($result) {
            $this->success('删除推荐位成功','lists');
        } else {
            $this->error('删除推荐位失败');
        }
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('Recpos_img');
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