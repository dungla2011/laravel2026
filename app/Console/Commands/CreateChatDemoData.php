<?php

namespace App\Console\Commands;

use App\Models\CrmMessage;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateChatDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:create-demo-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo demo data cho hệ thống chat';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Đang tạo demo data cho hệ thống chat...');

        // Tạo demo users
        $users = $this->createDemoUsers();
        
        // Tạo demo conversations
        $this->createDemoConversations($users);

        $this->info('✅ Đã tạo xong demo data!');
        $this->info('');
        $this->info('Demo users đã tạo:');
        foreach ($users as $user) {
            $this->line("- {$user->name} ({$user->email}) - Password: demo123");
        }
        $this->info('');
        $this->info('Bạn có thể đăng nhập bằng bất kỳ tài khoản nào ở trên và truy cập /chat để test.');

        return 0;
    }

    private function createDemoUsers()
    {
        $demoUsers = [
            [
                'name' => 'Nguyễn Văn An',
                'email' => 'an@demo.com',
                'username' => 'nguyenvanan'
            ],
            [
                'name' => 'Trần Thị Bình',
                'email' => 'binh@demo.com', 
                'username' => 'tranthibinh'
            ],
            [
                'name' => 'Lê Văn Cường',
                'email' => 'cuong@demo.com',
                'username' => 'levancuong'
            ],
            [
                'name' => 'Phạm Thị Dung',
                'email' => 'dung@demo.com',
                'username' => 'phamthidung'
            ],
            [
                'name' => 'Hoàng Văn Em',
                'email' => 'em@demo.com',
                'username' => 'hoangvanem'
            ]
        ];

        $users = [];
        
        foreach ($demoUsers as $userData) {
            // Kiểm tra user đã tồn tại chưa
            $existingUser = User::where('email', $userData['email'])->first();
            
            if ($existingUser) {
                $this->line("User {$userData['email']} đã tồn tại, bỏ qua...");
                $users[] = $existingUser;
                continue;
            }

            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'username' => $userData['username'],
                'password' => Hash::make('demo123'),
                'email_verified_at' => now(),
            ]);

            $users[] = $user;
            $this->line("✓ Đã tạo user: {$user->name}");
        }

        return $users;
    }

    private function createDemoConversations($users)
    {
        if (count($users) < 2) {
            $this->warn('Cần ít nhất 2 users để tạo conversations');
            return;
        }

        $conversations = [
            [
                'user1' => $users[0],
                'user2' => $users[1],
                'messages' => [
                    ['from' => 0, 'content' => 'Chào bạn! Bạn có khỏe không?'],
                    ['from' => 1, 'content' => 'Chào! Mình khỏe, cảm ơn bạn. Còn bạn thì sao?'],
                    ['from' => 0, 'content' => 'Mình cũng ổn. Hôm nay bạn có rảnh không?'],
                    ['from' => 1, 'content' => 'Có đấy, bạn muốn đi đâu không?'],
                    ['from' => 0, 'content' => 'Mình nghĩ chúng ta có thể đi cafe và nói chuyện về dự án mới'],
                ]
            ],
            [
                'user1' => $users[0],
                'user2' => $users[2],
                'messages' => [
                    ['from' => 2, 'content' => 'Anh ơi, em có thể hỏi về task hôm qua không?'],
                    ['from' => 0, 'content' => 'Được chứ, em cứ hỏi đi'],
                    ['from' => 2, 'content' => 'Em đang gặp khó khăn với phần database'],
                    ['from' => 0, 'content' => 'Anh sẽ hỗ trợ em. Chúng ta hẹn meeting lúc 2h chiều nhé'],
                ]
            ],
            [
                'user1' => $users[1],
                'user2' => $users[3],
                'messages' => [
                    ['from' => 1, 'content' => 'Chị ơi, báo cáo tuần này em đã hoàn thành'],
                    ['from' => 3, 'content' => 'Tốt lắm! Em gửi file cho chị xem nhé'],
                    ['from' => 1, 'content' => 'Dạ, em sẽ gửi ngay'],
                ]
            ]
        ];

        foreach ($conversations as $conv) {
            $user1 = $conv['user1'];
            $user2 = $conv['user2'];
            
            // Tạo thread_id unique
            $threadId = 'chat_' . min($user1->id, $user2->id) . '_' . max($user1->id, $user2->id);
            
            // Kiểm tra conversation đã tồn tại chưa
            $existingConv = CrmMessage::where('thread_id', $threadId)->first();
            if ($existingConv) {
                $this->line("Conversation giữa {$user1->name} và {$user2->name} đã tồn tại, bỏ qua...");
                continue;
            }

            $this->line("Tạo conversation giữa {$user1->name} và {$user2->name}...");

            foreach ($conv['messages'] as $index => $msgData) {
                $fromUser = $msgData['from'] == 0 ? $user1 : ($msgData['from'] == 1 ? $user2 : $users[$msgData['from']]);
                $toUser = $fromUser->id == $user1->id ? $user2 : $user1;

                $message = new CrmMessage();
                $message->thread_id = $threadId;
                $message->content = $msgData['content'];
                $message->uid_from = $fromUser->id;
                $message->id_to = $toUser->id;
                $message->msg_type = 'text';
                $message->status = 'sent';
                $message->ts = time() - (count($conv['messages']) - $index) * 300; // 5 phút giữa mỗi tin nhắn
                $message->created_at = now()->subMinutes((count($conv['messages']) - $index) * 5);
                $message->save();
            }

            $this->line("✓ Đã tạo " . count($conv['messages']) . " tin nhắn");
        }
    }
} 