<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Employer;
use Illuminate\Http\Request;
use App\Models\WorkExperience;
use App\Http\Controllers\Controller;
use App\Http\Traits\ListingApiTrait;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use ListingApiTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //listing validation from trait
        $this->ListingValidation();

        $query =  User::query()->select('id', 'first_name', 'last_name', 'email', 'phone', 'city', 'role', 'is_active', 'created_at', 'profile_image')->with('employer');

        // search 
        if (isset($request->search)) {
            $search = $request->search;
            $query = $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        // role
        if (isset($request->role)) {
            $query->where('role', $request->role);
        }
        // status
        if (isset($request->is_active)) {
            $query->where('is_active', $request->is_active);
        }
        // date range 
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $users = $this->filterSortPagination($query);

        return ok('get users list successfully', [
            'users'     =>  $users['query']->get(),
            'count'     =>  $users['count'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $this->validate($request, [
            'email'              => 'required|email|unique:users,email|max:250',
            'profile_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'nullable|string|max:255',
            'role'               => 'required|in:A,E,C',
            'phone'              => 'required|string|max:15',
            'password'           => 'nullable|string|min:8',
            'city'               => 'nullable|string|max:100',
            'headline'           => 'nullable|string|max:255',
            'summary'            => 'nullable|string|max:500',

        ]);

        if ($request->role === 'E') {
            // Conditional validation for employer fields
            $this->validate($request, [
                'employer.name'      => 'required|string|unique:employers,name,' . ($employerId ?? 'NULL') . '|max:255',
                'employer.industry'  => 'required|string|max:255',
                'employer.location'  => 'required|string|max:255',
                'employer.website'   => 'nullable|max:255',
                'employer.logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        }

        // Prepare payload for user creation
        $payload = $request->only(
            'first_name',
            'last_name',
            'email',
            'phone',
            'role',
            'is_active',
            'city',
            'headline',
            'summary'
        );

        $payload['password'] = isset($request->password) ? bcrypt($request->password) : bcrypt('password');

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $file =  $request->file('profile_image');
            $directory = 'profile_images';
            $filename = mt_rand(1000000000, time()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs($directory, $filename, 'public');
            $payload['profile_image'] = $filename;
        }

        // Create user
        $user = User::create($payload);

        // check if role employer
        if ($request->role === 'E') {
            $empPayload = $request->only('employer');

            if ($request->hasFile('employer.logo')) {
                $file = $request->file('employer.logo');
                $directory = 'employer_images';
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs($directory, $filename, 'public');
                $empPayload['employer']['logo'] = $filename;
            }
            // Attach user_id to employer payload
            $empPayload['employer']['user_id'] = $user->id;

            // Create Employer record
            Employer::create($empPayload['employer']);
        }
        return ok('User Create SuccessFully..', $user);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $user = User::with('employer', 'skills', 'educations')->with('inviteConnections', function ($qry) {
            $qry->where('status', 'P');
        })->findOrFail($id);

        // Fetch experiences and group by company_name, ordered by created_at
        $experiences = WorkExperience::where('user_id', $id)
            ->select('company_name', 'title', 'description', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('company_name');

        // Attach experiences to the user
        $user->experiences = $experiences;
        return ok('get user successfully..!', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $user = User::findOrFail($request->id);

        // If the user has an employer, get the employer record (assuming there is a one-to-one relationship)
        $employerId = ($user->role === 'E' && $user->employer) ? $user->employer->id : null;

        // Validate incoming data
        $this->validate($request, [
            'email'              => 'required|email|unique:users,email,' . $user->id . '|max:250',
            'profile_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'nullable|string|max:255',
            'role'               => 'required|in:A,E,C',
            'phone'              => 'required|string|max:15',
            'password'           => 'nullable|string|min:8',
            'city'               => 'nullable|string|max:100',
            'headline'           => 'nullable|string|max:255',
            'summary'            => 'nullable|string|max:500',

        ]);

        if ($request->role === 'E') {
            // Conditional validation for employer fields
            $this->validate($request, [
                'employer.name'      => 'required|string|unique:employers,name,' . ($employerId ?? 'NULL') . '|max:255',
                'employer.industry'  => 'required|string|max:255',
                'employer.location'  => 'required|string|max:255',
                'employer.website'   => 'nullable|max:255',
                'employer.logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        }


        $payload =  $request->only('first_name', 'last_name', 'email', 'role', 'phone', 'city', 'headline', 'is_active');

        if ($request->hasFile('profile_image')) {
            // check if user exits then delete this user image 
            if ($user->profile_image) {
                $Image = 'profile_images/' . $user->profile_image;
                Storage::disk('public')->delete($Image);
            }
            // New image store 
            $file       =  $request->file('profile_image');
            $directory  = 'profile_images';
            $filename   = mt_rand(1000000000, time()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs($directory, $filename, 'public');
            $payload['profile_image'] = $filename;
        }

        // Update user
        $user->update($payload);

        // Check if role is 'Employer' and update employer details
        if ($request->role === 'E') {
            $empPayload = $request->only('employer');

            // Handle employer logo upload and deletion of old logo
            if ($request->hasFile('employer.logo')) {
                $employer = $user->employer; // Assuming user has an `employer` relationship

                // Delete old logo if it exists
                if ($employer && $employer->logo) {
                    Storage::disk('public')->delete('employer_images/' . $employer->logo);
                }

                // Store new employer logo
                $file = $request->file('employer.logo');
                $directory = 'employer_images';
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs($directory, $filename, 'public');
                $empPayload['employer']['logo'] = $filename;
            }

            // Update or create employer record
            if ($user->employer) {
                $user->employer->update($empPayload['employer']);
            } else {
                $empPayload['employer']['user_id'] = $user->id;
                Employer::create($empPayload['employer']);
            }
            $user->load('employer');
        }

        return ok('User Update Successfully.!', $user);
    }

    /**
     * Remove user  in database.
     */
    public function delete($id, User $user)
    {

        $user = User::find($id);

        if (!$user) {
            return error('User not found', [], 'validation');
        }
        // profile image  
        if ($user->profile_image) {
            $Image = 'profile_images/' . $user->profile_image;
            Storage::disk('public')->delete($Image);
        }

        // check if employer then delete 
        if ($user->employer) {
            // check logo 
            if ($user->employer->logo) {
                $Image = 'employer_images/' . $user->employer->logo;
                Storage::disk('public')->delete($Image);
            }
            // delete employer
            $user->employer->delete();
        }

        $user->delete();

        return ok('User Delete SuccessFully..!');
    }

    // Bluk delete 
    public function bulkDelete(Request $request)
    {

        $this->validate($request, [
            'ids'   => 'required|array',
            'ids.*' => 'required|exists:users,id',
        ]);

        $users = User::whereIn('id', $request->ids)->get();

        foreach ($users as $key => $user) {
            if ($user->profile_image) {
                $Image = 'profile_images/' . $user->profile_image;
                Storage::disk('public')->delete($Image);
            }

            $user->delete();
        }

        return ok('User Bluck Delete Successfully..!');
    }

    /**
     * Status Change
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statusChange($id)
    {
        $category = User::find($id);

        if (!$category) {
            return error('User not found', [], 'validation');
        }

        $category->update(['is_active' => !$category->is_active]);

        $message =  $category->is_active ? 'Active' : 'In Active';

        return ok('Status ' . $message . ' Successfuly');
    }
}
