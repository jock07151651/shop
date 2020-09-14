<?php


namespace app\admin\controller;
use app\admin\model\Conf as ConfModel;

class Conf extends Base
{
    public function conflist() {
        $conf = new ConfModel();
        if (request()->isPost()) {
            $data = input('post.');
            // 获取checkbox的ename
            $checkField2D = $conf->getCheck('checkbox');
            // 自定义数组
            $checkField = [];
            // 2d是一个二维数组对象，对象里面0=》数组；数组里面，字段=》值
            foreach ($checkField2D as $k => $v) {
                // 将复选框拆解成一维数组
                $checkField[] = $v['ename'];
            }

            $allFields = [];
            // 处理文字数据
            foreach ($data as $k => $v) {
                // 将所获得的$k=ename存入数组
                $allFields[] = $k;
                if (is_array($v)) {
                    // 将复选框数组，以逗号分割维字符串
                    $value = implode(',',$v);
                    $conf->confSave($k,$value);
                } else {
                    // 不是数组直接修改
                    $conf->confSave($k,$v);
                }
            }

            // 如果复选框未选中任何一个选项，则设置为空,$k是下标，$v是ename
            foreach ($checkField as $k => $v) {
                if (!in_array($v,$allFields)) {
                    $conf->confSave($v);
                }
            }

            // 图片处理
            if($_FILES) { // $k = name
                foreach ($_FILES as $k => $v) {
                    // 是否有上传视频
                    if ($v['tmp_name']) {
                        // 通过ename查询对应数据
                        $imgs = $conf->fileSave($k);
                        if ($imgs['value']) {
                            // 删除旧图片
                            $oimg = IMG_UPLOADS . $imgs['value'];
                            if (file_exists($oimg)) {
                                @unlink($oimg);
                            }
                        }
                        $imgSrc = $this->upload($k);
                        $conf->confSave($k,$imgSrc);
                    }
                }
            }
        $this->success('修改配置项成功!');
        }
        $shopConfRes = $conf->getShopConf();
        $goodsConfRes = $conf->getGoodsConf();
        $this->assign([
            'shopConfRes' => $shopConfRes,
            'goodsConfRes' => $goodsConfRes,
        ]);
        return view();
    }

    public function lists() {
        $conf = new ConfModel();
        if (request()->isPost()) {
            $sort = input('post.');
            foreach ($sort['sort'] as $k => $v ) {
                $conf->confSort($k,$v);
            }
            $this->success('排序成功!');
        }
        $confList = $conf->getConfList();
        $this->assign('confList',$confList);
        return view();
    }

    public function add() {
        if (Request()->isPost()) {
            $Conf = new ConfModel();
            $data = input('post.');
            if ($data['form_type'] == 'radio' || $data['form_type'] == 'select' ||
                $data['form_type'] == 'checkbox' ) {
                $data['value'] = str_replace('，',',',$data['value']);
                $data['values'] = str_replace('，',',',$data['values']);
            }

//            $validate = validate('Conf');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $Conf->addandSaveConf($data);
            if ($result !== false) {
                $this->success('添加配置项成功','lists');
            } else {
                $this->error('添加配置项失败');
            }
        }
        return view();
    }

    // 配置管理修改
    public function edit($id) {
        $conf = new ConfModel();
        if (request()->isPost()) {
            $data = input('post.');

            if ($data['form_type'] == 'radio' || $data['form_type'] == 'select' ||
                $data['form_type'] == 'checkbox' ) {
                $data['value'] = str_replace('，',',',$data['value']);
                $data['values'] = str_replace('，',',',$data['values']);
            }

            // 验证
//            $validate = validate('Conf');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            // 执行修改
            $result = $conf->addandSaveConf($data,$data['id']);
            if ($result !== false) {
                $this->success('修改配置项成功','lists');
            } else {
                $this->error('修改配置项失败');
            }
        }
        $confData = $conf->getConfByID($id);
        $this->assign('confData',$confData);
        return view();
    }

    // 删除配置项，和 上传的value值，图片
    public function del($id) {
        $conf = new ConfModel();
        $fileData = $conf->getConfByID($id);
        if($fileData['form_type'] == 'file') {
            $delImg = IMG_UPLOADS.DS.$fileData['value'];
            if (file_exists($delImg)) {
                @unlink($delImg);
            }
        }
        $result = $conf->delConfByID($id);
        if ($result) {
            $this->success('删除配置项成功','lists');
        } else {
            $this->error('删除配置项失败');
        }
    }

    public function upload($file){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file($file);
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