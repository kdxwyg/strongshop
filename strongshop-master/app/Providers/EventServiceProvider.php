<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Events\CreatedOrder;
use App\Events\OrderPaid;
use App\Events\OrderClosed;
use App\Events\OrderCanceled;
use App\Events\OrderShipped;
use App\Events\OrderReceived;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //注册成功
        Registered::class => [
            \App\Listeners\User\SendRegisteredNotification::class, //发送`注册成功`通知和验证邮件
            \App\Listeners\User\UserSourceRecord::class, //记录用户来源
        ],
        //登录成功
        Login::class => [
            \App\Listeners\UpdateShoppingCart::class, //更新购物车
        ],
        //重置密码成功
        PasswordReset::class => [
        ],
        //下单成功
        CreatedOrder::class => [
            \App\Listeners\Order\OrderSourceRecord::class, //记录订单来源
            \App\Listeners\Order\SendOrderCreatedNotification::class, //发送通知
        ],
        //支付成功
        OrderPaid::class => [
            \App\Listeners\Order\SendOrderPaidNotification::class, //发送通知
        ],
        //订单关闭（管理员关闭、超时未付款系统自动关闭）
        OrderClosed::class => [
            \App\Listeners\Order\SendOrderClosedNotification::class, //发送通知
        ],
        //订单取消（会员主动取消）
        OrderCanceled::class => [
        ],
        //确认发货
        OrderShipped::class => [
            \App\Listeners\Order\SendOrderShippedNotification::class, //发送通知
            \App\Listeners\Order\IncrementProductSaleNum::class, //累计产品销量和减库存
        ],
        //确认收货（会员确认、系统自动确认）
        OrderReceived::class => [
        ],
    ];

    /**
     * 需要注册的订阅者类。
     *
     * @var array
     */
    protected $subscribe = [
        'App\Listeners\StrongShopEventSubscriber',
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }

}
