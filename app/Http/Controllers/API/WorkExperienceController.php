<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\WorkExperience;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Traits\ListingApiTrait;

class WorkExperienceController extends Controller
{
    use ListingApiTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //listing validation from trait
        $this->ListingValidation();

        $query = WorkExperience::with('user:id,first_name,last_name,email,profile_image');

        // search 
        if (isset($request->search)) {
            $search = $request->search;
            $query = $query->where(function ($query) use ($search) {
                $query->where('job_title', 'like', '%' . $search . '%')
                    ->orWhere('company_name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // user id filter by 
        $user_id = $request->user_id??auth()->user()->id;
        if ($user_id) {
            $query->where('user_id',$user_id);
        }
        // date range 
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $workexperience = $this->filterSortPagination($query);

        return ok('get Work experiences listing..!', [
            'workexperience' => $workexperience['query']->get()->groupBy('company_name'),
            'count'          => $workexperience['count']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $this->validate($request, [
            // 'user_id'        => 'required|exists:users,id',
            'title'          => 'required|string|max:255',
            'employment_type' => 'required|in:F,P,FL,SE,I,T', //'Full-time', 'Part-time', 'Freelance','SelfEmployed','Intern','Trainee'
            'company_name'   => 'required|string|max:255',
            'start_date'     => 'required|date|date_format:Y-m-d',
            'end_date'       => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
            'description'    => 'nullable|string|max:500',
            'location'       => 'nullable|string|max:128',
            'location_type'  => 'required|in:OS,RMT,HYB', // On-Site Remote Hybrid
        ]);
        $request['user_id'] = auth()->user()->id;
        // Create new work experience
        $workExperience = WorkExperience::create($request->all());

        return ok('Work experience created successfully.', $workExperience);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $workExperience = WorkExperience::with('user:id,first_name,last_name,email,profile_image')->findOrFail($id);

        return ok('get WorkExperience successfully.', $workExperience);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Validate incoming data
        $this->validate($request, [
            'id'             => 'required|exists:work_experiences,id',
            // 'user_id'        => 'required|exists:users,id',
            'title'          => 'required|string|max:255',
            'employment_type' => 'required|in:F,P,FL,SE,I,T', //'Full-time', 'Part-time', 'Freelance','SelfEmployed','Intern','Trainee'
            'company_name'   => 'required|string|max:255',
            'start_date'     => 'required|date|date_format:Y-m-d',
            'end_date'       => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
            'description'    => 'nullable|string|max:500',
            'location'       => 'nullable|string|max:128',
            'location_type'  => 'required|in:OS,RMT,HYB', // On-Site Remote Hybrid
        ]);

        // Find and update work experience
        $workExperience = WorkExperience::findOrFail($request->id);

        $workExperience->update($request->all());

        return ok('Work experience updated successfully.', $workExperience);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $workExperience = WorkExperience::findOrFail($id);

        $workExperience->delete();

        return ok('Work experience deleted successfully.');
    }
}
