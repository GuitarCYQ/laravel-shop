<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reviews'   =>  ['required', 'array'],
            'reviews.*.id'  =>  [
                'required',
                /**
                 * reviews.*.id 表示验证reviews 数组下的每个元素中的id
                 * required 基本验证规则，确保该字段存在且不为空
                 * Rule::exists() 自定义数据库存在性验证
                 *      'order_item','id': 验证这些 id 是否存在于 order_item 表中
                 *      ->where('order_id', $this->route('order')->id) order_id 跟 路由里的id相等
                 */
                Rule::exists('order_items', 'id')->where('order_id', $this->route('order')->id)
            ],
            'reviews.*.rating'  =>  ['required', 'integer', 'between:1,5'],
            'reviews.*.review'  =>  ['required'],
        ];
    }

    public function attributes() {
        return [
            'reviews.*.rating'  =>  '评分',
            'reviews.*.review'  =>  '评价',
        ];
    }
}
