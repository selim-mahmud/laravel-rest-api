<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //seed api_user table
        factory(App\ApiUser::class, 1)->create();

        //seed tag table
        factory(App\Tag::class, 10)->create();

        //seed user table
        factory(App\User::class, 10)->create()->each(function ($user) {
            //seed question table
            $questionCount = rand(10, 50);
            factory(App\Question::class, $questionCount)->create(['user_id'=>$user->id])->each(function ($question) use ($user){
                //seed answer table
                $answerCount = rand(5, 10);
                factory(App\Answer::class, $answerCount)->create(['question_id' => $question->id, 'user_id' => $user->id]);
            });
        });

        //seed question_tag pivot table
        $tags = App\Tag::all();
        App\Question::all()->each(function ($question) use ($tags) {
            $question->tags()->attach(
                $tags->random(rand(1, 5))->pluck('id')->toArray()
            );
        });
    }
}
