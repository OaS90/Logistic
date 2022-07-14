<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailQuote;

class EmailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('quote_emails') as $email) {
            EmailQuote::create(['email' => $email]);
        }
    }
}
