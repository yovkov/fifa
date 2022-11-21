<?php

namespace App\Http\Controllers;

use App\Models\Game;
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
}
