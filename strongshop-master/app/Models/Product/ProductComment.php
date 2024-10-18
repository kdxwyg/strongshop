<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductComment extends Model
{

    use \Illuminate\Database\Eloquent\SoftDeletes;

    public $tableComments = '产品评论表';
    protected $table = 'product_comment';
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $casts = ['pictures' => 'json', 'videos' => 'json', 'more' => 'json'];
    protected $hidden = ['deleted_at'];

    /**
     * Validator rules
     * @param type $on
     * @return type
     */
    public function rules()
    {
        return [
            'user_id' => ['required', 'integer'],
            'at_user_id' => ['integer'],
            'product_id' => ['integer'],
            'product_sku' => ['string', 'max:50'],
            'content' => ['required', 'string', 'max:2000'],
            'pictures' => ['array'],
            'videos' => ['array'],
            'likes' => ['required', 'integer'],
            'status' => ['required', 'integer'],
            'credits' => ['required', 'integer'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
            'deleted_at' => ['date'],
        ];
    }

    /**
     * Validator messages
     * @return type
     */
    public function messages()
    {
        return [];
    }

    /**
     * Validator customAttributes
     * @return type
     */
    public function customAttributes()
    {
        return [
            'id' => 'ID',
            'user_id' => '会员id',
            'first_name' => '会员名',
            'last_name' => '会员姓',
            'country' => '会员所属国家',
            'at_user_id' => '@会员id',
            'product_id' => '产品id',
            'product_sku' => '产品sku',
            'content' => '内容',
            'pictures' => '图片',
            'videos' => '视频',
            'likes' => '点赞数',
            'star' => '星级',
            'status' => '状态',
            'credits' => '获得积分奖励',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => '删除时间',
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

    public function getContentShortAttribute($value = null)
    {
        $len = mb_strlen($value);
        if ($len > 100)
        {
            $value = mb_substr($value, 0, 100) . ' ...';
            return $value;
        }
        return $value;
    }

    public function getUploadAtAttribute($value = null)
    {
        if ($value)
        {
            return now()->parse($value)->toFormattedDateString();
        }
        return $value;
    }

    /**
     * 作品图片【绝对路径】
     */
    public function getAssetPicturesAttribute($value = null)
    {
        if (!$value)
        {
            $value = $this->getAttribute('pictures');
        }
        if (!$value || empty($value))
        {
            return $value;
        }
        $datas = json_decode($value, 1);
        foreach ($datas as &$data)
        {
            $data['src'] = asset($data['src']);
        }
        return $datas;
    }

    /**
     * 作品图片封面图
     */
    public function getAssetPictureCoverAttribute($value = null)
    {
        if (!$value)
        {
            $value = $this->getAttribute('pictures');
        }
        if (!$value || empty($value))
        {
            return $value;
        }
        $datas = json_decode($value, 1);
        if (empty($datas))
        {
            return [
                'src' => '',
                'width' => '',
                'height' => '',
            ];
        }
        return [
            'src' => asset($datas[0]['src_thumb']),
            'width' => $datas[0]['thumb_width'],
            'height' => $datas[0]['thumb_height'],
        ];
    }

    /**
     * 评论者
     * @return type
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

}
