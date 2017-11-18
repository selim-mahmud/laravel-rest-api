<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('password'),
        'active' => $faker->boolean(80),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\ApiUser::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'user_name' => $faker->unique()->userName,
        'password' => $password ?: $password = bcrypt('password'),
        'active' => $faker->boolean(80)
    ];
});

$factory->define(App\Tag::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->text(40),
        'slug' => function (array $tag) {
            return str_slug($tag['name']);
        },
        'active' => $faker->boolean(80)
    ];
});

$factory->define(App\Question::class, function (Faker\Generator $faker) {
    return array(
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        },
        'title' => $faker->sentence(),
        'slug' => function (array $question) {
            return str_slug($question['title']);
        },
        'description' => $faker->paragraph,
        'featured' => $faker->boolean(),
        'sticky' => $faker->boolean(),
        'solved' => $faker->boolean(),
        'up_vote' => $faker->numberBetween(0, 100),
        'down_vote' => $faker->numberBetween(0, 100),
    );
});

$factory->define(App\Answer::class, function (Faker\Generator $faker) {
    return array(
        'question_id' => function() {
            return factory(App\Question::class)->create()->id;
        },
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        },
        'description' => $faker->paragraph,
        'excepted' => $faker->boolean(),
        'up_vote' => $faker->numberBetween(0, 100),
        'down_vote' => $faker->numberBetween(0, 100),
    );
});
