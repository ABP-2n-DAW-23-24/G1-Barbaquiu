<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user_joan = User::create([
            'name' => 'Joan',
            'surnames' => 'Paneque Domingo',
            'email' => 'jpaneque@cendrassos.net',
            'password' => bcrypt('123456')
        ]);

        $user_roman = User::create([
            'name' => 'Roman',
            'surnames' => 'Mysyura',
            'email' => 'rmysyura@cendrassos.net',
            'password' => bcrypt('123456'),
            'image' => '/assets/img/roman.jpg'
        ]);

        $user_emma = User::create([
            'name' => 'Emma',
            'surnames' => 'Cardosa',
            'email' => 'ecardosa@cendrassos.net',
            'password' => bcrypt('123456')
        ]);

        $user_aniol = User::create([
            'name' => 'Aniol',
            'surnames' => 'Moreno',
            'email' => 'amoreno@cendrassos.net',
            'password' => bcrypt('123456')
        ]);

        $user_marcos = User::create([
            'name' => 'Marcos',
            'surnames' => 'Muñoz',
            'email' => 'mmunoz@cendrassos.net',
            'password' => bcrypt('123456')
        ]);

                // joan
                $user_joan->sendFriendRequest($user_roman);
                $user_roman->acceptFriendRequest($user_joan);
        
                $user_joan->sendFriendRequest($user_emma);
                $user_emma->acceptFriendRequest($user_joan);
        
                $user_joan->sendFriendRequest($user_aniol);
                $user_aniol->acceptFriendRequest($user_joan);
        
                $user_joan->sendFriendRequest($user_marcos);
                $user_marcos->acceptFriendRequest($user_joan);
        
                // roman
                $user_roman->sendFriendRequest($user_emma);
                $user_emma->acceptFriendRequest($user_roman);
        
                $user_roman->sendFriendRequest($user_aniol);
                $user_aniol->acceptFriendRequest($user_roman);
        
                $user_roman->sendFriendRequest($user_marcos);
                $user_marcos->acceptFriendRequest($user_roman);
        
                // emma
                $user_emma->sendFriendRequest($user_aniol);
                $user_aniol->acceptFriendRequest($user_emma);
        
                $user_emma->sendFriendRequest($user_marcos);
                $user_marcos->acceptFriendRequest($user_emma);
        
                // aniol
                $user_aniol->sendFriendRequest($user_marcos);
                $user_marcos->acceptFriendRequest($user_aniol);

    
    }
}
