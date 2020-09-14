<?php


namespace app\admin\controller;
use app\admin\model\Type as TypeModel;
use app\admin\model\Attr as AttrModel;

class Type extends Base
{
    public function lists() {
        $type = new typeModel();
        $typeList = $type->gettypeList();
        $this->assign('typeList',$typeList);
        return view();
    }

    public function add() {
        if (Request()->isPost()) {
            $type = new TypeModel();
            $data = input('post.');

//            $validate = validate('type');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $type->addandSaveType($data);
            if ($result !== false) {
                $this->success('添加商品类型成功','lists');
            } else {
                $this->error('添加商品类型失败');
            }
        }
        return view();
    }

    public function edit($id) {
        $type = new typeModel();
        if (request()->isPost()) {
            $data = input('post.');
            // 验证
//            $validate = validate('type');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            // 执行修改
            $result = $type->addandSaveType($data,$data['id']);
            if ($result !== false) {
                $this->success('修改商品类型成功','lists');
            } else {
                $this->error('修改商品类型失败');
            }
        }
        $typeData = $type->gettypeByID($id);
        $this->assign('typeData',$typeData);
        return view();
    }

    public function del($id) {
        $type = new typeModel();
        $attr = new AttrModel();
        // 删除商品类型时，删除类型对应的属性
        $attr->delTypeID($id);
        $result = $type->deltypeByID($id);
        if ($result) {
            $this->success('删除商品类型成功','lists');
        } else {
            $this->error('删除商品类型失败');
        }
    }

}