<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InternalException;

class ProductSku extends Model
{
    use HasFactory;

    // 允许被批量赋值的字段
    protected $fillable = ['title', 'description', 'price', 'stock'];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    // 减库存
    public function decreaseStock($amount)
    {
        /**
         * decrement 减少字段的值，会u返回影响的行数
         */
        if($amount < 0) {
            throw new InternalException('减库存不能小于0');
        }
        return $this->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

    // 加库存
    public function addStock($amount){
        if($amount < 0) {
            throw new InternalException('加库存不能小于0');
        }
        $this->increment('stock', $amount);
    }
}
