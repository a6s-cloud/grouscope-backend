<?php

namespace App\Http\Controllers;

use App\AnalysisResults;
use Illuminate\Http\Request;

class AnalysisResultListsController extends Controller
{
    public function index(Request $request)
    {
        $query = AnalysisResults::query();
        if ($request->has('search_word')) {
            $query = $query->where('analysis_word', 'LIKE', "%{$request->query('search_word')}%");
        }

        $query = $query->paginate(10);
        return \Response::json($query);
    }
}
