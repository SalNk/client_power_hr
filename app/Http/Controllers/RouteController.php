<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteController extends Controller
{
    public function index() {
        $jobs = Job::with('user', 'category') -> where('user_id',Auth::user()->id)->latest()-> get();
        $candidates = JobUser::whereIn('job_id', $jobs->pluck('id'))->get();
        return view('index',[
            'jobs' => $jobs,
            'candidates' => $candidates,
        ]);
    }

    public function routes(Request $request) {
        if(view()->exists($request->path())) {
            return view($request->path());
        } else {
            return abort(404);
        }
    }
}
