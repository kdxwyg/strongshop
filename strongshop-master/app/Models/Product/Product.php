<?php

/**
 * StrongShop
 * @author Karen <strongshop@qq.com>
 * @license http://www.strongshop.cn/license/
 * @copyright StrongShop Software
 */

namespace App\Models\Product;

use App\Models\Admin\Product\Product as Model;
use App\Repositories\AppRepository;

class Product extends Model
{
    //use \Laravel\Scout\Searchable;

    /**
     * 配置模型索引 「scout 搜索」
     * 默认情况下，每个模型都会被持久化到与模型的 「表」 名（通常是模型名称的复数形式）相匹配的索引。
     * @return string
     */
    public function searchableAs(): string
    {
        return $this->getTable() . '_type';
    }

    /**
     * 配置可搜索数据 「scout 搜索」
     * 默认情况下，模型以完整的 toArray 格式持久化到搜索索引。
     * @return array
     */
    public function toSearchableArray(): array
    {
        return $this->only(['id', 'title', 'intro', 'meta_keywords', 'meta_description', 'spu', 'sku', 'status']);
    }

    /**
     * 售价 (按汇率转换) （访问器）
     * （访问器 & 修改器 https://learnku.com/docs/laravel/6.x/eloquent-mutators/5179#defining-an-accessor）
     * @param type $value
     * @return type
     */
    public function getSalePriceAttribute($value)
    {
        return AppRepository::convertCurrency($value);
    }

    /**
     * 原价 (按汇率转换)
     * @param type $value
     * @return type
     */
    public function getOriginalPriceAttribute($value)
    {
        return AppRepository::convertCurrency($value);
    }

    /**
     * 批发价格 (按汇率转换)
     * @param type $value
     * @return type
     */
    public function getWholesaleSetAttribute($value)
    {
        if (!$value)
        {
            return $value;
        }
        $wholesale_set = json_decode($value, 1);
        foreach ($wholesale_set['price'] as $key => $val)
        {
            $wholesale_set['price'][$key] = AppRepository::convertCurrency($val);
        }
        return $wholesale_set;
    }

    /**
     * 封面图
     */
    public function getImgCoverAttribute($value)
    {
        if (!$value)
        {
            return $value;
        }
        $thumb_suffix_name = config('strongshop.productImage.thumb.suffix_name');
        $path_arr = pathinfo($value); //路径信息
        $newpath = "{$path_arr['dirname']}/{$path_arr['filename']}{$thumb_suffix_name}.{$path_arr['extension']}";
        return asset($newpath);
    }

    /**
     * 规格图
     */
    public function getImgSpecAttribute($value)
    {
        if (!$value)
        {
            return $value;
        }
        $thumb_suffix_name = config('strongshop.productImage.thumb.suffix_name');
        $path_arr = pathinfo($value); //路径信息
        $newpath = "{$path_arr['dirname']}/{$path_arr['filename']}{$thumb_suffix_name}.{$path_arr['extension']}";
        return asset($newpath);
    }

    /**
     * 相册图 [绝对路径]
     */
    public function getAssetImgPhotosAttribute($value = null)
    {
        if (!$value)
        {
            $value = $this->getAttribute('img_photos');
        }
        if (!$value)
        {
            return $value;
        }
        $datas = json_decode($value, 1);
        foreach ($datas as &$data)
        {
            $src = $data['src'];
            $thumb_suffix_name = config('strongshop.productImage.thumb.suffix_name');
            $path_arr = pathinfo($src); //路径信息
            $newpath = "{$path_arr['dirname']}/{$path_arr['filename']}{$thumb_suffix_name}.{$path_arr['extension']}";
            $data['src_thumb'] = asset($newpath);
            $data['src'] = asset($src);
        }
        return $datas;
    }

    public function getTitleAttribute($value)
    {
        if (!$value)
        {
            return $value;
        }
        $lang_front = app('strongshop')->getCurrentLanguage();
        $arr = json_decode($value, true);
        if (!$arr)
        {
            return $value;
        }
        return $arr[$lang_front] ?? array_shift($arr);
    }

    public function getIntroAttribute($value)
    {
        if (!$value)
        {
            return $value;
        }
        $lang_front = app('strongshop')->getCurrentLanguage();
        $arr = json_decode($value, true);
        if (!$arr)
        {
            return $value;
        }
        return $arr[$lang_front] ?? array_shift($arr);
    }

    public function getDetailsAttribute($value)
    {
        if (!$value)
        {
            return $value;
        }
        $lang_front = app('strongshop')->getCurrentLanguage();
        $arr = json_decode($value, true);
        if (!$arr)
        {
            return $value;
        }
        return $arr[$lang_front] ?? array_shift($arr);
    }

    /**
     * 所属产品分类
     * （模型关联 https://learnku.com/docs/laravel/6.x/eloquent-relationships/5177#many-to-many）
     * @return type
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category', 'product_id', 'category_id');
    }

    /**
     * 拥有的产品规格
     */
    public function specs()
    {
        return $this->belongsToMany(Spec::class, 'product_spec', 'product_id', 'spec_id')->withPivot('spec_value');
    }

}
