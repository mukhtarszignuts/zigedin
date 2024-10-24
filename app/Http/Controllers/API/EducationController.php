<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\ListingApiTrait;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    use ListingApiTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //listing validation from trait
        $this->ListingValidation();

        $query = Education::with('user:id,first_name,last_name,email,profile_image');

        // search 
        if (isset($request->search)) {
            $search = $request->search;
            $query = $query->where(function ($query) use ($search) {
                $query->where('school_name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // date range 
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $educations = $this->filterSortPagination($query);

        return ok('get education listing..!', [
            'educations' => $educations['query']->get(),
            'count'      => $educations['count']
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
            'school_name'    => 'required|string|max:255',
            'degree'         => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_date'     => 'required|date_format:Y-m-d',
            'end_date'       => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'description'    => 'nullable|string|max:500',
        ]);

        $request['user_id']=auth()->user()->id;
        // Create new education record
        $education = Education::create($request->all());

        return ok('Education record created successfully.', $education);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $education = Education::findOrFail($id);
        return ok('get education record successfully.', $education);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Validate incoming data
        $this->validate($request, [
            'id'             => 'required|exists:education,id',
            // 'user_id'        => 'required|exists:users,id',
            'school_name'    => 'required|string|max:255',
            'degree'         => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_date'     => 'required|date_format:Y-m-d',
            'end_date'       => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'description'    => 'nullable|string|max:500',
        ]);
        // Find and update education record
        $education = Education::findOrFail($request->id);

        $education->update($request->all());

        return ok('Education record updated successfully.', $education);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $education = Education::findOrFail($id);
        $education->delete();

        return ok('Education record deleted successfully.');
    }
}
