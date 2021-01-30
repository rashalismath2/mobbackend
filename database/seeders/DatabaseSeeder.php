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


        $groups = Group::factory()
                ->count(20)
                ->create();

        $requests = Request::factory()
                ->count(20)
                ->create();

        $groupsStudents = GroupsStudents::factory()
                ->count(20)
                ->create();
        
    }
}
