<?php
namespace App\Http\Controllers;

use App\AnalysisResults;
use App\Tweets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalysisResultsController extends Controller
{
    public function index()
    {
        return AnalysisResults::all();
    }
    public function show($id)
    {
        $results = AnalysisResults::find($id);

        $tweet_users = DB::table('tweets')
                        ->select(DB::raw('user_name,user_account,count(*) as tweet_count'))
                        ->where('analysis_result_id', '=', $id)
                        ->groupBy('user_name','user_account')
                        ->orderBy('tweet_count', 'desc')
                        ->get();

        $results['user_ranking'] = $tweet_users;
        return \Response::json($results);
    }
    public function delete($id)
    {
        $tweets_query = Tweets::query();
        $tweets_query->where('analysis_result_id', '=', $id)->delete();

        AnalysisResults::find($id)->delete();
        return 204;
    }
}
