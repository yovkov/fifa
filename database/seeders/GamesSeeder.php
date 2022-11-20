<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class GamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Game::truncate();

        $data = Storage::disk('local')
            ->get('seeder/games.json');
        $data = json_decode($data);

        $games = $data->data;

        foreach ($games as $game) {
            $bg_datetime = Carbon::parse($game->local_date)->subHour();
            $finished = $game->finished == 'FALSE' ? 0 : 1;
            Game::create([
                'id' => $game->id,
                '_id' => $game->_id,
                'type' => $game->type,
                'home_team_id' => $game->home_team_id,
                'away_team_id' => $game->away_team_id,
                'home_score' => $game->home_score,
                'away_score' => $game->away_score,
                'game_date' => $bg_datetime,
                'group' => $game->group,
                'matchday' => $game->matchday,
                'finished' => $finished,
                'time_elapsed' => $game->time_elapsed,
            ]);
        }
    }
}
