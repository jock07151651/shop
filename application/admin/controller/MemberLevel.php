<?php


namespace app\admin\controller;
use app\admin\model\MemberLevel as MemberLevelModel;
use app\admin\model\Attr as AttrModel;

class MemberLevel extends Base
{
    public function lists() {
        $memberLevel = new memberLevelModel();
        $memberLevelList = $memberLevel->getmemberLevelList();
        $this->assign('memberLevelList',$memberLevelList);
        return view();
    }

    public function add() {
        if (Request()->isPost()) {
            $memberLevel = new memberLevelModel();
            $data = input('post.');

//            $validate = validate('memberLevel');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $memberLevel->addandSavememberLevel($data);
            if ($result !== false) {
                $this->success('添加会员成功','lists');
            } else {
                $this->error('添加会员失败');
            }
        }
        return view();
    }

    public function edit($id) {
        $memberLevel = new memberLevelModel();
        if (request()->isPost()) {
            $data = input('post.');
            // 验证
//            $validate = validate('memberLevel');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            // 执行修改
            $result = $memberLevel->addandSavememberLevel($data,$data['id']);
            if ($result !== false) {
                $this->success('修改会员成功','lists');
            } else {
                $this->error('修改会员失败');
            }
        }
        $memberLevelData = $memberLevel->getmemberLevelByID($id);
        $this->assign('memberLevelData',$memberLevelData);
        return view();
    }

    public function del($id) {
        $memberLevel = new memberLevelModel();
        $result = $memberLevel->delmemberLevelByID($id);
        if ($result) {
            $this->success('删除会员成功','lists');
        } else {
            $this->error('删除会员失败');
        }
    }

}