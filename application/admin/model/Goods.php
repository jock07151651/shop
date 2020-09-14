<?php

namespace app\admin\model;

class Goods extends Base
{
    protected $field = true; // 忽略该模型不存在字段

    // 新增前执行
    protected static function init()
    {
        // 新增前
        Goods::beforeInsert(function ($goods) {

            // 新增前上传缩略图
            if ($_FILES['og_thumb']['tmp_name']) {
                $ThumbName = $goods->upload('og_thumb');
                $ogThumb = date('Ymd') . DS . $ThumbName;
                $bigThumb = date('Ymd') . DS . 'big_' . $ThumbName;
                $midThumb = date('Ymd') . DS . 'mid_' . $ThumbName;
                $smThumb = date('Ymd') . DS . 'sm_' . $ThumbName;
                $image = \think\Image::open(IMG_UPLOADS . DS . $ogThumb);
                // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
                $image->thumb(500, 500)->save(IMG_UPLOADS . DS . $bigThumb);
                $image->thumb(240, 240)->save(IMG_UPLOADS . DS . $midThumb);
                $image->thumb(58, 58)->save(IMG_UPLOADS . DS . $smThumb);
                $goods->og_thumb = $ogThumb;
                $goods->big_thumb = $bigThumb;
                $goods->mid_thumb = $midThumb;
                $goods->sm_thumb = $smThumb;
            }

            $goods->goods_code = time() . rand(111111, 999999);    // 商品编号
        });

        // 新增后
        Goods::afterInsert(function ($goods) {

            // 获取新增商品的id
            $mpriceArr = $goods->mp;
            $goodsID = $goods->id;

            // 添加商品推荐位
            $resData = input('post.');
            if (!empty($resData['recpos'])) {
                foreach ($resData['recpos'] as $k => $v) {
                    db('rec_item')->insert(['recpos_id'=>$v,'value_id'=>$goodsID,'value_type'=>1]);
                }
            }

            // 批量写入会员价格
            if ($mpriceArr) {
                foreach ($mpriceArr as $k => $v) {
                    if (trim($v) == '') {
                        continue;
                    } else {
                        db('member_price')->insert(['mlevel_id' => $k, 'mpprice' => $v, 'goods_id' => $goodsID]);
                    }
                }
            }

            // 新增商品属性值
            $goodsData = input('post.');
            $i = 0;
            if (isset($goodsData['goods_attr'])) {
                $attrPrice = $goodsData['attr_price'];
                // 遍历，$k是attr表的id，$v如果是唯一选就是字符串，否则是数组
                foreach ($goodsData['goods_attr'] as $k => $v) {
                    if (is_array($v)) {
                        if (!empty($v)) {
                            // k1对应数组的下标，$v1对应属性值
                            foreach ($v as $k1 => $v1) {
                                if (!$v1) {
                                    $i++;
                                    continue;
                                }
                                db('goods_attr')->insert([
                                    'attr_id' => $k, 'attr_value' => $v1,
                                    'goods_id' => $goodsID, 'attr_price' => $attrPrice[$i]
                                ]);
                                $i++;
                            }
                        }
                    } else {
                        db('goods_attr')->insert(['attr_id' => $k, 'attr_value' => $v, 'goods_id' => $goodsID]);
                    }
                }
            }

            // 商品相册处理
            if ($goods->_hasImgs($_FILES['goods_photo']['tmp_name'])) {
                // 获取表单上传文件
                $files = request()->file('goods_photo');

                foreach ($files as $file) {
                    // 移动到框架应用根目录/uploads/ 目录下
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'uploads');
                    if ($info) {
                        // 成功上传后 获取上传信息
                        // 输出 42a79759f284b767dfcb2a0197904287.jpg
                        $PhotoName = $info->getFilename();
                        $ogPhoto = date('Ymd') . DS . $PhotoName; // 原图
                        $bigPhoto = date('Ymd') . DS . 'big_' . $PhotoName; // 缩略图
                        $midPhoto = date('Ymd') . DS . 'mid_' . $PhotoName;
                        $smPhoto = date('Ymd') . DS . 'sm_' . $PhotoName;
                        $image = \think\Image::open(IMG_UPLOADS . DS . $ogPhoto);
                        // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
                        $image->thumb(500, 500)->save(IMG_UPLOADS . DS . $bigPhoto);
                        $image->thumb(250, 250)->save(IMG_UPLOADS . DS . $midPhoto);
                        $image->thumb(80, 80)->save(IMG_UPLOADS . DS . $smPhoto);
                        db('goods_photo')->insert([
                            'og_photo' => $ogPhoto, 'sm_photo' => $smPhoto,
                            'mid_photo' => $midPhoto, 'big_photo' => $bigPhoto,
                            'goods_id' => $goodsID
                        ]);
                    } else {
                        // 上传失败获取错误信息
                        echo $file->getError();
                    }
                }
            }
        });

        // 修改前
        Goods::beforeUpdate(function ($goods) {
            $mpriceArr = $goods->mp;
            $goodsID = $goods->id;

            // 修改商品属性值
            $goodsData = input('post.');
            //echo '<pre/>';
            //print_r($goodsData);die;
            if (isset($goodsData['old_goods_attr'])) {
                $i = 0;
                $attrPrice = $goodsData['old_attr_price'];
                // 将$attrPrice的键作为值，键从0开始
                $idsArr = array_keys($attrPrice);
                // 将$attrPrice的值作为值，键从0开始
                $valArr = array_values($attrPrice);

                // 遍历，$k是attr表的id，$v如果是唯一选就是字符串，否则是数组
                foreach ($goodsData['old_goods_attr'] as $k => $v) {
                    if (is_array($v)) {
                        if (!empty($v)) {
                            // k1对应数组的下标，$v1对应属性值
                            foreach ($v as $k1 => $v1) {
                                if (!$v1) {
                                    $i++;
                                    continue;
                                }
                                db('goods_attr')->where('id',$idsArr[$i])->update([
                                    'attr_value' => $v1,'attr_price' => $valArr[$i]
                                ]);
                                $i++;
                            }
                        }
                    } else {
                        db('goods_attr')->where('id',$idsArr[$i])->update([
                            'attr_value'=> $v,'attr_price' => $valArr[$i],
                        ]);
                        $i++;
                    }
                }
            }

            // 商品相册处理
            if ($goods->_hasImgs($_FILES['goods_photo']['tmp_name'])) {
                // 获取表单上传文件
                $files = request()->file('goods_photo');

                foreach ($files as $file) {
                    // 移动到框架应用根目录/uploads/ 目录下
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'uploads');
                    if ($info) {
                        // 成功上传后 获取上传信息
                        // 输出 42a79759f284b767dfcb2a0197904287.jpg
                        $PhotoName = $info->getFilename();
                        $ogPhoto = date('Ymd') . DS . $PhotoName; // 原图
                        $bigPhoto = date('Ymd') . DS . 'big_' . $PhotoName; // 缩略图
                        $midPhoto = date('Ymd') . DS . 'mid_' . $PhotoName;
                        $smPhoto = date('Ymd') . DS . 'sm_' . $PhotoName;
                        $image = \think\Image::open(IMG_UPLOADS . DS . $ogPhoto);
                        // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
                        $image->thumb(500, 500)->save(IMG_UPLOADS . DS . $bigPhoto);
                        $image->thumb(250, 250)->save(IMG_UPLOADS . DS . $midPhoto);
                        $image->thumb(80, 80)->save(IMG_UPLOADS . DS . $smPhoto);
                        db('goods_photo')->insert([
                            'og_photo' => $ogPhoto, 'sm_photo' => $smPhoto,
                            'mid_photo' => $midPhoto, 'big_photo' => $bigPhoto,
                            'goods_id' => $goodsID
                        ]);
                    } else {
                        // 上传失败获取错误信息
                        echo $file->getError();
                    }
                }
            }

            // 修改时，新增商品属性值
            $goodsData = input('post.');

            // 修改前删除对应商品的推荐位
            db('rec_item')->where(['value_id'=>$goodsID,'value_type'=>1])->delete();
            // 修改前添加商品推荐位
            if (!empty($goodsData['recpos'])) {
                foreach ($goodsData['recpos'] as $k => $v) {
                    db('rec_item')->insert(['recpos_id'=>$v,'value_id'=>$goodsID,'value_type'=>1]);
                }
            }


            if (isset($goodsData['goods_attr'])) {
                $i = 0;
                $attrPrice = $goodsData['attr_price'];
                // 遍历，$k是attr表的id，$v如果是唯一选就是字符串，否则是数组
                foreach ($goodsData['goods_attr'] as $k => $v) {
                    if (is_array($v)) {
                        if (!empty($v)) {
                            // k1对应数组的下标，$v1对应属性值
                            foreach ($v as $k1 => $v1) {
                                if (!$v1) {
                                    $i++;
                                    continue;
                                }
                                db('goods_attr')->insert([
                                    'attr_id' => $k, 'attr_value' => $v1,
                                    'goods_id' => $goodsID, 'attr_price' => $attrPrice[$i]
                                ]);
                                $i++;
                            }
                        }
                    } else {
                        db('goods_attr')->insert(['attr_id' => $k, 'attr_value' => $v, 'goods_id' => $goodsID]);
                    }
                }
            }

            // 批量写入会员价格
            db('member_price')->where('goods_id', $goodsID)->delete();
            if ($mpriceArr) {
                foreach ($mpriceArr as $k => $v) {
                    if (trim($v) == '') {
                        continue;
                    } else {
                        db('member_price')->insert(['mlevel_id' => $k, 'mpprice' => $v, 'goods_id' => $goodsID]);
                    }
                }
            }
            if ($_FILES['og_thumb']['tmp_name']) {
                // 删除旧图片
                @unlink(IMG_UPLOADS . DS . $goods->og_thumb);
                @unlink(IMG_UPLOADS . DS . $goods->big_thumb);
                @unlink(IMG_UPLOADS . DS . $goods->mid_thumb);
                @unlink(IMG_UPLOADS . DS . $goods->sm_thumb);

                // 上传新图片
                $ThumbName = $goods->upload('og_thumb');
                $newOgThumb = date('Ymd') . DS . $ThumbName;
                $newBigThumb = date('Ymd') . DS . 'big_' . $ThumbName;
                $newMidThumb = date('Ymd') . DS . 'mid_' . $ThumbName;
                $newSmThumb = date('Ymd') . DS . 'sm_' . $ThumbName;
                $image = \think\Image::open(IMG_UPLOADS . DS . $newOgThumb);
                // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
                $image->thumb(500, 500)->save(IMG_UPLOADS . DS . $newBigThumb);
                $image->thumb(250, 250)->save(IMG_UPLOADS . DS . $newMidThumb);
                $image->thumb(80, 80)->save(IMG_UPLOADS . DS . $newSmThumb);
                $goods->og_thumb = $newOgThumb;
                $goods->big_thumb = $newBigThumb;
                $goods->mid_thumb = $newMidThumb;
                $goods->sm_thumb = $newSmThumb;

            }
        });



        // 删除前
        Goods::beforeDelete(function ($goods) {
            $goodsID = $goods->id;
            // 删除商品对应的推荐位
            db('rec_item')->where(['value_id'=>$goodsID,'value_type'=>'1'])->delete();
            // 判断商品是否有原图，删除硬盘上的各种图片
            if ($goods->og_thumb) {
                $thumb = [];
                $thumb[] = IMG_UPLOADS . DS . $goods->og_thumb;
                $thumb[] = IMG_UPLOADS . DS . $goods->big_thumb;
                $thumb[] = IMG_UPLOADS . DS . $goods->mid_thumb;
                $thumb[] = IMG_UPLOADS . DS . $goods->sm_thumb;
                foreach ($thumb as $k => $v) {
                    if (file_exists($v)) {
                        @unlink($v);
                    }
                }
            }
            // 删除商品对应的会员价格
            db('member_price')->where('goods_id', $goodsID)->delete();

            // 删除商品对应的属性值
            db('goods_attr')->where('goods_id', $goodsID)->delete();

            // 查询goods_photo 商品相册
            $goodsPhoto = new GoodsPhoto();
            $photos = $goodsPhoto->where('goods_id', $goodsID)->select();
            if (!empty($photos)) {
                // 循环删除硬盘上的图片
                foreach ($photos as $k => $v) {
                    if ($v->og_photo) {
                        $photo = [];
                        $photo[] = IMG_UPLOADS . DS . $v->og_photo;
                        $photo[] = IMG_UPLOADS . DS . $v->big_photo;
                        $photo[] = IMG_UPLOADS . DS . $v->mid_photo;
                        $photo[] = IMG_UPLOADS . DS . $v->sm_photo;
                        foreach ($photo as $k1 => $v1) {
                            if (file_exists($v1)) {
                                @unlink($v1);
                            }
                        }
                    }
                }
            }
            // 删除商品相册数据
            $goodsPhoto->delPhotoByGoodID($goodsID);
        });
    }

    private function _hasImgs($tmpArr)
    {
        // 遍历文件中_FILE['goods_ptoto']['tmp_name']中的数组
        foreach ($tmpArr as $k => $v) {
            if ($v) {
                return true;
            }
        }
        return false;
    }

    public function upload($imgName)
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file($imgName);
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'uploads');
        if ($info) {
            // 成功上传后 获取上传信息

            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            return $info->getFilename();
        } else {
            // 上传失败获取错误信息
            echo $file->getError();
            die;
        }
    }

    public function addGoods($data)
    {
        return self::create($data);
    }

    public function getGoodsList($join)
    {
        return self::alias('g')->field('g.*,c.cate_name,b.brand_name,t.type_name,SUM(p.goods_stock) gn')
            ->join($join)->group('g.id')->order('id desc')->paginate(10);
    }

    public function delGoodsByID($id)
    {
        return self::destroy($id);
    }

    public function getGoodsByID($id)
    {
        return self::find($id);
    }

    public function savrAeditGoodsByID($data, $id = '')
    {
        return self::save($data, $id);
    }

    public function getImgByID($id)
    {
        return self::field('Goods_img')->find($id);
    }

}