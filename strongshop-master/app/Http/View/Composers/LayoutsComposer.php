<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Repositories\AppRepository;
use App\Repositories\ProductRepository;

class LayoutsComposer
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

        //商店配置
        //$view->with('_STRONGSHOP_CONFIG', AppRepository::getShopConfig());
        //语言
        $view->with('_languages', AppRepository::getLanguages());
        //货币
        $view->with('_currencies', AppRepository::getCurrencies()); //货币列表
        //产品分类
        $view->with('_categories', ProductRepository::getCategories());
        //产品收藏总数
        $view->with('_wish_list_total', \DB::table('user_collect')->where('user_id', $user_id)->count());
        //未读反馈回复
        $view->with('_unread_feedback_replies_total', \DB::table('user_feedback')->where('user_id', $user_id)->where('status', 2)->whereNull('read_at')->count() ?: '');
    }

}
