<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserAddress;
use App\Models\User;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        $users->each(function (User $user) {
            if (fake()->boolean(80)) {
                $numberOfAddresses = fake()->numberBetween(1,3);

                UserAddress::factory()->defaultAddress()->create([
                    'user_id' => $user->id,
                ]);

                for ($i = 1; $i < $numberOfAddresses; $i++) {
                    UserAddress::factory()->create([
                        'user_id' => $user->id,
                        'is_default' => false,
                    ]);
                }
            }
        });
    }
}
