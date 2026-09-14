<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            ['code' => 'AUD', 'description' => 'Australia Dollar', 'decimal' => 2],
            ['code' => 'CNY', 'description' => 'China Yuan', 'decimal' => 2],
            ['code' => 'EUR', 'description' => 'Euro', 'decimal' => 2],
            ['code' => 'GBP', 'description' => 'Great Britain Pound', 'decimal' => 2],
            ['code' => 'IDR', 'description' => 'Indonesia Rupiah', 'decimal' => 0],
            ['code' => 'JPY', 'description' => 'Japan Yen', 'decimal' => 2],
            ['code' => 'RBN', 'description' => 'Rupiah Dalam Ribuan', 'decimal' => 2],
            ['code' => 'SGD', 'description' => 'Singapore Dollar', 'decimal' => 2],
            ['code' => 'USD', 'description' => 'US Dollar', 'decimal' => 2],
        ];

        foreach ($currencies as $curr) {
            Currency::updateOrCreate(['code' => $curr['code']], $curr);
        }
    }
}
