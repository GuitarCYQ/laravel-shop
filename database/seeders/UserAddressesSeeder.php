<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAddress;

class UserAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * User::all() 从数据获取所以的用户，并返回一个集合
         * ->each() 是 Collection 的一个方法，与 foreach 类似，循环集合中的每一个元素，将其作为参数传递给匿名函数，在这里集合里的元素都说User类型
         * factory(UserAddress::class, random_int(1, 3)) 对每一个用户，产生一个 1-3 的随机数作为我们要为每个用户生成地址的个数
         * create(['user_id' => $user->id]) 将随机生成的数据写入数据库，同时指定这批数据的user_id 字段统一为当前循环的用户ID
         */
        User::all()->each(function (User $user) {
            UserAddress::factory()->count(random_int(1, 3))->create(['user_id' => $user->id]);
        });
    }
}
