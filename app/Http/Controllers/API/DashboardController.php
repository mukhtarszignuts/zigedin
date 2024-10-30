<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Skill;

class DashboardController extends Controller
{
    /**
     * Display dashboard statistics.
     *
     * This function retrieves the total counts of registered users and available skills,
     * which are then returned as part of the dashboard data.
     *
     * @param Request $request The HTTP request instance.
     * @return \Illuminate\Http\JsonResponse The dashboard data with user and skill counts.
     */
    public function dashboard(Request $request)
    {
        // Retrieve counts of users and skills for dashboard statistics
        $data = [
            'users'  => User::count(),  // Total number of registered users
            'skills'  => Skill::count(), // Total number of available skills
        ];

        return ok('Retrieved dashboard details successfully.', $data);
    }
}
