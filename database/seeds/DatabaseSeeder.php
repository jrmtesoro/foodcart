<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 50; $i++) {
            $menu = App\Menu::create([
                "name" => $faker->word,
                "description" => $faker->sentence,
                "price" => $faker->numberBetween(0, 300),
                "cooking_time" => $faker->numberBetween(0,60),
                "image_name" =>  time().rand(1000, 9999).".png"
            ]);
            App\Restaurant::find(1)->menu()->save($menu);
            $rand = $faker->numberBetween(1, 2);
            App\MenuCategory::create([
                "menu_id" => $menu->id,
                "category_id" => $rand
            ]);
        }
    }
}
