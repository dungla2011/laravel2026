<?php

namespace App\Http\Controllers;

use App\Models\CrmMessage;
use App\Models\CrmMessageGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Hiển thị giao diện chat chính
     */
    public function index()
    {

        if(!isAdminACP_()){
            die("Bạn không có quyền truy cập chức năng này");
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $channel_name = \request('channel_name');
        $phoneParam = \request('user'); // Lấy parameter &user=phone từ URL

        // Nếu có parameter phone, xử lý tìm/tạo user Zalo
        $zaloUserData = null;
        if ($phoneParam) {
            $zaloUserData = $this->handleZaloUserByPhone($phoneParam, $channel_name);
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($zaloUserData);
            echo "</pre>";
            die("PHONE.. $phoneParam ");

        }

        // Lấy danh sách conversations (threads) của user
        $conversations = $this->getUserConversations($user->id, $channel_name);

        return view('chat.index', compact('conversations', 'user', 'zaloUserData'));
    }

    /**
     * Xử lý tìm/tạo user Zalo theo phone
     */
    private function handleZaloUserByPhone($phone, $channel = 'event1')
    {
        try {
            // 1. Tìm trong bảng zalo_users
            $zaloUser = \App\Models\ZaloUser::where('phone', $phone)->first();

            // 2. Nếu chưa có, tạo record mới
            if (!$zaloUser) {
                $zaloUser = \App\Models\ZaloUser::create([
                    'phone' => $phone,
                    'name' => 'Zalo User ' . $phone,
                    'status' => 0
                ]);
            }

            // 3. Gọi API Zalo để lấy thông tin user
            $helper = new \ZaloHelper(
                'http://localhost:30000',
                env('ZALO_API_USER', 'admin'),
                env('ZALO_API_PASSWORD', '938475wufo87908u09')
            );

            $result = $helper->findUser($channel, $phone);

            // 4. Nếu lấy được thông tin từ Zalo, update bảng
            if ($result['success'] ?? false) {
                $zaloInfo = $result['user'] ?? [];
                $zaloUser->update([
                    'name' => $zaloInfo['displayName'] ?? $zaloUser->name,
                    'zalo_id' => $zaloInfo['uid'] ?? null,
                    'status' => 1,
                    'log' => json_encode($zaloInfo)
                ]);

                return [
                    'success' => true,
                    'zalo_user' => $zaloUser,
                    'zalo_info' => $zaloInfo,
                    'uid' => $zaloInfo['uid'] ?? null,
                    'name' => $zaloInfo['displayName'] ?? $zaloUser->name,
                    'phone' => $phone
                ];
            } else {
                return [
                    'success' => false,
                    'zalo_user' => $zaloUser,
                    'error' => $result['error'] ?? 'Không thể lấy thông tin Zalo',
                    'phone' => $phone
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'phone' => $phone
            ];
        }
    }

    /**
     * Lấy danh sách conversations của user
     */
    private function getUserConversations($userId, $channel_name = null)
    {
        // Lấy tin nhắn cuối cùng của mỗi thread dựa trên thời gian
        $latestMessages = CrmMessage::select('thread_id', 'content', 'created_at', 'd_name','ts', 'uid_from', 'id_to', 'type' , 'is_self')
            ->whereIn('id', function($query) {
                $query->select(\DB::raw('MAX(id)'))
                    ->from('crm_messages')
                    ->whereNotNull('thread_id')
                    ->groupBy('thread_id');
            })->where("channel_name", $channel_name ?? 'default')
            ->orderBy('ts', 'desc')
            ->limit(500)
            ->get()
            ->map(function($conversation) use ($userId) {
                // Đếm tổng số tin nhắn trong thread
                $messageCount = CrmMessage::where('thread_id', $conversation->thread_id)->count();

                $conversation->last_message_time =  nowyh(round($conversation->ts/1000));
//                $conversation->last_message_time =  nowyh(round($conversation->ts/1000));
                $conversation->last_message = $conversation->content;
                $conversation->message_count = $messageCount;

                // Xử lý tên hiển thị dựa trên type
                if ($conversation->type == 1) {
                    // Group chat - giữ nguyên d_name
                    $conversation->d_name_other = $conversation->d_name;

                    //conversation->thread_id
                    //Lấy ra thread info từ CrmMessageGroup
                    if($threadInfo = CrmMessageGroup::where('gid', $conversation->thread_id)->first())
                        $conversation->g_name = $threadInfo->name;




                } else {
                    // Chat 2 người - tìm tên của người còn lại (is_self = 0)
                    $otherPersonMessage = CrmMessage::where('thread_id', $conversation->thread_id)
                        ->where('is_self', 0)
                        ->whereNotNull('d_name')
                        ->first();

                    if ($otherPersonMessage) {
                        $conversation->d_name_other = $otherPersonMessage->d_name;
                    } else {
                        // Fallback nếu không tìm thấy
                        $conversation->d_name_other = $conversation->d_name;
                    }
                }

                if(0)
                if($tmp = json_decode($conversation->content)){
                    if($tmp->thumb ?? ''){
                        $conversation->content = '<img src="'.$tmp->thumb.'" style="max-width: 200px; max-height: 200px;"/>';
                    } else {
//                        $conversation->content = $tmp->data->text ?? '';
                    }
                }
//                $conversation = [];
                return $conversation;
            });

        return $latestMessages;
    }

    /**
     * Lấy tin nhắn của một conversation
     */
    public function getMessages(Request $request)
    {
        $threadId = $request->get('thread_id');
        $page = $request->get('page', 1);
        $limit = 100;

        if (!$threadId) {
            return response()->json(['error' => 'Thread ID is required'], 400);
        }

        $messages = CrmMessage::where('thread_id', $threadId)
            ->orderBy('ts', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get()
            ->reverse()
            ->values()
            ->map(function($message) {
                $sender = User::find($message->uid_from);
                $message->sender_name = $sender ? $sender->getNameTitle() : 'Unknown';
                $message->sender_avatar = $sender ? ($sender->avatar ?? '/tpl_modernize/assets/images/svgs/icon-user-male.svg') : '/tpl_modernize/assets/images/svgs/icon-user-male.svg';
                $time = $message->ts ? date('H:i:s d/m', $message->ts/1000) . " - $message->ts " : null;
                $date = $message->ts ? date('d/m/Y', $message->ts/1000) : null;
//                $message->formatted_time = $message->ts ? $message->ts->format('H:i d/m') : '';
//                $message->formatted_date = $message->ts ? $message->ts->format('d/m') : '';
                $message->formatted_time = $time;
                $message->formatted_date = $date;

                if($tmp = json_decode($message->content)){
                    if($tmp->thumb ?? '') {
                        //Làm sao onclick thì mo anh sang trang moi:
                        $title = $tmp->title ?? 'no_title';
                        $message->content = '<a href="' . $tmp->thumb . '" target="_blank"><img src="' . $tmp->thumb . '" style="max-width: 600px; "/></a> <p> '.$title.' </p>';
//                        $message->content = '<img src="'.$tmp->thumb.'" style="max-width: 600px; "/>';
                    }
                    if(str_contains($message->content, 'isCaller') && str_contains($message->content, 'duration') ){
                        $message->content = " *** Cuộc gọi: " . $tmp->params;
                    }
                }

                return $message;
            });

        return response()->json([
            'messages' => $messages,
            'has_more' => $messages->count() == $limit
        ]);
    }

    /**
     * Gửi tin nhắn mới + gửi tới Zalo nếu callback provided
     */
    /**
     * Gửi tin nhắn tới Zalo
     * Callback $saveDbCallback quyết định có ghi DB hay không
     */
    public function sendMessage(Request $request, $saveDbCallback = null)
    {
        $request->validate([
            'uid' => 'required|string',
            'message' => 'required|string|max:5000',
            'channel_name' => 'nullable|string'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $uid = $request->get('uid');
        $messageContent = $request->get('message');
        $channel = $request->get('channel_name', 'event1');

        // Gửi tới Zalo luôn hoạt động
        $zaloResult = $this->sendToZalo($uid, $messageContent, $channel);

        $response = [
            'success' => $zaloResult['success'],
            'zalo' => $zaloResult
        ];

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($request->toArray());
//        echo "</pre>";
//        echo "<br/>\n $uid , $messageContent";
//        die();

        // Gọi callback để ghi DB nếu được cấp
        if(0)
        if (is_callable($saveDbCallback)) {
            $dbResult = call_user_func($saveDbCallback, [
                'thread_id' => $request->get('thread_id'),
                'content' => $messageContent,
                'to_user_id' => $request->get('to_user_id'),
                // 'user_id' => $request->get('user_id'),
            ]);
            $response['database'] = $dbResult;
        }

        return response()->json($response);
    }

    /**
     * Callback độc lập: gửi tin nhắn tới Zalo
     * Luôn hoạt động, không phụ thuộc vào DB
     */
    private function sendToZalo($uid, $message, $channel = 'event1')
    {
        try {
            $helper = new \ZaloHelper(
                'http://localhost:30000',
                env('ZALO_API_USER', 'admin'),
                env('ZALO_API_PASSWORD', '938475wufo87908u09')
            );

            $result = $helper->sendMessage($channel, $uid, $message);

            if ($result['success'] ?? false) {
                return [
                    'success' => true,
                    'msgId' => $result['data']['msgId'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['error'] ?? 'Lỗi gửi tin Zalo'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Callback: ghi tin nhắn vào database
     */
    public function saveDbCallback($data)
    {
        try {
            $message = new CrmMessage();
            $message->thread_id = $data['thread_id'];
            $message->thread_id = str_replace('zalo_', '', $message->thread_id);
            $message->content = $data['content'];
            $message->uid_from = $data['uid_from'] ?? '';
            $message->id_to = $data['to_user_id']  ?? '';
            $message->msg_type = 'text';
            $message->user_id = getCurrentUserId();
            $message->status = 1; // 1 = sent (số, không phải string)
            $message->ts = time() * 1000; // milliseconds để match với Zalo format
            $message->save();

            // Format response
            $message->sender_name = Auth::user()->getNameTitle();
            $message->sender_avatar = Auth::user()->avatar ?? '/tpl_modernize/assets/images/svgs/icon-user-male.svg';
            $message->formatted_time = $message->created_at->format('H:i d/m');
            $message->formatted_date = $message->created_at->format('d/m');

            return [
                'success' => true,
                'message' => $message
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Tìm kiếm users để bắt đầu chat mới
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['users' => []]);
        }

        $users = User::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%");
            })
            ->where('id', '!=', Auth::id())
            ->select('id', 'name', 'email', 'username', 'avatar')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->getNameTitle(),
                    'avatar' => $user->avatar ?? '/tpl_modernize/assets/images/svgs/icon-user-male.svg',
                    'email' => $user->email
                ];
            });

        return response()->json(['users' => $users]);
    }

    /**
     * Bắt đầu conversation mới
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $currentUser = Auth::user();
        $targetUserId = $request->user_id;

        // Tạo thread_id unique
        $threadId = 'chat_' . min($currentUser->id, $targetUserId) . '_' . max($currentUser->id, $targetUserId);

        // Kiểm tra xem conversation đã tồn tại chưa
        $existingMessage = CrmMessage::where('thread_id', $threadId)->first();

        if (!$existingMessage) {
            // Tạo tin nhắn đầu tiên (system message)
            $message = new CrmMessage();
            $message->thread_id = $threadId;
            $message->content = 'Cuộc trò chuyện đã bắt đầu';
            $message->uid_from = $currentUser->id;
            $message->id_to = $targetUserId;
            $message->msg_type = 'system';
            $message->status = 'sent';
            $message->ts = time();
            $message->save();
        }

        return response()->json([
            'success' => true,
            'thread_id' => $threadId,
            'redirect_url' => route('chat.conversation', ['thread_id' => $threadId])
        ]);
    }

    /**
     * Hiển thị conversation cụ thể
     */
    public function showConversation($threadId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Kiểm tra user có quyền truy cập conversation này không
        $hasAccess = CrmMessage::where('thread_id', $threadId)
            ->where(function($query) use ($user) {
                $query->where('uid_from', $user->id)
                      ->orWhere('id_to', $user->id);
            })
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Bạn không có quyền truy cập cuộc trò chuyện này');
        }

        // Lấy thông tin partner
        $partnerMessage = CrmMessage::where('thread_id', $threadId)
            ->where(function($query) use ($user) {
                $query->where('uid_from', '!=', $user->id)
                      ->orWhere('id_to', '!=', $user->id);
            })
            ->first();

        $partnerId = null;
        $partner = null;

        if ($partnerMessage) {
            $partnerId = $partnerMessage->uid_from == $user->id ? $partnerMessage->id_to : $partnerMessage->uid_from;
            $partner = User::find($partnerId);
        }

        $conversations = $this->getUserConversations($user->id);

        return view('chat.conversation', compact('threadId', 'partner', 'conversations', 'user'));
    }

    /**
     * Đánh dấu tin nhắn đã đọc
     */
    public function markAsRead(Request $request)
    {
        $threadId = $request->get('thread_id');
        $userId = Auth::id();

        CrmMessage::where('thread_id', $threadId)
            ->where('id_to', $userId)
            ->where('status', '!=', 'read')
            ->update(['status' => 'read']);

        return response()->json(['success' => true]);
    }

    /**
     * Upload file/image cho chat
     */
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'thread_id' => 'required|string',
            'to_user_id' => 'required|integer'
        ]);

        $user = Auth::user();
        $file = $request->file('file');

        // Lưu file
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('chat_files', $fileName, 'public');

        // Tạo message với file
        $message = new CrmMessage();
        $message->thread_id = $request->thread_id;
        $message->content = $file->getClientOriginalName();
        $message->uid_from = $user->id;
        $message->id_to = $request->to_user_id;
        $message->msg_type = 'file';
        $message->status = 'sent';
        $message->ts = time();

        // Lưu thông tin file vào trường log dạng JSON
        $message->log = json_encode([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'file_type' => $file->getMimeType(),
            'file_url' => asset('storage/' . $filePath)
        ]);

        $message->save();

        // Format response
        $message->sender_name = $user->getNameTitle();
        $message->sender_avatar = $user->avatar ?? '/tpl_modernize/assets/images/svgs/icon-user-male.svg';
        $message->formatted_time = $message->created_at->format('H:i d/m');
        $message->formatted_date = $message->created_at->format('d/m/Y');
        $message->file_info = json_decode($message->log, true);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}

