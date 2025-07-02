<?php

namespace App\Policies;

use App\Models\User;
use App\Model\UserAddress;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserAddressPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // 当own方法放回true时，代表当前用户可以修改对应的地址
    public function own(User $user, UserAddress $address)
    {
        return $address->user_id === $user->id;
    }
}
