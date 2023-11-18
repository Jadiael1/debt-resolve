<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@teste.com',
            'email_verified_at' => now(),
            'password' => Hash::make('testePassword'),
            'remember_token' => Str::random(10),
        ])->each(function ($user) {
            \App\Models\Charge::factory(1)->create()->each(function ($charge) use ($user) {
                $status = ["collector_id", "debtor_id"][random_int(0, 1)];
                $charge[$status] = $user->id;
                $charge->save();
                for ($i = 1; $i <= $charge->installments_number; $i++) {
                    \App\Models\Installment::factory(1)->state([
                        'value' => $charge->amount / $charge->installments_number,
                        'installment_number' => $i,
                        'charge_id' => $charge->id,
                        'due_date' => Carbon::now()->day($charge->due_day)->addMonths($i)
                    ])->create();
                }
            });
        });
        \App\Models\User::factory()->create([
            'name' => 'Test User1',
            'email' => 'test1@teste.com',
            'email_verified_at' => now(),
            'password' => Hash::make('testePassword'),
            'remember_token' => Str::random(10),
        ]);
        // \App\Models\User::factory(19)->create();
    }
}
