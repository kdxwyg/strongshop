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

use Illuminate\Http\Request;
use App\Http\Controllers\Strongadmin\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use App\Models\ShippingOption;

class ShippingOptionController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShippingOption  $shippingOption
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ShippingOption $shippingOption)
    {
        if (!$request->expectsJson())
        {
            return $this->view('shippingOption.index', ['model' => $shippingOption]);
        }
        $model = ShippingOption::orderBy(($request->field ?: 'id'), ($request->order ?: 'desc'));
        if ($request->id)
        {
            $model->where('id', $request->id);
        }
        if ($request->title)
        {
            $model->where('title', 'like', "%{$request->title}%");
        }
        if ($request->desc)
        {
            $model->where('desc', 'like', "%{$request->desc}%");
        }
        if ($request->code)
        {
            $model->where('code', 'like', "%{$request->code}%");
        }
        if ($request->status)
        {
            $model->where('status', $request->status);
        }
        if ($request->more)
        {
            $model->where('more', 'like', "%{$request->more}%");
        }
        if ($request->created_at_begin && $request->created_at_end)
        {
            $model->whereBetween('created_at', [$request->created_at_begin, Carbon::parse($request->created_at_end)->endOfDay()]);
        }
        if ((isset($request->page) && $request->page <= 0) || $request->export)
        {
            $rows = $model->get();
        } else
        {
            $rows = $model->paginate($request->limit);
        }
        //$rows->makeHidden(['deleted_at']);
        return ['code' => 200, 'message' => __('admin.Success'), 'data' => $rows];
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'id' => ['required', 'integer', Rule::exists('shipping_option')],
        ]);
        if ($validator->fails())
        {
            return ['code' => 3001, 'message' => $validator->errors()->first(), 'data' => $validator->errors()];
        }
        $model = ShippingOption::find($request->id);
        if ($model)
        {
            return ['code' => 200, 'message' => __('admin.Success'), 'data' => $model];
        } else
        {
            return ['code' => 5001, 'message' => __('admin.Server internal error')];
        }
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShippingOption  $shippingOption
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, ShippingOption $shippingOption)
    {
        if (!$request->expectsJson())
        {
            return $this->view('shippingOption.form', ['model' => $shippingOption]);
        }
        $rules = array_merge_recursive($shippingOption->rules(), [
        ]);
        $messages = $shippingOption->messages();
        $customAttributes = $shippingOption->customAttributes();
        $validator = Validator::make($request->all(), $rules, $messages, $customAttributes);
        if ($validator->fails())
        {
            return ['code' => 3001, 'message' => $validator->errors()->first(), 'data' => $validator->errors()];
        }
        $shippingOption->fill($request->all());
        if ($shippingOption->save())
        {
            return [
                'code' => 200,
                'message' => __('admin.SuccessCreated'),
                'data' => $shippingOption,
                'log' => sprintf('[%s][%s][id:%s]', __('admin.SuccessCreated'), $shippingOption->tableComments, $shippingOption->id)
            ];
        } else
        {
            return ['code' => 5001, 'message' => __('admin.Server internal error')];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShippingOption  $shippingOption
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShippingOption $shippingOption)
    {
        if (!$request->expectsJson())
        {
            $model = $shippingOption::find($request->id);
            return $this->view('shippingOption.form', ['model' => $model]);
        }
        $rules = array_merge_recursive($shippingOption->rules(), [
            'id' => ['required', 'integer', Rule::exists('shipping_option')],
        ]);
        $messages = $shippingOption->messages();
        $customAttributes = $shippingOption->customAttributes();
        $validator = Validator::make($request->all(), $rules, $messages, $customAttributes);
        if ($validator->fails())
        {
            return ['code' => 3001, 'message' => $validator->errors()->first(), 'data' => $validator->errors()];
        }
        $model = $shippingOption::find($request->id);
        $model->fill($request->all());
        if ($model->save())
        {
            return [
                'code' => 200,
                'message' => __('admin.SuccessUpdated'),
                'data' => $model,
                'log' => sprintf('[%s][%s][id:%s]', __('admin.SuccessUpdated'), $shippingOption->tableComments, $model->id)
            ];
        } else
        {
            return ['code' => 5001, 'message' => __('admin.Server internal error')];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShippingOption  $shippingOption
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ShippingOption $shippingOption)
    {
        $validator = Validator::make($request->all(), [
                    'id' => ['required',],
        ]);
        if ($validator->fails())
        {
            return ['code' => 3001, 'message' => $validator->errors()->first(), 'data' => $validator->errors()];
        }
        $ids = is_array($request->id) ? $request->id : [$request->id];
        $model = $shippingOption::whereIn('id', $ids);
        if ($model->delete())
        {
            return [
                'code' => 200,
                'message' => __('admin.SuccessDestroyed'),
                'log' => sprintf('[%s][%s]『id:%s』', __('admin.SuccessDestroyed'), $shippingOption->tableComments, json_encode($ids))
            ];
        } else
        {
            return ['code' => 5001, 'message' => __('admin.Server internal error')];
        }
    }

}
