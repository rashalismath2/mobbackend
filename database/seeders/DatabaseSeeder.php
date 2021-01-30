<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Master;
use App\Models\Group;
use App\Models\Request;
use App\Models\GroupsStudents;

use Illuminate\Database\Eloquent\Factories\Sequence;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        for ($i=0; $i < 21; $i++) { 
                $masters = Master::factory()
                ->state(new Sequence(
                        ['gender' => 'male'],
                        ['gender' => 'female'],
                    ))
                ->create();
        
                $groups = Group::factory()
                        ->has(Request::factory()->count(20))
                        ->has(GroupsStudents::factory()->count(20))
                        ->for($masters)
                        ->create();
        }
        
    }
}
