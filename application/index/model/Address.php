<?php


namespace app\index\model;

class Address extends BaseModel
{
    public function getUserAddress($uid) {
        return self::where('user_id',$uid)->find();
    }

    public function updateAddress($data,$uid) {
        return self::where('user_id', $uid)
            ->update([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'tel' => $data['tel'],
                'province' => $data['province'],
                'city' => $data['city'],
                'country' => $data['country'],
                'address' => $data['address'],
                'email' => $data['email'],
                'zipcode' => $data['zipcode'],
                'sign_building' => $data['sign_building'],
                'best_time' => $data['best_time'],
            ]);
    }

    public function saveAddress($data) {
        return self::save(
            [
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'phone' => $data['phone'],
                'tel' => $data['tel'],
                'province' => $data['province'],
                'city' => $data['city'],
                'country' => $data['country'],
                'address' => $data['address'],
                'email' => $data['email'],
                'zipcode' => $data['zipcode'],
                'sign_building' => $data['sign_building'],
                'best_time' => $data['best_time'],
            ]
        );
    }

}