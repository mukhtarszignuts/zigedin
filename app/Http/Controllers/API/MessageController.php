<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\ListingApiTrait;
use App\Events\NewMessage;

class MessageController extends Controller
{
    use ListingApiTrait;

    /**
     * Get Chat Message ChatLog
     */
    public function chat($id)
    {

        $user = User::findOrFail($id);

        $messages = Message::with('sender:id,first_name,last_name,email,profile_image', 'receiver:id,first_name,last_name,email,profile_image')

            ->where(function ($query) use ($id) {
                $query->where('receiver_id', $id)->where('sender_id', auth()->user()->id);
            })
            ->orWhere(function ($query) use ($id) {
                $query->where('receiver_id', auth()->user()->id)->where('sender_id', $id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Map the messages to the desired format
        $mappedMessages = $messages->map(function ($message) {
            return [
                'message' => $message->message, // Adjust field name as needed
                'time' => $message->created_at->format('D M d Y H:i:s \G\M\T+0000 (O)'), // Adjust format as needed
                'senderId' => $message->receiver_id, // Updated to sender_id
                'feedback' => [
                    'isSent' => $message->is_sent,
                    'isDelivered' => $message->is_delivered,
                    'isSeen' => $message->is_seen,
                ],
            ];
        });


        // Prepare the chat data
        $chat = [
            'id' => $user->id,
            'userId' => auth()->user()->id, // Assuming the senderId is the authenticated user
            'unseenMsgs' => $messages->where('is_seen', false)->count(),
            'messages' => $mappedMessages
        ];

        // Prepare the profile user data
        $profileUser = [
            'id'        => $user->id,
            'avatar'    => $user->image_url,
            'fullName'  => $user->fullName,
            'role'      => $user->role,
            'about'     => $user->email,
            'status'    => 'online',
        ];

        return ok('get message successfully.!', ['chat' => $chat, 'contact' => $profileUser]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'sender_id'    => 'required|exists:users,id',
            'receiver_id'  => 'required|exists:users,id',
            'message'      => 'required|string',
            'unseen_msgs'  => 'integer',
            'feedback'     => 'string|nullable',
            'is_sent'      => 'boolean',
            'is_delivered' => 'boolean',
            'is_seen'      => 'boolean',
        ]);

        //! Need to check count 
        $count = User::where('id', $request->sender_id)->whereHas('sendMessages')->first();
        $data = [];

        $message = Message::create($validatedData);

        $msg = [
            'message' => $message->message,
            'time' => $message->created_at->format('D M d Y H:i:s \G\M\T+0000 (O)'), // Adjust format as needed,
            'senderId' => $message->receiver_id,
            'feedback' => [
                'isSent' => $message->is_sent,
                'isDelivered' => $message->is_delivered,
                'isSeen' => $message->is_seen,
            ]
        ];

        $chat = [
            'id' => $request->sender_id,
            'userId' =>  $request->sender_id, // Assuming the senderId is the authenticated user
            'unseenMsgs' => 0,
            'messages' => [$msg]
        ];

        //! Need to added Chat
        if (!$count) {
            $data['chat'] = $chat;
        }
        $data['msg'] = $msg;
       
        broadcast(new NewMessage($message))->toOthers();
        // broadcast(new NewMessage($msg))->toOthers();

        return ok('Message create successfully.',$data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $message = Message::with('sender:id,first_name,last_name,email,profile_image', 'receiver:id,first_name,last_name,email,profile_image')->findOrFail($id);

        return ok('Message get successfully.', $message);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $id)
    {
        $id->validate([
            'id'            => 'required|exists:messages,id',
            'sender_id'     => 'required',
            'receiver_id'   => 'required',
            'message'       => 'required',
            'is_attachment' => 'required|in:0,1',
        ]);

        $message = Message::findOrFail($id->id);

        $message->update($id->all());

        return ok('Message update successfully.', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $message = Message::findOrFail($id);

        $message->delete();

        return ok('Message delete successfully.');
    }

    // chats 
    public function chats(Request $request)
    {
        // Validate request
        $this->ListingValidation();

        // Base query for users
        $query = User::query()
            ->select('id', 'first_name', 'last_name',  'email', 'role', 'is_active', 'created_at', 'profile_image'); // Adjusted field name
        // Eager load messages
        $query->whereNot('id',auth()->user()->id);
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        }

        // Apply filter, sort, and pagination
        $users = $this->filterSortPagination($query);

        // Format user data
        $filterData = $users['query']->get()->map(function ($user) {
            return [
                'id'       => $user->id,
                'fullName' => $user->fullName,
                'role'     => $user->role,
                'about'    => $user->email,
                'avatar'   => $user->image_url,
                'status'   => $user->is_active ? 'online' : 'offline',
            ];
        });

        // Authentication user data
        $profileUser = [
            'id'        => auth()->user()->id,
            'avatar'    => auth()->user()->image_url,
            'fullName'  => auth()->user()->fullName ?? auth()->user()->first_name,
            'role'      => auth()->user()->role,
            'about'     => auth()->user()->email,
            'status'    => auth()->user()->is_active ? 'online' : 'offline',
            // 'settings' => [
            //     'isTwoStepAuthVerificationEnabled' => true,
            //     'isNotificationsOn'                => false,
            // ],
        ];

        $chats = $users['query']
            ->whereHas('sendMessages', function ($qry) {
                $qry->where('receiver_id', auth()->user()->id);
            })
            ->get()
            ->map(function ($user) {
                // Fetch only the last message for each user
                $lastMessage = Message::where(function ($query) use ($user) {
                    $query->where('receiver_id', $user->id)
                        ->where('sender_id', auth()->user()->id);
                })
                    ->orWhere(function ($query) use ($user) {
                        $query->where('receiver_id', auth()->user()->id)
                            ->where('sender_id', $user->id);
                    })
                    ->latest('created_at')
                    ->first();

                return [
                    'id' => $user->id,
                    'role' => $user->role,
                    'fullName' => $user->fullName,
                    'about' => $user->email,
                    'chat' => [
                        'id' => $user->id,
                        'userId' => $user->id,
                        'unseenMsgs' => $user->receiveMessages->where('is_seen', false)->count(),
                        'lastMessage' => $lastMessage ? [
                            'message' => $lastMessage->message,
                            'time' => $lastMessage->created_at->format('D M d Y H:i:s \G\M\T+0000 (O)'), // Adjust format as needed
                            'senderId' => $lastMessage->sender_id,
                            'feedback' => [
                                'isSent' => $lastMessage->is_sent,
                                'isDelivered' => $lastMessage->is_delivered,
                                'isSeen' => $lastMessage->is_seen,
                            ],
                        ] : [], // Return an empty array if no messages
                    ],
                ];
            });


        // Determine message type
        $msg = $request->type == 'chat' ? 'Chat' : 'Contact';

        return ok($msg . ' fetch successfully', [
            'contacts'      => $filterData,
            'profileUser'   => $profileUser,
            'chats'         => $chats,
            'count'         => $users['count'],
        ]);
    }

    /**
     * Message Seen 
     */
    public function seenMessage($id)
    {

        $updated = Message::where('is_sent', true)
            ->where(function ($query) use ($id) {
                $query->where('receiver_id', $id)->where('sender_id', auth()->user()->id);
            })
            ->orWhere(function ($query) use ($id) {
                $query->where('receiver_id', auth()->user()->id)->where('sender_id', $id);
            })->update(['is_seen' => true, 'is_delivered' => true]);

        if ($updated > 0) {
            $msg = "message seen successfully";
        } else {
            $msg = "No messages found to update.";
        }

        return ok($msg);
    }
}
