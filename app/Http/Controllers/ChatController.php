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

        // Lấy danh sách conversations (threads) của user
        $conversations = $this->getUserConversations($user->id, $channel_name);

        return view('chat.index', compact('conversations', 'user'));
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
     * Gửi tin nhắn mới
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'thread_id' => 'required|string',
            'content' => 'required|string|max:5000',
            'to_user_id' => 'required|integer'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $message = new CrmMessage();
        $message->thread_id = $request->thread_id;
        $message->content = request('content');
        $message->uid_from = $user->id;
        $message->id_to = $request->to_user_id;
        $message->msg_type = 'text';
        $message->status = 'sent';
        $message->ts = time();
        $message->save();

        // Format response
        $message->sender_name = $user->getNameTitle();
        $message->sender_avatar = $user->avatar ?? '/tpl_modernize/assets/images/svgs/icon-user-male.svg';
        $message->formatted_time = $message->created_at->format('H:i d/m');
        $message->formatted_date = $message->created_at->format('d/m');

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
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
