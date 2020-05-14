<?php
$str = '{"banquetPayment":[{"order_id":"1957","user_id":434,"banquet_payment_no":"","banquet_pay_type":3,"banquet_apply_pay_date":"2020-05-12 00:00","banquet_pay_item_price":"69656","banquet_payment_remark":"7888x12\u684c=94656-\u5b9a\u91d125000=69656","banquet_pay_to_company":"\u4e0a\u6d77\u65b0\u534e\u8054\u623f\u5730\u4ea7\u5f00\u53d1\u6709\u9650\u516c\u53f8","banquet_pay_to_account":"3105 0174 3600 0000 0833","banquet_pay_to_bank":"\u5efa\u8bbe\u94f6\u884c\u4e0a\u6d77\u957f\u5b81\u652f\u884c","receipt_img":[],"note_img":[],"create_time":"2020-05-12 16:19","update_time":"2020-05-12 16:19","id":"1574"}]}';
$source = json_decode($str);

foreach ($source as $key=>$value) {

    switch ($key) {
        case 'order':
            $where = [];
            $where[] = ['id', '=', $value['id']];
            \app\common\model\Order::where($where)->update(['status'=>13]);
            break;

        case 'banquet':
            $where = [];
            $where[] = ['id', '=', $value['id']];
            \app\common\model\OrderBanquet::where($where)->update(['item_check_status'=>13]);
            break;

        case 'banquetSuborder':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            \app\common\model\OrderBanquetSuborder::where($where)->update(['item_check_status'=>13]);
            break;

        case 'banquetIncome':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            \app\common\model\OrderBanquetReceivables::where($where)->update(['item_check_status'=>13]);
            break;

        case 'banquetPayment':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            \app\common\model\OrderBanquetPayment::where($where)->update(['item_check_status'=>13]);
            break;

        case 'wedding':
            $where = [];
            $where[] = ['id', '=', $value['id']];
            \app\common\model\OrderWedding::where($where)->update(['item_check_status'=>13]);
            break;

        case 'weddingSuborder':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            \app\common\model\OrderWeddingSuborder::where($where)->update(['item_check_status'=>13]);
            break;

        case 'weddingIncome':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            \app\common\model\OrderWeddingReceivables::where($where)->update(['item_check_status'=>13]);
            break;

        case 'weddingPayment':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            \app\common\model\OrderWeddingPayment::where($where)->update(['item_check_status'=>13]);
            break;

        case 'hotelItem':
            $where = [];
            $where[] = ['id', '=', $value['id']];
            \app\common\model\OrderHotelItem::where($where)->update(['item_check_status'=>13]);
            break;

        case 'hotelProtocol':
            $where = [];
            $where[] = ['id', '=', $value['id']];
            \app\common\model\OrderHotelProtocol::where($where)->update(['item_check_status'=>13]);
            break;

        case 'car':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            $where[] = ['id', '=', $value[1]['id']];
            \app\common\model\OrderCar::where($where)->update(['item_check_status'=>13]);
            break;

        case 'wine':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            \app\common\model\OrderWine::where($where)->update(['item_check_status'=>13]);
            break;

        case 'sugar':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            \app\common\model\OrderSugar::where($where)->update(['item_check_status'=>13]);
            break;

        case 'dessert':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            \app\common\model\OrderDessert::where($where)->update(['item_check_status'=>13]);
            break;

        case 'light':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            \app\common\model\OrderLight::where($where)->update(['item_check_status'=>13]);
            break;

        case 'led':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            \app\common\model\OrderLed::where($where)->update(['item_check_status'=>13]);
            break;

        case 'd3':
            $where = [];
            $where[] = ['id', '=', $value[0]['id']];
            \app\common\model\OrderD3::where($where)->update(['item_check_status'=>13]);
            break;
    }
}