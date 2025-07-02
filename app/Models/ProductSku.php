<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    use HasFactory;

    // 允许被批量赋值的字段
    protected $fillable = ['title', 'description', 'price', 'stock'];

    public function product(){
        return $this->belongTo(Product::class);
    }
}
