<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Standing;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class GamesController extends Controller
{
    /**
     * Display the user's profile form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        return Inertia::render('Dashboard', [
            'games' => Game::with('home')->with('away')->with('prediction')->with('allPredictions.user')->orderby('game_date','ASC')->get(),
            'standings' => Standing::with('team')->get()
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param  \App\Http\Requests\ProfileUpdateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {

        return Redirect::route('profile.edit');
    }

    public function store(Request $request)
    {
        $request->merge([
            'user_id' => auth()->user()->id,
        ]);

        $existsPrediction = Prediction::where('user_id',auth()->user()->id)->where('game_id',$request->get('game_id'))->first();

        if ($existsPrediction) {
            $existsPrediction->home_score = $request->get('home_score');
            $existsPrediction->away_score = $request->get('away_score');
            $existsPrediction->save();
        }
        else {
            $existsPrediction = Prediction::create($request->toArray());
        }

        $games = Game::with('home')->with('away')->with('prediction')->with('allPredictions.user')->orderby('game_date','ASC')->get();
        

        return response([
            'data' => [
                'prediction' => $existsPrediction,
                'games' => $games
            ],
            'message' => __('Prediction created successfully!')
        ], 200);
    }

    public function scores()
    {
        $users = User::with('predictions')->get();

        foreach ($users as $user) {
            $formattedData[$user->id]['user'] = $user;
            $points = 0;
            $resultsPredicted = 0;
            $outcomesPredicted = 0;
            $totalPredictions = 0;
            foreach ($user->predictions as $prediction) {
                $home_score = $prediction->game->home_score;
                $away_score = $prediction->game->away_score;
                $home_prediction = $prediction->home_score;
                $away_prediction = $prediction->away_score;

                $diff_score = $home_score - $away_score;
                $diff_prediction = $home_prediction - $away_prediction;
                $outcome = $diff_score * $diff_prediction;

                if ($home_score == $home_prediction && $away_score == $away_prediction) {
                    $points += 3;
                    $resultsPredicted ++;
                    $totalPredictions ++;
                } else if ($outcome > 0 || ($diff_score == 0 && $diff_prediction == 0)) {
                    $points += 1;
                    $outcomesPredicted ++;
                    $totalPredictions ++;
                }
                else {
                    $totalPredictions ++;
                }
            }
            $formattedData[$user->id]['points'] = $points;
            $formattedData[$user->id]['outcomes'] = $outcomesPredicted;
            $formattedData[$user->id]['results'] = $resultsPredicted;
            $formattedData[$user->id]['predictions'] = $totalPredictions;
        }

        $points = array_column($formattedData, 'points');
        array_multisort($points, SORT_DESC, $formattedData);

        return Inertia::render('Leaderboard', [
            'data' => $formattedData
        ]);
    }
}
