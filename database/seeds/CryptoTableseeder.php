<?php

use Illuminate\Database\Seeder;

class CryptoTableseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cryptos')->insert([
            [
                'crypto' => 'BTC',
            ],
            [
                'crypto' => 'ETH',
            ],
            [
                'crypto' => 'ETC',
            ],
            [
                'crypto' => 'LSK',
            ],
            [
                'crypto' => 'FCT',
            ],
            [
                'crypto' => 'XRP',
            ],
            [
                'crypto' => 'XEM',
            ],
            [
                'crypto' => 'LTC',
            ],
            [
                'crypto' => 'BCH',
            ],
            [
                'crypto' => 'MONA',
            ],
            [
                'crypto' => 'DASH',
            ],
            [
                'crypto' => 'ZEC',
            ],
            [
                'crypto' => 'XMR',
            ],
            [
                'crypto' => 'REP',
            ]
        ]);
    }
}
