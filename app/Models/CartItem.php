<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    // 允许批量复制的字段
    protected $fillable = ['amount'];
    public $timestamps = false;

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function productSku() {
        return $this->belongsTo(ProductSku::class);
    }
}
