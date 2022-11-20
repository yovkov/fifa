<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Team::truncate();

        $data = Storage::disk('local')
            ->get('seeder/teams.json');
        $data = json_decode($data);

        $teams = $data->data;
        foreach ($teams as $team) {
            Team::create([
                'id' => $team->id,
                '_id' => $team->_id,
                'name' => $team->name_en,
                'flag' => $team->flag,
                'group' => $team->groups,
            ]);
        }
    }
}
