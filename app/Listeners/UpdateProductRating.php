<?php

namespace App\Listeners;

use DB;
use App\Models\OrderItem;
use App\Events\OrderReviewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProductRating implements ShouldQueue
{

    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderReviewed  $event
     * @return void
     */
    public function handle(OrderReviewed $event)
    {
        // 通过 with 方法提前加载数据，避免 N+1 性能问题
        $items = $event->getOrder()->items()->with(['product'])->get();
        foreach ($items as $item) {
            /**
             * first() 是优先执行
             */
            $result = OrderItem::query()
                ->where('product_id', $item->product_id)
                ->whereNotNull('reviewed_at')
                ->whereHas('order', function ($query) {
                    $query->whereNotNull('paid_at');
                })
                ->first([
                    DB::raw('count(*) as review_count'),
                    DB::raw('avg(rating) as rating')
                ]);
            // 更新商品的评分和评论数
            $item->product->update([
                'rating'    =>  $result->rating,
                'review_count'  =>  $result->review_count,
            ]);
        }
    }
}
