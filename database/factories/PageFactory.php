<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Folder;
use App\Models\Page;
use Faker\Generator as Faker;

$factory->define(Page::class, function (Faker $faker) {
    return [
        'title' => $faker->unique()->words(random_int(2, 3), true),
        'content' => $faker->randomHtml(),
        'published' => false,
        'folder_id' => optional(Folder::query()->inRandomOrder()->first())->id
    ];
});
