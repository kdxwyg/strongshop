<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Repositories\CartRepository;
use App\Repositories\ShippingRepository;
use App\Repositories\RegionRepository;

class ShoppingCartComposer
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
        $this->user = app('strongshop')->user ?: null;
        $this->user_id = $this->user ? $this->user->id : null;
        $this->cart = CartRepository::getCart();
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
        $user_id = $this->user_id;
        $cart = $this->cart;
        if (config('strongshop.showShipingFeeInCart'))
        {
            if ($this->user && $this->user->country_code)
            {
                $_user_country_code = app('strongshop')->user->country_code; //会员所属国家code
            } else
            {
                $_user_country_code = config('strongshop.defaultCountry'); //默认国家code
            }
            //显示配送费用
            $shipping_fee_list = ShippingRepository::getShippingFeeList($cart['cart_weight'], $_user_country_code);
            $shipping_total = $shipping_fee_list[0]['fee'] ?? 0;
            $view->with('_countries', RegionRepository::countries()); //国家列表
            $view->with('_user_country_code', $_user_country_code); //会员所属国家code
            $view->with('_shipping_options', $shipping_fee_list); //配送方式列表
        } else
        {
            $shipping_total = false; // false 表示不显示配送费用
        }
        $cart['total']['shipping_total'] = $shipping_total;

        //购物车
        $view->with('_cart', $cart);
    }

}
