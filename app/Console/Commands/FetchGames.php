<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest games data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $response = Http::withBody(
            '{
                "email": "yovkov@gmail.com",
                "password": "yovkov2112"
            }', 'application/json'
        )->post('http://api.cup2022.ir/api/v1/user/login')->object();

        $token = $response->data->token;

        $response = Http::withToken($token)->get('http://api.cup2022.ir/api/v1/match')->object();

        foreach ($response->data as $game) {
            $existsGame = Game::where('id',$game->id)->first();

            if (!$existsGame) {
                $existsGame = new Game;
            }

            $bg_datetime = Carbon::parse($game->local_date)->subHour();
            $finished = $game->finished == 'FALSE' ? 0 : 1;
            
            $existsGame->id = $game->id;
            $existsGame->_id = $game->_id;
            $existsGame->type = $game->type;
            $existsGame->home_team_id = $game->home_team_id;
            $existsGame->away_team_id = $game->away_team_id;
            $existsGame->home_score = $game->home_score;
            $existsGame->away_score = $game->away_score;
            $existsGame->game_date = $bg_datetime;
            $existsGame->group = $game->group;
            $existsGame->matchday = $game->matchday;
            $existsGame->finished = $finished;
            $existsGame->time_elapsed = $game->time_elapsed;

            $existsGame->save();
        }
        
        return Command::SUCCESS;
    }
}
