<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Connection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\ListingApiTrait;
use Illuminate\Support\Facades\Auth;

class ConnectionController extends Controller
{
    use ListingApiTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //listing validation from trait
        $this->ListingValidation();

        $query = Connection::whereNot('status', 'R');

        $sender_qry = (clone $query)->where('user_id', auth()->user()->id)->with('friend:id,first_name,last_name,profile_image,phone,headline');
        $receiver_qry = (clone $query)->where('status', 'P')->where('connection_id', auth()->user()->id)->with('user:id,first_name,last_name,profile_image,phone,headline');

        // need to sorting last requeset send for connection
        $request['sort_field'] = 'request_date';
        $request['sort_order'] = 'desc';

        $sender   = $this->filterSortPagination($sender_qry);
        $receiver = $this->filterSortPagination($receiver_qry);

        $data = [
            'sender'         => $sender['query']->get(),
            'sender_count'   => $sender['count'],
            'receiver'       => $receiver['query']->get(),
            'receiver_count' => $receiver['count']
        ];

        return ok('get connection list', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'connection_id' => [
                'required',
                'exists:users,id',
                // Ensure user_id and connection_id are not the same
                function ($attribute, $value, $fail) {
                    if (auth()->user()->id == $value) {
                        $fail('You cannot connect with yourself.');
                    }
                }
            ],
            'status' => 'nullable|in:A,P,R',
        ]);

        $user  = User::select('id', 'first_name', 'last_name', 'profile_image')->findOrfail(auth()->user()->id);

        $user->connections()->syncWithoutDetaching([
            $request->connection_id => [
                'request_date' => Carbon::now(),
            ]
        ]);

        //! Need to discuss if connect or disconnect with same api

        //  // Load the connections ordered by the latest request date
        // $user->load(['connections' => function($query) {
        //     $query->orderBy('pivot_request_date', 'desc'); // Ensure to order by the pivot table's request_date
        // }]);

        return ok('Connection created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'connection_id' => [
                'required',
                'exists:users,id',
                // Ensure user_id and connection_id are not the same
                function ($attribute, $value, $fail) {
                    if (auth()->user()->id == $value) {
                        $fail('You cannot connect with yourself.');
                    }
                }
            ],
            'status' => 'nullable|in:A,R',
        ]);

        $user_id = auth()->user()->id;

        $user = User::findOrFail($user_id);


        // Update the status in the pivot table
        $updated = $user->inviteConnections()->syncWithoutDetaching([
            $request->connection_id => [
                'status' => $request->status,
            ]
        ]);

        // Update the pivot table
        // $updated = $user->inviteConnections()->updateExistingPivot($request->connection_id, [
        //     'status' => $request->status,
        // ]);

        if ($updated) {
            return ok('Connection updated successfully.');
        } else {
            return error('No changes made to the connection..');
        }
    }

    // delete connection 
    public function delete($id)
    {
        $user = User::findOrFail(auth()->user()->id);
        // Update the status in the pivot table
        $updated = $user->connections()->detach([$id]);

        if ($updated) {
            return ok('Connection delete successfully.');
        } else {
            return error('Connection delete failed.');
        }
    }

    // suggested connection 
    public function suggestedConnection(Request $request)
    {
        // Get the authenticated user
        $authUser = auth()->user();

        // Get the IDs of users the authenticated user is connected to
        $connectedUserIds = $authUser->connections()->pluck('id')->toArray();

        // Get the IDs of users who have invited the authenticated user
        $invitedUserIds = $authUser->inviteConnections()->pluck('id')->toArray();

        // Combine all IDs to exclude
        $excludedUserIds = array_merge([$authUser->id], $connectedUserIds, $invitedUserIds);

        // Fetch suggested connections
        $users = User::select('id', 'first_name', 'last_name', 'profile_image', 'email', 'headline', 'role')
            ->whereNotIn('id', $excludedUserIds)
            ->with('connections', 'inviteConnections')
            ->get();

        return ok('Suggested connections found', $users);
    }
    // public function suggestedConnection(Request $request)
    // {
    //     $query = User::query();

    //     $authUser = (clone $query)->where('id', auth()->user()->id)->first();

    //     $connectedUserIds = $authUser->connections()->pluck('connection_id')->toArray();
    //     $invitedUserIds = $authUser->inviteConnections()->pluck('connection_id')->toArray();

    //     // Combine both arrays to have a single array of IDs to exclude
    //     $excludedUserIds = array_merge([$authUser->id], $connectedUserIds, $invitedUserIds);


    //     // $user = User::select('id', 'first_name', 'last_name', 'profile_image', 'email','headline','role')
    //     // ->whereNotIn('id', $excludedUserIds)
    //     // ->with('connections', 'inviteConnections')->get();

    //     $user = (clone $query)->select('id', 'first_name', 'last_name', 'profile_image', 'email','headline','role')
    //     ->whereNotIn('id', $excludedUserIds)
    //     ->with('connections', 'inviteConnections')->get();

    //     return ok('Suggested connections found', $user);
    // }
}
