<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GamePrediction;
use App\Models\ColorPrediction;
use Illuminate\Support\Facades\Session;

class ColorPredictionController extends Controller
{
    public function index(Request $request) {
        $colorData = new GamePrediction;
        if (!empty($request->search)) {
            $colorData = $colorData->where(function ($query) use ($request) {
                $query->orWhere('id', 'LIKE', '%'. $request->search. '%');
            });
        }

        $colorData = $colorData->where('game_type', '1')->orderBy('id', 'DESC')->sortable()->paginate(env('ITEMS_PER_PAGE'));
        return view('color_prediction.color-prediction-list',
        [
            'colorData' => $colorData,
            'searchData' => $request->search,
            'uri' => \Request::route()->uri
       ]);
    }

    public function gameEdit(Request $request) {
        $gameData = GamePrediction::find($request->id);
        if($gameData) {

            $users = ColorPrediction::where('game_id', $gameData->id)->sortable()->paginate(env('ITEMS_PER_PAGE'));

            return view('color_prediction.color-prediction-detail',
            [
                'colorData' => $gameData,
                'users' => $users,
                'searchData' => $request->search,
                'uri' => \Request::route()->uri
           ]);
        }
    }

    public function updateColor(Request $request) {

        $gameData = GamePrediction::find($request->id);
        if($gameData && $gameData->status != 2) {
            $gameData->update(['result_color' => $request->result_color]);
            ColorPrediction::where('game_id', $request->id)->update(['result_color' => $request->result_color]);
            Session::flash('message', 'Result Color Updated Successfully');
            return back();
        }
        Session::flash('message', 'You can not update the color after game end');
        return back();
    }
}
