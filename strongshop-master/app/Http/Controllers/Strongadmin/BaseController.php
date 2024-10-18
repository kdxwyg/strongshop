<?php

/**
 * StrongShop
 * @author Karen <strongshop@qq.com>
 * @license http://www.strongshop.cn/license/
 * @copyright StrongShop Software
 */
/**
 * StrongShop
 * @author Karen <strongshop@qq.com>
 * @license http://www.strongshop.cn/license/
 * @copyright StrongShop Software
 */

namespace App\Http\Controllers\Strongadmin;

use OpenStrong\StrongAdmin\Http\Controllers\BaseController as Controller;
use App;

class BaseController extends Controller
{

    public function __construct()
    {
        App::setLocale('zh-CN');
    }

    /**
     * 获取登录用户信息
     * @return User
     */
    protected function getUser()
    {
        return auth('strongadmin')->user();
    }

}
