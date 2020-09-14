<?php


namespace app\index\controller;
use app\index\model\Brand as BrandModel;


class Brand extends Base
{
    public function listAjax() {
        $brand = new BrandModel();
        $brandRes = $brand->getBrandListPage(17);
        $data = [];
        // 总条数
        $data['totalPage'] = $brandRes->lastPage();
        // 显示对应的详细信息列表
        $brands = $brandRes->items();
        $html = '<div class="brand-list" id="recommend_brands">
                            <ul>';
        $html .= '';
        foreach ($brands as $k => $v) {
            $html .= '<li>
                                    <div class="brand-img">
                                        <a href="brandn.php?id=204" target="_blank">
                                            <img src="'.config('view_replace_str.__UPLOADS__').'/'.$v['brand_img'].'">
                                        </a>
                                    </div>
                                    <div class="brand-mash">
                                        <div data-bid="204" ectype="coll_brand"><i class="iconfont icon-zan-alt"></i></div>
                                        <div class="coupon">
                                            <a href="brandn.php?id=204" target="_blank">关注人数<br>
                                            <div id="collect_count_204">0</div></a>
                                        </div>
                                    </div>
                                </li>';
        }


        $html .= '</ul>
                            <a href="javascript:void(0);" ectype="changeBrand" class="refresh-btn"><i
                                    class="iconfont icon-rotate-alt"></i><span>换一批</span></a>
                        </div>';
        $data['brands'] = $html;
        return json($data);
    }
}