<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Job;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Skill::query();
        // search 
        if (isset($request->search)) {
            $search = $request->search;
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        $skills = $query->get();

        return ok('skill fetch successfully.!', $skills);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $this->validate($request, [
            'user_id' => 'nullable|exists:users,id',
            'job_id'  => 'nullable|exists:jobs,id',
            'name'    => 'required|string|max:255',
        ]);

        $skill = Skill::updateOrCreate(
            ['name' => $request->input('name')], // Attributes to check for existence
            [] // Attributes to update or set if creating
        );

        // Attach skill to user if user_id is provided and user exists
        $user_id = isset($request->user_id) ? $request->user_id :auth()->user()->id;
        
        $user = User::find($user_id);
        if ($user) {
            $user->skills()->attach($skill->id);
        }

        // Attach skill to job if job_id is provided and job exists
        if ($request->job_id) {
            $job = Job::find($request->job_id);
            if ($job) {
                $job->skills()->attach($skill->id);
            }
        }

        return ok('Skill Added successfully.', $skill);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Find skill by ID
        $skill = Skill::findOrFail($id);
        return ok('get skill successfully.', $skill);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
       
        // Validate incoming data
        $this->validate($request, [
            'id' => 'required|exists:skills,id',
            'name' => 'required|string|max:255',
        ]);

         // Find skill by ID
         $skill = Skill::findOrFail($request->id);

        // Update skill
        $skill->update([
            'name' => $request->input('name'),
        ]);

        return ok('Skill updated successfully.', $skill);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        // Find skill by ID
        $skill = Skill::findOrFail($id);

        // Detach skill from users
        $skill->users()->detach();

        // Detach skill from jobs
        $skill->job()->detach();

        // Delete skill
        $skill->delete();

        return ok('Skill deleted successfully.');
    }
}
