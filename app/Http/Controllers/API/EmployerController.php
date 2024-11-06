<?php

namespace App\Http\Controllers\API;

use App\Models\Employer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\ListingApiTrait;
use Illuminate\Support\Facades\Storage;

class EmployerController extends Controller
{
    use ListingApiTrait;

    public $directory;

    public function __construct()
    {
        $this->directory = "employer_images";
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       //listing validation from trait
        $this->ListingValidation();

        $query = Employer::query();

        // search 
        if (isset($request->search)) {
            $search = $request->search;
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        $employer = $this->filterSortPagination($query);

        return ok('fetch page listing successfully..!', [
            'pages'  => $employer['query']->get(),
            'count'  => $employer['count']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'              => 'required|in:company,school,showcase',
            'user_id'           => 'nullable|exists:users,id',
            'name'              => 'required|string|unique:employers,name',
            'industry'          => 'required|string|max:255',
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'location'          => 'required|string|max:255',
            'website'           => 'nullable|max:255',
            'organization_size' => 'required_if:type,company|max:255',
            'organization_type' => 'required_if:type,company|max:255',
            'public_url'        => 'required|unique:employers,public_url|max:255',
        ]);

         // Handle logo upload
         if ($request->hasFile('logo')) {
            $file =  $request->file('logo');
            $filename = mt_rand(1000000000, time()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs($this->directory, $filename, 'public');
            $validated['logo'] = $filename;
        }

        $validated['user_id'] = $request->user_id?$request->user_id:auth()->user()->id;


        $employer = Employer::create($validated);
        
        return ok('Page created successfully..!',$employer);
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $page = Employer::findOrFail($id);

        return ok('get page details successfully..!', ['page' => $page]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $employer =  Employer::findOrFail($request->id);

        if(!$employer){
            return error('Page Not Found.');
        }
        
        $validated = $request->validate([
            'type'              => 'required|in:company,school,showcase',
            'user_id'           => 'nullable|exists:users,id',
            'name'              => 'required|string|unique:employers,name',
            'industry'          => 'required|string|max:255',
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'location'          => 'required|string|max:255',
            'website'           => 'nullable|max:255',
            'organization_size' => 'required_if:type,company|max:255',
            'organization_type' => 'required_if:type,company|max:255',
            'public_url'        => 'required|unique:employers,public_url|max:255',
        ]);

         // Handle logo upload
         if ($request->hasFile('logo')) {
            // check if user exits then delete this user image 
            if ($employer->logo) {
                $Image = $this->directory.'/' . $employer->logo;
                Storage::disk('public')->delete($Image);
            }
            // New image store 
            $file       =  $request->file('logo');
            $filename   = mt_rand(1000000000, time()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs($this->directory, $filename, 'public');
            $validated['logo'] = $filename;
        }

        $employer->update($validated);

        return ok('Page updated successfully..!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $employer = Employer::findOrFail($id);
         
        // logo  
         if ($employer->logo) {
            $Image = $this->directory.'/' . $employer->logo;
            Storage::disk('public')->delete($Image);
        }

        $employer->delete();

        return ok('Page deleted successfully..!');
    }
}
