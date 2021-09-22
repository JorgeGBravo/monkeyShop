<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function arrayPositionAuth(){
        $position = Auth::id() - 1;
        //log::info('position');
        //log::info($position);
        return $position;
    }

    public function getIsAdmin(){
       $admin = User::select('isAdmin')
            ->where('id', Auth::id())
            ->get();
        if ($admin[0]->isAdmin === 0) {
            return response()->json(['message' =>'You do not have Administrator permissions'], 403);
        }
        return $admin;
    }
}
