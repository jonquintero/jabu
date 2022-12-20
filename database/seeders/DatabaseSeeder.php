<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Frequency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([

            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => Hash::make('secret'),
        ]);

        $insert = [
            [
                'name' => 'Daily',
                'created_at' => now()
            ],
            [
                'name' => 'Monday',
                'created_at' => now()
            ],
            [
                'name' => 'Wednesday',
                'created_at' => now()
            ],
            [
                'name' => 'Friday',
                'created_at' => now()
            ],
            [
                'name' => 'Monthly On 5th',
                'created_at' => now()
            ],
            [
                'name' => 'Yearly On, March 5th',
                'created_at' => now()
            ],
        ];

        Frequency::insert($insert);
    }
}
