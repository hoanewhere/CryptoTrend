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
                'crypto' => 'bitcoin',
            ],
            [
                'crypto' => 'Ethereum',
            ],
            [
                'crypto' => 'EthereumClassic',
            ],
            [
                'crypto' => 'LISK',
            ],
            [
                'crypto' => 'Factom',
            ],
            [
                'crypto' => 'Ripple',
            ],
            [
                'crypto' => 'XEM',
            ],
            [
                'crypto' => 'Litecoin',
            ],
            [
                'crypto' => 'BitcoinCash',
            ],
            [
                'crypto' => 'MonaCoin',
            ],
            [
                'crypto' => 'Dash',
            ],
            [
                'crypto' => 'Zcash',
            ],
            [
                'crypto' => 'Monero',
            ],
            [
                'crypto' => 'Augur',
            ]
        ]);
    }
}
