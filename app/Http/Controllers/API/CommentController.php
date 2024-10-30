<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\ListingApiTrait;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use ListingApiTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate the request parameters for listing from a trait
        $this->ListingValidation();

        // Start a query to retrieve comments, eager loading related user and post data
        $query = Comment::with('user:id,first_name,last_name,profile_image', 'post');

        // Filter comments by user_id if provided; otherwise, use the authenticated user's ID
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        } else {
            // Default to the currently authenticated user's ID
            $query->where('user_id', auth()->user()->id);
        }

        // Filter comments by post_id if provided
        if ($request->post_id) {
            $query->where('post_id', $request->post_id);
        }

        // Implement search functionality for comment content
        if (isset($request->search)) {
            $search = $request->search;
            $query = $query->where(function ($query) use ($search) {
                // Search within the comment content using a 'like' query
                $query->where('content', 'like', '%' . $search . '%');
            });
        }

        // Apply filtering, sorting, and pagination to the query
        $comments = $this->filterSortPagination($query);

        return ok('fetch comment listing successfully..!', [
            'comments'  => $comments['query']->get(),
            'count'     => $comments['count']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required',
        ]);

        // Assign the authenticated user's ID to the request data
        $request['user_id'] = auth()->user()->id;

        // Create a new comment using the validated request data
        $comment = Comment::create($request->all());

        // Return a success response with the created comment data
        return ok('comment created successfully.!', $comment);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Retrieve the comment by ID, including related user and post data
        $comment = Comment::with('user:id,first_name,last_name,profile_image', 'post')->findOrFail($id);

        return ok('get comment succussfully.!', $comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'id'      => 'required|exists:comments,id',
            'post_id' => 'required|exists:posts,id',
            'content' => 'required',
        ]);

        $comment = Comment::findOrFail($request->id);

        // Update the comment's content with the validated data
        $comment->update($request->only('content'));

        return ok('comment updated successfully.!', $comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        // Retrieve the comment by ID, throwing a 404 error if not found
        $comment = Comment::findOrFail($id);

        // Delete the retrieved comment from the database
        $comment->delete();

        return ok('comment delete successfully.!');
    }
}
