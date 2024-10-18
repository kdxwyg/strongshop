<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Repositories\AppRepository;

/**
 * 配置视图全局变量
 */
class GlobalStrongShopComposer
{

    public $user;
    public $user_id;
    public $cart;

    public function __construct()
    {
        if (!app('strongshop')->isWeb())
        {
            return;
        }
    }

    /**
     * 将数据绑定到视图
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (!app('strongshop')->isWeb())
        {
            return;
        }
        $view->with('_current_currency', AppRepository::getCurrentCurrency()); //当前货币code
        $view->with('_current_currency_name', AppRepository::getCurrentCurrencyName()); //当前货币名称
    }

}
