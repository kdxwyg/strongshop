<?php

/**
 * StrongShop
 * @author Karen <strongshop@qq.com>
 * @license http://www.strongshop.cn/license/
 * @copyright StrongShop Software
 */

namespace App\Models\Admin\Product;

use App\Models\BaseModel as Model;
use App\Repositories\AppRepository;

class Product extends Model
{

    use \Illuminate\Database\Eloquent\SoftDeletes;

    public $tableComments = '产品表';
    protected $table = 'product';
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $casts = ['wholesale_set' => 'json', 'img_photos' => 'json', 'more' => 'json'];

    /**
     * Validator rules
     * @param type $on
     * @return type
     */
    public function rules()
    {
        $data = [
            'title' => ['required', 'string', 'max:255'],
            'intro' => ['nullable', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:65535'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'spu' => ['required', 'string', 'max:100'],
            'sku' => ['required', 'string', 'max:100'],
            'stock' => ['required', 'integer'],
            'stock_status' => ['required', 'integer'],
            'original_price' => ['numeric', 'gt:0', 'lte:999999'],
            'sale_price' => ['required', 'numeric', 'gt:0', 'lte:999999'],
            'img_cover' => ['required', 'string', 'max:255'],
            'img_photos' => ['required', 'array', 'min:1'],
            'img_photos.src.*' => ['required', 'string', 'max:500'],
            'long' => ['nullable', 'integer'],
            'width' => ['nullable', 'integer'],
            'high' => ['nullable', 'integer'],
            'weight' => ['nullable', 'integer'],
            'volume_weight' => ['nullable', 'integer'],
            'click_num' => ['nullable', 'integer'],
            'status' => ['required', 'integer'],
            'remark' => ['nullable', 'string', 'max:255'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
            'deleted_at' => ['date'],
            'related_sku' => ['nullable', 'string', 'max:255'],
            'related_accessories_sku' => ['nullable', 'string', 'max:999'],
            'brand_id' => ['nullable', 'integer'],
            'spec_group_id' => 'nullable', 'integer',
            'categories' => ['required', 'array'],
            'wholesale_set' => ['nullable', 'array'],
            'wholesale_set.num.*' => ['numeric', 'min:1', 'distinct'],
            'wholesale_set.price.*' => ['numeric', 'gt:0'],
            'productSpecs' => ['nullable', 'array'],
            'productSpecs.*.spec_value' => ['required_if:productSpecs.*.spec_type,1', 'max:50'],
            'is_hot' => ['integer', 'in:1,2'],
            'is_new' => ['integer', 'in:1,2'],
            'is_recommend' => ['integer', 'in:1,2'],
        ];
        if (config('strongshop.multiLanguageBackend'))
        {
            $lang_front = config('strongshop.defaultLanguage');

            $data['title'] = ['required', 'array', 'max:99'];
            $data['title.' . $lang_front] = ['required', 'string', 'max:255'];

            $data['intro'] = ['nullable', 'array', 'max:99'];
            $data['intro.' . $lang_front] = ['nullable', 'string', 'max:255'];

            $data['details'] = ['nullable', 'array', 'max:99'];
            $data['details.' . $lang_front] = ['nullable', 'string', 'max:65535'];
        }
        return $data;
    }

    /**
     * Validator messages
     * @return type
     */
    public function messages()
    {
        return [
            'wholesale_set.num.*.distinct' => ':attribute 不能重复',
            'productSpecs.*.spec_value.required_if' => '产品价格规格 不能为空',
        ];
    }

    /**
     * Validator customAttributes
     * @return type
     */
    public function customAttributes()
    {
        return [
            'id' => '产品id',
            'title' => '产品标题',
            'intro' => '产品简介',
            'details' => '产品详情',
            'meta_keywords' => 'Meta Keywords',
            'meta_description' => 'Meta Description',
            'spu' => 'SPU',
            'sku' => 'SKU',
            'stock' => '库存',
            'stock_status' => '库存状态',
            'original_price' => '原价(' . config('strongshop.defaultCurrencyBackend') . ')',
            'sale_price' => '售价(' . config('strongshop.defaultCurrencyBackend') . ')',
            'img_cover' => '产品封面图',
            'img_photos' => '产品相册',
            'long' => '长',
            'width' => '宽',
            'high' => '高',
            'weight' => '重量(g)',
            'volume_weight' => '体积重',
            'click_num' => '点击数',
            'status' => '状态',
            'remark' => '产品备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => '删除时间',
            'related_sku' => '相关产品',
            'related_accessories_sku' => '相关配件',
            'brand_id' => '所属品牌',
            'categories' => '产品分类',
            'wholesale_set' => '批发设置',
            'wholesale_set.num.*' => '批发数量',
            'wholesale_set.price.*' => '批发价格',
            'spec_group_id' => '规格组',
            'productSpecs' => '产品规格',
            'productSpecs.*.spec_value' => '产品规格',
            'is_hot' => '热卖',
            'is_new' => '新品',
            'is_recommend' => '推荐',
            'hidden' => '强制隐藏',
            'sale_num' => '销量',
        ];
    }

    public function getAttributeLabel($key)
    {
        $datas = $this->customAttributes();
        return $datas[$key] ?? $key;
    }

    /**
     * Fill the model with an array of attributes.
     * @param type $attributes
     * @return $this
     */
    public function fill($attributes)
    {
        foreach ($attributes as $key => $attribute)
        {
            if ($attribute === null)
            {
                unset($attributes[$key]);
            }
        }
        parent::fill($attributes);
        return $this;
    }

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

    public function setTitleAttribute($value)
    {
        if (is_array($value))
        {
            $value = json_encode($value);
        }
        $this->attributes['title'] = $value;
    }

    public function getTitleAttribute($value)
    {
        if (!$value)
        {
            return $value;
        }
        $lang_front = config('strongshop.defaultLanguage');
        $arr = json_decode($value, true);
        if (!$arr)
        {
            if (!config('strongshop.multiLanguageBackend'))
            {
                return $value;
            }
            return [$lang_front => $value];
        }
        if (!config('strongshop.multiLanguageBackend'))
        {
            return $arr[$lang_front] ?? array_shift($arr);
        }
        return $arr;
    }

    /**
     * name [后台显示]
     * @return type
     */
    public function getTitleLabelAttribute($value)
    {
        if (!$value)
        {
            $value = $this->getOriginal('title');
        }
        if (!$value)
        {

            return $value;
        }
        $lang_front = config('strongshop.defaultLanguage');
        $arr = json_decode($value, true);
        if (!$arr)
        {
            return $value;
        }
        return $arr[$lang_front] ?? array_shift($arr);
    }

    public function setIntroAttribute($value)
    {
        if (is_array($value))
        {
            $value = json_encode($value);
        }
        $this->attributes['Intro'] = $value;
    }

    public function getIntroAttribute($value)
    {
        if (!$value)
        {
            return $value;
        }
        $lang_front = config('strongshop.defaultLanguage');
        $arr = json_decode($value, true);
        if (!$arr)
        {
            if (!config('strongshop.multiLanguageBackend'))
            {
                return $value;
            }
            return [$lang_front => $value];
        }
        if (!config('strongshop.multiLanguageBackend'))
        {
            return $arr[$lang_front] ?? array_shift($arr);
        }
        return $arr;
    }

    public function setDetailsAttribute($value)
    {
        if (is_array($value))
        {
            $value = json_encode($value);
        }
        $this->attributes['details'] = $value;
    }

    public function getDetailsAttribute($value)
    {
        if (!$value)
        {
            return $value;
        }
        $lang_front = config('strongshop.defaultLanguage');
        $arr = json_decode($value, true);
        if (!$arr)
        {
            if (!config('strongshop.multiLanguageBackend'))
            {
                return $value;
            }
            return [$lang_front => $value];
        }
        if (!config('strongshop.multiLanguageBackend'))
        {
            return $arr[$lang_front] ?? array_shift($arr);
        }
        return $arr;
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
