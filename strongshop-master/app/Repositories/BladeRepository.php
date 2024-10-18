<?php

/**
 * StrongShop
 * @author Karen <strongshop@qq.com>
 * @license http://www.strongshop.cn/license/
 * @copyright StrongShop Software
 */

namespace App\Repositories;

use Illuminate\Support\Facades\Blade;

class BladeRepository
{

    /**
     * 设置 price blade 指令
     */
    public static function setPriceBlade()
    {
        Blade::directive('price', function ($price) {
            return "<?php echo \App\Repositories\AppRepository::getCurrentCurrencyName() . ' ' . $price; ?>";
        });
    }

}
