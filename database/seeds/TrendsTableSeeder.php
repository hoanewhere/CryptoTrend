<?php

use Illuminate\Database\Seeder;

class TrendsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('trends')->insert([
            [
                'crypto_id' => 1,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 1,
                'day_cnt' => 2,
                'week_cnt' => 3,
            ],
            [
                'crypto_id' => 2,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 4,
                'day_cnt' => 5,
                'week_cnt' => 6,
            ],
            [
                'crypto_id' => 3,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 7,
                'day_cnt' => 8,
                'week_cnt' => 9,
            ],
            [
                'crypto_id' => 4,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 10,
                'day_cnt' => 11,
                'week_cnt' => 12,
            ],
            [
                'crypto_id' => 5,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 13,
                'day_cnt' => 14,
                'week_cnt' => 15,
            ],
            [
                'crypto_id' => 6,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 16,
                'day_cnt' => 17,
                'week_cnt' => 18,
            ],
            [
                'crypto_id' => 7,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 19,
                'day_cnt' => 20,
                'week_cnt' => 21,
            ],
            [
                'crypto_id' => 8,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 22,
                'day_cnt' => 23,
                'week_cnt' => 24,
            ],
            [
                'crypto_id' => 9,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 25,
                'day_cnt' => 26,
                'week_cnt' => 27,
            ],
            [
                'crypto_id' => 10,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 28,
                'day_cnt' => 29,
                'week_cnt' => 30,
            ],
            [
                'crypto_id' => 11,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 31,
                'day_cnt' => 32,
                'week_cnt' => 33,
            ],
            [
                'crypto_id' => 12,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 34,
                'day_cnt' => 35,
                'week_cnt' => 36,
            ],
            [
                'crypto_id' => 13,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 37,
                'day_cnt' => 38,
                'week_cnt' => 39,
            ],
            [
                'crypto_id' => 14,
                'time_id' => 1,
                'complete_flg' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'hour_cnt' => 40,
                'day_cnt' => 41,
                'week_cnt' => 42,
            ]
        ]);
    }
}
