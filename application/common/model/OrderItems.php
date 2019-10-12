<?php

namespace app\common\model;

/**
 * 订单子表
 * Class OrderItems
 * @package app\common\model
 * @author keinx
 */
class OrderItems extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'utime';
    protected $updateTime = 'utime';


    /**
     * 更改发货数量
     * @param $order_id
     * @param $ship_data
     * @return string
     * @throws \think\Exception
     */
    public function ship($order_id, $ship_data)
    {
        foreach ($ship_data as $k => $v) {
            $this->where('order_id', 'eq', $order_id)
                ->where('id', $v[0])
                ->setInc('sendnums', $v[1]);
        }
        return '';
    }


    /**
     * 查询订单是否部分发货
     * @param $order_id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isAllShip($order_id)
    {
        $return = 'all';
        $orderModel = new Order();
        $orderInfo = $orderModel::with('items')
            ->field('order_id,user_id')
            ->where('order_id', 'eq', $order_id)
            ->find();
        $orderModel->aftersalesVal($orderInfo);

        if (isset($orderInfo['items']) && count($orderInfo['items']) > 0) {
            foreach ($orderInfo['items'] as $p) {
                $remainingNum = $p['nums'] - $p['sendnums'] - $p['reship_nums'];
                if ($remainingNum > 0) {
                    $return = 'section';
                }
            }
        }

        return $return;
    }


}