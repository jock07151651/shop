<?php


namespace app\admin\controller;
use app\admin\model\Attr as AttrModel;
use app\admin\model\Type as TypeModel;

class Attr extends Base
{
    public function lists() {
        $attr = new attrModel();
        $typeID = input('type_id');
        if($typeID) {
            $map['type_id'] = ['=',$typeID];
        } else {
            $map['type_id'] = ['=',1];
        }

        $attrList = $attr->getAttrListPage($map);
        $this->assign('attrList',$attrList);
        return view();
    }

    public function add() {
        if (Request()->isPost()) {
            $attr = new attrModel();
            $data = input('post.');

            // 替换中文逗号
            if ($data['attr_values']) {
                $data['attr_values'] = str_replace('，',',',$data['attr_values']);
            }

//            $validate = validate('attr');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $attr->addAndSaveAttr($data);
            if ($result !== false) {
                $this->success('添加商品类型属性成功','Type/lists');
            } else {
                $this->error('添加商品类型属性失败');
            }
        }
        $type = new TypeModel();
        $typeList = $type->getTypeLists();
        $this->assign([
            'typeList' => $typeList,
        ]);
        return view();
    }

    public function edit($id) {
        $attr = new attrModel();
        if (request()->isPost()) {
            $data = input('post.');

            // 替换中文逗号
            if ($data['attr_values']) {
                $data['attr_values'] = str_replace('，',',',$data['attr_values']);
            }

            // 验证
//            $validate = validate('attr');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            // 执行修改
            $result = $attr->addAndSaveAttr($data,$data['id']);
            if ($result !== false) {
                $this->success('修改商品类型属性成功','Type/lists');
            } else {
                $this->error('修改商品类型属性失败');
            }
        }
        $type = new TypeModel();
        $typeList = $type->getTypeLists();
        $attrData = $attr->getAttrByID($id);
        $this->assign([
            'typeList' => $typeList,
            'attrData' => $attrData,
        ]);
        return view();
    }

    public function del($id) {
        $attr = new attrModel();
        $result = $attr->delAttrByID($id);
        if ($result) {
            $this->success('删除商品类型属性成功','Type/lists');
        } else {
            $this->error('删除商品类型属性失败');
        }
    }

    public function ajaxGetAttr() {
        $type_id = input('type_id');
        $attr = new attrModel();
        $attrList = $attr->getAttrByType_id($type_id);
        echo json_encode($attrList);
    }

}