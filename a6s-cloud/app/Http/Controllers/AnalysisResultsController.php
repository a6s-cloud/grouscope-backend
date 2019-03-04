<?php

namespace App\Http\Controllers;

use App\AnalysisResults;
use Illuminate\Http\Request;

class AnalysisResultsController extends Controller
{
    public function index()
    {
        return AnalysisResults::all();
    }
    public function show($id)
    {
        return AnalysisResults::find($id);
    }
}
