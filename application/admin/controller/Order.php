<?php


namespace app\admin\controller;
use app\admin\model\Order as OrderModel;

class Order extends Base
{
    public function lists() {
        $order = new OrderModel();
        $orderList = $order->getorderList();
        $this->assign('orderList',$orderList);
        return view();
    }

    public function add() {
        if (Request()->isPost()) {
            $order = new orderModel();
            $data = input('post.');
            if (stripos($data['order_url'],'http://') === false) {
                $data['order_url'] = 'http://'.$data['order_url'];
            }
            // 获取上传图片的名称
            if ($_FILES['order_img']['name']) {
                $data['order_img'] = $this->upload();
            }

            $validate = validate('order');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            $result = $order->addorder($data);
            if ($result !== false) {
                $this->success('添加商品品牌成功','lists');
            } else {
                $this->error('添加商品品牌失败');
            }
        }
        return view();
    }

    public function edit($id) {
        $order = new orderModel();
        if (request()->isPost()) {
            $data = input('post.');
            // 给url加前缀
            if ($data['order_url'] && stripos($data['order_url'],'http://') === false) {
                $data['order_url'] = 'http://' . $data['order_url'];
            }
            // 是否上传图片
            if ($_FILES['order_img']['name']) {
                // 获取旧照片
                $oldorder = $order->getImgByID($id);
                // 拼接路径
                $oldorderImg = IMG_UPLOADS.DS.$oldorder['order_img'];
                if(file_exists($oldorderImg)) {
                    @unlink($oldorderImg);
                }
                $data['order_img'] = $this->upload();
            } else {
                // 获取旧照片
                $oldorder = $order->getImgByID($id);
                // 拼接路径
                $oldorderImg = $oldorder['order_img'];
                $data['order_img'] = $oldorderImg;
            }

            // 验证
            $validate = validate('order');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            // 执行修改
            $result = $order->savrAeditorderByID($data,$data['id']);
            if ($result !== false) {
                $this->success('修改商品品牌成功','lists');
            } else {
                $this->error('修改商品品牌失败');
            }
        }
        $orderData = $order->getorderByID($id);
        $this->assign('orderData',$orderData);
        return view();
    }

    public function del($id) {
        $order = new orderModel();
        $result = $order->delorderByID($id);
        if ($result) {
            $this->success('删除商品品牌成功','lists');
        } else {
            $this->error('删除商品品牌失败');
        }
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('order_img');
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