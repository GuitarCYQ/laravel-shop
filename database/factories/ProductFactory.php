<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{

    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $image = $this->faker->randomElement([
            "https://img10.360buyimg.com/jdcms/s240x240_jfs/t1/293308/39/14873/209270/6865ec0fF5ddf4d32/313c6e63674625ed.jpg",
            "https://img11.360buyimg.com/jdcms/s240x240_jfs/t1/297584/31/18589/137030/68639138F8af9f746/747ff292f3aff785.jpg",
            "https://img10.360buyimg.com/jdcms/s240x240_jfs/t1/304715/18/14124/236678/68623c65Ffe7b8ae5/3fbdb2b60c64db09.jpg",
            "https://img30.360buyimg.com/jdcms/s240x240_jfs/t1/289218/34/15451/176301/6864ff3fF46d944fa/873916a531322107.jpg",
            "https://img13.360buyimg.com/jdcms/s240x240_jfs/t1/167259/39/51804/79423/68623c1eF1275f1bd/d63056e3d6bf7506.jpg",
            "https://img10.360buyimg.com/jdcms/s240x240_jfs/t1/293308/39/14873/209270/6865ec0fF5ddf4d32/313c6e63674625ed.jpg",
            "https://img14.360buyimg.com/jdcms/s240x240_jfs/t1/312772/9/13628/102560/68639d79F20f81e4f/e492a83a2a698df7.jpg",
            "https://img10.360buyimg.com/jdcms/s240x240_jfs/t1/280481/36/27799/186673/6864aa3bFee4ea118/241f4db475148eab.png",
            "https://img11.360buyimg.com/jdcms/s240x240_jfs/t1/292746/11/13957/190956/68634987F43cb1380/1acc65d33f193a54.jpg",
        ]);

        return [
            'title'         => $this->faker->word,
            'description'   => $this->faker->sentence,
            'image'         => $image,
            'on_sale'       => true,
            'rating'        => $this->faker->numberBetween(0, 5),
            'sold_count'    => 0,
            'review_count'  => 0,
            'price'         => 0,
        ];
    }
}
