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
        //listing validation from trait
        $this->ListingValidation();

        $query = Comment::with('user:id,first_name,last_name,profile_image', 'post');

        // filter by users id 
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        } else {
            $query->where('user_id', auth()->user()->id);
        }

        //  filter by posts id 
        if ($request->post_id) {
            $query->where('post_id', $request->post_id);
        }

        // search 
        if (isset($request->search)) {
            $search = $request->search;
            $query = $query->where(function ($query) use ($search) {
                $query->where('content', 'like', '%' . $search . '%');
            });
        }

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
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required',
        ]);

        $request['user_id'] = auth()->user()->id;

        $comment = Comment::create($request->all());

        return ok('comment created successfully.!', $comment);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $comment = Comment::with('user:id,first_name,last_name,profile_image', 'post')->findOrFail($id);

        return ok('get comment succussfully.!', $comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id'      => 'required|exists:comments,id',
            'post_id' => 'required|exists:posts,id',
            'content' => 'required',
        ]);

        $comment = Comment::findOrFail($request->id);

        $comment->update($request->only('content'));

        return ok('comment updated successfully.!', $comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $comment = Comment::findOrFail($id);

        $comment->delete();

        return ok('comment delete successfully.!');
    }
}
