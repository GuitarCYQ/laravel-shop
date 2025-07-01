<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at'
    ];
    protected $dates = ['last_used_at'];

    //与User模型关联，管理关系是一对多
    public function user() {
        return $this->belongTo(User::class);
    }

    // 创建一个方法，在之后的代码可以通过$address->full_address来获取完整地址
    public function getFullAddressAttribute() {
        return "{$this->province}{$this->city}{$this->district}{$this->adderss}";
    }
}
