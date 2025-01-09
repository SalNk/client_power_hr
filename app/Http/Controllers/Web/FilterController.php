<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;

class FilterController extends Controller
{
    public function create()
    {
        $skills = Skill::all();


        return view('filter.create', [
            'skills' => $skills
        ]);
        return view('filter.create',);
    }

    public function search(Request $request)
    {
        try {
            $users = User::with('skills', 'personne','profile','salary');
            $skills = $request->input('skills');

            if ($request->has('skills')) {
                $users->whereHas('skills', function($query) use ($skills) {
                    $query->whereIn('name', $skills);
                });
            }

            if ($request->name != null){
                $users->where(function($query) use ($request) {
                    $query->whereHas('personne', function($q) use ($request) {
                        $q->where('name', 'like', '%'.$request->name.'%')
                            ->orWhere('prenom', 'like', '%'.$request->name.'%')
                            ->orWhere('postnom', 'like', '%'.$request->name.'%');
                    })
                    ->orWhere('email', 'like', '%'.$request->name.'%');
                });
            }

            if ($request->matricule != null){
                $users->where(function($query) use ($request) {
                    $query->whereHas('personne', function($q) use ($request) {
                        $q->where('matricule', 'like', '%'.$request->matricule.'%');
                    });
                });
            }

            if ($request->role != null ) {
                $users->where(function($query) use ($request) {
                    $query->whereHas('profile', function($q) use ($request) {
                        $q->where('title', 'like', '%'.$request->role.'%');
                    });
                });
            }

            if ($request->salary_max != null  || $request->salary_min != null) {
                if($request->salary_max == null){
                    $salary_max = 0;
                }else{
                    $salary_max = $request->salary_max;
                }
                if($request->salary_min == null){
                    $salary_min = 0;
                }else{
                    $salary_min = $request->salary_min;
                }

                $users->where(function($query) use ($salary_max, $salary_min) {
                    $query->whereHas('salary', function($q) use ($salary_max, $salary_min) {
                        $q->where('max', '<=', $salary_max)
                        ->where('min', '>=', $salary_min);
                    });
                });
            }

            if ($request->city != null ) {
                $users->where(function($query) use ($request) {
                    $query->whereHas('profile', function($q) use ($request) {
                        $q->where('location', 'like', '%'.$request->city.'%');
                    })->orWhereHas('personne', function($q) use ($request) {
                        $q->where('ville', 'like', '%'.$request->city.'%');
                    });
                });
            }
            return redirect()->back()->with(['results'=>$users->get()]);
            return $users->get();
        } catch (\Exception $e) {
            // Handle the error, e.g., log it and return a user-friendly message
            return response()->json(['error' => 'An error occurred while searching.' .$e->getMessage()], 500);
        }
    }
}
