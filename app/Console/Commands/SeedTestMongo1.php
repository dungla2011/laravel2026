<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TestMongo1;

class SeedTestMongo1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:testmongo1 {count=50 : Number of records to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed TestMongo1 collection with fake data for testing pagination and search';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        
        $this->info("Creating {$count} fake TestMongo1 records...");
        $this->info('========================================');

        // Sample Vietnamese names
        $vietnameseFirstNames = [
            'Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng',
            'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý', 'Đinh', 'Đào', 'Lương', 'Tô'
        ];
        
        $vietnameseLastNames = [
            'Văn Minh', 'Thị Lan', 'Văn Hùng', 'Thị Mai', 'Văn Đức', 'Thị Hoa', 'Văn Nam',
            'Thị Linh', 'Văn Tuấn', 'Thị Nga', 'Văn Long', 'Thị Thu', 'Văn Khang', 'Thị Trang',
            'Văn Phong', 'Thị Hương', 'Văn Quang', 'Thị Nhung', 'Văn Tài', 'Thị Yến'
        ];

        $domains = [
            'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 
            'fpt.edu.vn', 'vnu.edu.vn', 'hust.edu.vn', 'uit.edu.vn'
        ];

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $created = 0;
        $errors = 0;

        for ($i = 0; $i < $count; $i++) {
            try {
                // Generate Vietnamese name
                $firstName = $vietnameseFirstNames[array_rand($vietnameseFirstNames)];
                $lastName = $vietnameseLastNames[array_rand($vietnameseLastNames)];
                $fullName = $firstName . ' ' . $lastName;
                
                // Generate email
                $emailPrefix = $this->removeVietnameseAccents(strtolower(str_replace(' ', '', $lastName)));
                $emailPrefix = preg_replace('/[^a-z0-9]/', '', $emailPrefix);
                $email = $emailPrefix . rand(1, 999) . '@' . $domains[array_rand($domains)];
                
                // Check if email already exists
                $existingEmail = TestMongo1::where('email', $email)->first();
                if ($existingEmail) {
                    $email = $emailPrefix . rand(1000, 9999) . '@' . $domains[array_rand($domains)];
                }
                
                // Generate phone number (Vietnamese format)
                $phoneFormats = [
                    '09' . rand(10000000, 99999999),
                    '08' . rand(10000000, 99999999),
                    '07' . rand(10000000, 99999999),
                    '03' . rand(10000000, 99999999),
                ];
                
                $phone = $phoneFormats[array_rand($phoneFormats)];
                
                // Create record
                TestMongo1::create([
                    'name' => $fullName,
                    'email' => $email,
                    'phone' => $phone,
                    'created_at' => now()->subDays(rand(0, 365)),
                ]);
                
                $created++;
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error creating record: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Seeding completed!");
        $this->info("Created: {$created} records");
        if ($errors > 0) {
            $this->warn("Errors: {$errors} records failed");
        }
        
        // Show statistics
        try {
            $total = TestMongo1::count();
            $this->info("Total records in database: {$total}");
        } catch (\Exception $e) {
            $this->warn("Could not count total records: " . $e->getMessage());
        }
        
        $this->newLine();
        $this->info("🎯 Test your CRUD system:");
        $this->info("- Visit: /testmongo1");
        $this->info("- Try searching for names, emails, or phone numbers");
        $this->info("- Test pagination with different page sizes");
        $this->info("- Try sorting by different columns");
        
        return Command::SUCCESS;
    }
    
    /**
     * Remove Vietnamese accents from string
     */
    private function removeVietnameseAccents($str)
    {
        $accents = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ'
        ];
        
        $replacements = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd'
        ];
        
        return str_replace($accents, $replacements, $str);
    }
} 