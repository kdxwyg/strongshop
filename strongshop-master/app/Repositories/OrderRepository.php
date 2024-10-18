<?php

/**
 * StrongShop
 * @author Karen <strongshop@qq.com>
 * @license http://www.strongshop.cn/license/
 * @copyright StrongShop Software
 */

namespace App\Repositories;

use App\Repositories\AppRepository;
use App\Models\Order\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OrderRepository
{

    /**
     * 生成订单编号
     */
    public static function generateOrderNo()
    {
        mt_srand((double) microtime() * 1000000);
        $str = 'ST';
        $str .= date('YmdHi') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        return $str;
    }

    /**
     * 设置订单为已支付（成功支付）
     * @param type $order_no 订单编号
     * @param type $transaction_id 交易id
     * @param type $paid_amount 实际支付金额
     * @param type $paid_currency 实际支付货币
     * @return type
     */
    public static function paid($order_no, $transaction_id, $paid_amount, $paid_currency)
    {
        $order = Order::where('order_no', $order_no)->first();
        if (!$order)
        {
            if (config('app.debug') || env('PAYMENT_LOG'))
            {
                Log::debug('payment notify $order_no,$transaction_id,$paid_amount: ', [$order_no, $transaction_id, $paid_amount]);
            }
            return;
        }

        if ($order->order_status === Order::STATUS_PAID)
        {
            if (config('app.debug') || env('PAYMENT_LOG'))
            {
                Log::debug('payment notify $order_no,$transaction_id,$paid_amount:已支付 ', [$order_no, $transaction_id, $paid_amount]);
            }
            return;
        }
        if ($paid_currency === $order->currency_code)
        {
            if (config('app.debug') || env('PAYMENT_LOG'))
            {
                Log::debug("111 paid_currency：{$paid_currency}，order->currency_code：{$order->currency_code}");
            }
            $order_amount = $order->order_amount; //订单金额
        } else
        {
            $order_amount = AppRepository::convertCurrencyToDefault($order->order_amount, $order->currency_rate);
            if (config('app.debug') || env('PAYMENT_LOG'))
            {
                Log::debug("222 paid_currency：{$paid_currency}，order->currency_code：{$order->currency_code}");
            }
        }

        //验证实际支付金额和订单金额是否一致
        if ($paid_amount != $order_amount)
        {
            if (config('app.debug') || env('PAYMENT_LOG'))
            {
                Log::debug("验证实际支付金额和订单金额不一致 order_amount：{$order_amount}，paid_amount：{$paid_amount}");
            }
            $order->order_status = Order::STATUS_PAY_EXCEPTION; //支付异常
            $order->paid_amount = $paid_amount;
            $order->transaction_id = $transaction_id;
            $order->paid_at = now();
            $order->pay_remark = '实际支付金额和订单金额不一致';
            $order->save();
            return true;
        }

        $order->order_status = Order::STATUS_PAID;
        $order->paid_amount = $paid_amount;
        $order->transaction_id = $transaction_id;
        $order->paid_at = now();
        $order->save();

        event(new \App\Events\OrderPaid($order));

        return true;
    }

    /**
     * 支付异常
     * @param type $order_no
     * @param type $desc
     * @return boolean
     */
    public static function exception($order_no, $desc = '支付异常')
    {
        $order = Order::where('order_no', $order_no)->first();
        if (!in_array($order->order_status, [Order::STATUS_UNPAID]))
        {
            return true;
        }
        $order->order_status = Order::STATUS_PAY_EXCEPTION;
        $order->pay_remark = $desc;
        return $order->save();
    }

    /**
     * 设置订单为（支付失败）
     * @param type $order_no
     * @param type $desc
     * @return type
     */
    public static function failed($order_no, $desc = '')
    {
        $order = Order::where('order_no', $order_no)->first();
        if (!in_array($order->order_status, [Order::STATUS_UNPAID]))
        {
            return true;
        }
        $order->order_status = Order::STATUS_PAY_FAILED;
        $order->transaction_id = $desc;
        return $order->save();
    }

}
