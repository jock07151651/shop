<?php
include $wxPay.'Base.php';
include $wxPay.'phpqrcode/phpqrcode.php';
/* 
 * 童攀课堂
 * https://www.tongpankt.com/
 */

class WeiXinPay2 extends Base
{
    //1.调用统一下单API 后去二维码支付链接
    public function getQrUrl($outTradeNo,$price){
        //调用统一下单API
           $params = [
               'appid'=> self::APPID,
                'mch_id'=> self::MCHID,
                'nonce_str'=>md5(time()),
                'body'=> '扫码支付模式二',
                'out_trade_no'=> $outTradeNo,
                'total_fee'=> $price,
                'spbill_create_ip'=>$_SERVER['SERVER_ADDR'],
                'notify_url'=> self::NOTIFY,
                'trade_type'=>'NATIVE',
                'product_id'=>$outTradeNo
           ];
       $arr = $this->unifiedorder($params);
       return $arr['code_url'];
    }

}
