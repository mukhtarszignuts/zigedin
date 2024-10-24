<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\ListingApiTrait;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    use ListingApiTrait;

    public $directory;

    public function __construct()
    {
        $this->directory = "posts";
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //listing validation from trait
        $this->ListingValidation();

        $query = Post::with('user:id,first_name,last_name,profile_image', 'attachments')->withCount('comments')->withCount('likes');

        // filter by users id 
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        } else {
            $query->where('user_id', auth()->user()->id);
        }

        // filter by status 
        if ($request->is_active) {
            $query->where('is_active', $request->is_active);
        }
        // date range 
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // search 
        if (isset($request->search)) {
            $search = $request->search;
            $query = $query->where(function ($query) use ($search) {
                $query->where('content', 'like', '%' . $search . '%')
                    ->orWhere('tags', 'like', '%' . $search . '%')
                    ->orWhere('mentions', 'like', '%' . $search . '%');
            });
        }

        $posts = $this->filterSortPagination($query);

        return ok('fetch posts listing successfully..!', [
            'posts'  => $posts['query']->get(),
            'count'  => $posts['count']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content'       => 'required|string',
            'tags'          => 'nullable|string',
            'mentions'      => 'nullable|string',
            'is_attachment' => 'required|boolean',
            'visibility'    => 'in:P,C',
            'attachments'   => 'required_if:is_attachment,true|array', // Attachments required if is_attachment is true
            'attachments.*' => 'file|max:2048', // Max 2Mb
        ]);

        $request['user_id'] = auth()->user()->id;

        $post = Post::create($request->all());

        // Check if attachments are provided and if is_attachment is true
        if (isset($request->is_attachment) && $request->attachments) {
            $attachmentsData = [];

            foreach ($request->attachments as $file) {
                if ($file) {
                    // Generate a unique filename
                    $filename = mt_rand(1000000000, time()) . '.' . $file->getClientOriginalExtension();

                    // Store the file
                    $file->storeAs($this->directory, $filename, 'public');

                    // Collect attachment data
                    $attachmentsData[] = [
                        'directory' => $this->directory,
                        'file_name' => $filename,
                    ];
                }
            }

            // Bulk insert attachments
            $post->attachments()->createMany($attachmentsData);
        }

        return ok('create post successfully.', $post);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            //code...
            $post = Post::with('user:id,first_name,last_name,profile_image', 'comments', 'likes', 'attachments')
                ->withCount('comments')->withCount('likes')->findOrFail($id);
    
            return ok('Post get successfully.', $post);
        } catch (\Throwable $th) {
            //throw $th;
            return error($th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id'            => 'required|exists:posts,id',
            'user_id'       => 'required|exists:users,id',
            'content'       => 'required|string',
            'tags'          => 'nullable|string',
            'mentions'      => 'nullable|string',
            'is_attachment' => 'required|boolean',
            'visibility'    => 'in:P,C',
            'attachments.*' => 'file|max:2048', // Max 2Mb
        ]);

        $post = Post::findOrFail($request->id);

        // Update post fields
        $post->update($request->only(['user_id', 'content', 'tags', 'mentions', 'is_attachment', 'visibility']));

        // Check if attachments are provided and if is_attachment is true
        if (isset($request->is_attachment) && $request->attachments) {
            //! Optionally clear existing attachments if needed
            // $post->attachments()->delete(); // Uncomment to delete old attachments

            $attachmentsData = [];

            foreach ($request->attachments as $file) {
                if ($file) {
                    // Generate a unique filename
                    $filename = mt_rand(1000000000, time()) . '.' . $file->getClientOriginalExtension();

                    // Store the file
                    $file->storeAs($this->directory, $filename, 'public');

                    // Collect attachment data
                    $attachmentsData[] = [
                        'directory' => $this->directory,
                        'file_name' => $filename,
                    ];
                }
            }

            // Bulk insert new attachments
            $post->attachments()->createMany($attachmentsData);
        }

        return ok('Post updated successfully.', $post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $post = Post::findOrFail($id);

        // Optionally delete the attachments before deleting the post
        foreach ($post->attachments as $attachment) {
            // Delete the file from storage
            Storage::disk('public')->delete($attachment->directory . '/' . $attachment->file_name);
        }

        // delete attachment 
        $post->attachments()->delete();

        // delete comments 
        $post->comments()->delete();

        // Delete like 
        $post->likes()->detach();

        // Delete the post
        $post->delete();

        return ok('Post deleted successfully.');
    }

    /**
     * Like a post
     */
    public function likePost($id)
    {
        $user_id = auth()->user()->id;

        // Check if the post exists
        $post = Post::findOrFail($id);

        // Check if the user has already liked the post
        $likeExists = $post->likes()->where('user_id', $user_id)->exists();

        if ($likeExists) {
            $post->likes()->detach($user_id);
            return error('Post Unliked successfully.');
        }

        // Add the like
        $post->likes()->attach($user_id);

        return ok('Post liked successfully.');
    }
}
