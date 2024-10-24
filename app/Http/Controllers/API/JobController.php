<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\ListingApiTrait;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    use ListingApiTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //listing validation from trait
        $this->ListingValidation();

        $query = Job::query();

        // filter by employer id 
        if ($request->emp_id) {
            $query->wher('emp_id', $request->emp_id);
        }

        // filter by status 
        if ($request->status) {
            $query->where('status', $request->status);
        }
        // date range 
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }


        // search 
        if (isset($request->search)) {
            $search = $request->search;
            $query = $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        $jobs = $this->filterSortPagination($query);

        return ok('get jobs successfully..!', [
            'jobs'  => $jobs['query']->get(),
            'count' => $jobs['count']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $this->validate($request, [
            'emp_id'        => 'required|exists:employers,id', // Assuming `employers` table exists
            'job_type'      => 'required|in:F,P,FL', // Full-time, Part-time, Freelance
            'work_mode'     => 'required|in:WFH,WFO,HYB', // Work from home, office, hybrid
            'title'         => 'required|string|max:128',
            'description'   => 'required|string|max:128|unique:jobs,description',
            'location'      => 'nullable|string|max:15',
            'salary_range'  => 'nullable|string|max:128',
            'posted_at'     => 'nullable|date',
            'status'        => 'required|in:O,C' // Open, Close
        ]);

        // Create new job
        $job = Job::create($request->all());

        return ok('Job Create Successfully.!', $job);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $job = Job::findOrFail($id);
        return ok('get Job Successfully.!', $job);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Validate incoming data
        $this->validate($request, [
            'id'            => 'required|exists:jobs,id',
            'emp_id'        => 'required|exists:employers,id',
            'job_type'      => 'required|in:F,P,FL',
            'work_mode'     => 'required|in:WFH,WFO,HYB',
            'title'         => 'required|string|max:128',
            'description'   => 'required|string|max:255',
            'location'      => 'nullable|string|max:15',
            'salary_range'  => 'nullable|string|max:128',
            'posted_at'     => 'nullable|date',
            'status'        => 'required|in:O,C'
        ]);

        // Find and update job
        $job = Job::find($request->id);

        $job->update($request->all());

        return ok('Job Updated successfully.!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $job = Job::findOrFail($id);

        // Delete job
        $job->delete();

        return ok('Job deleted successfully.');
    }
}
