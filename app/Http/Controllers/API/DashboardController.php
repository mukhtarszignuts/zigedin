<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Skill;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // 
        $data = [
            'users'  => User::count(),
            'skills'  => Skill::count(),
        ];

        return ok('get dashboard details successfully.', $data);
    }

    // delete attachment 
    
}
