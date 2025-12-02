<?php
/**
 * Auto Translate Multi-Language Tool
 *
 * Tự động dịch các file translation từ EN sang các ngôn ngữ khác
 * Sử dụng Google Translate API (free endpoint)
 *
 * Usage:
 *   php task-cli/auto-translate-multi-language.php
 *   php task-cli/auto-translate-multi-language.php --dry-run
 *   php task-cli/auto-translate-multi-language.php --file=monitor.php
 *   php task-cli/auto-translate-multi-language.php --lang=ja,ko
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

require_once __DIR__ . '/../app/common.php';

class AutoTranslateMultiLanguage
{
    private $langPath;
    private $dryRun = false;
    private $targetFile = null;
    private $targetLangs = null;
    private $force = false;
    private $stats = [
        'files_processed' => 0,
        'keys_translated' => 0,
        'keys_skipped' => 0,
        'keys_forced' => 0,
        'errors' => 0,
    ];

    public function __construct($options = [])
    {
        $this->langPath = base_path('resources/lang');
        $this->dryRun = $options['dry-run'] ?? false;
        $this->targetFile = $options['file'] ?? null;
        $this->targetLangs = $options['lang'] ?? null;
        $this->force = $options['force'] ?? false;
    }

    public function run()
    {
        echo "=================================================\n";
        echo "   Auto Translate Multi-Language Tool\n";
        echo "=================================================\n\n";

        if ($this->dryRun) {
            echo "⚠️  DRY RUN MODE - No files will be modified\n\n";
        }

        if ($this->force) {
            echo "🔥 FORCE MODE - Will re-translate existing translations\n\n";
        }

        // Lấy danh sách ngôn ngữ từ clang1
        $allLanguages = clang1::getLanguageListKey();
        echo "📋 Supported languages from clang1: " . implode(', ', $allLanguages) . "\n\n";

        // Lọc ngôn ngữ nếu có --lang
        if ($this->targetLangs) {
            $targetLangList = array_map('trim', explode(',', $this->targetLangs));
            $allLanguages = array_intersect($allLanguages, $targetLangList);
            echo "🎯 Filtered languages: " . implode(', ', $allLanguages) . "\n\n";
        }

        // Đường dẫn EN (source)
        $enPath = $this->langPath . '/en';

        if (!is_dir($enPath)) {
            echo "❌ Error: EN language folder not found at: $enPath\n";
            return;
        }

        // Lấy danh sách file trong EN
        $enFiles = $this->getPhpFiles($enPath);

        if (empty($enFiles)) {
            echo "❌ Error: No PHP files found in EN folder\n";
            return;
        }

        echo "📁 Found " . count($enFiles) . " file(s) in EN folder:\n";
        foreach ($enFiles as $file) {
            echo "   - $file\n";
        }
        echo "\n";

        // Lọc file nếu có --file
        if ($this->targetFile) {
            $enFiles = array_filter($enFiles, function($file) {
                return basename($file) === $this->targetFile;
            });

            if (empty($enFiles)) {
                echo "❌ Error: File '$this->targetFile' not found in EN folder\n";
                return;
            }

            echo "🎯 Filtered to file: $this->targetFile\n\n";
        }

        // Dịch từng file
        foreach ($enFiles as $enFile) {
            $fileName = basename($enFile);
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "📄 Processing: $fileName\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

            $enData = include $enFile;

            if (!is_array($enData)) {
                echo "⚠️  Skipped: Not a valid translation array\n\n";
                continue;
            }

            $this->stats['files_processed']++;

            // Dịch sang từng ngôn ngữ
            foreach ($allLanguages as $lang) {
                if ($lang === 'en') {
                    continue; // Skip EN itself
                }

                echo "  🌍 Translating to: $lang ... ";

                $targetLangPath = $this->langPath . '/' . $lang;
                $targetFile = $targetLangPath . '/' . $fileName;

                // Tạo folder nếu chưa có
                if (!is_dir($targetLangPath)) {
                    if (!$this->dryRun) {
                        mkdir($targetLangPath, 0755, true);
                    }
                    echo "\n     📁 Created folder: $targetLangPath\n";
                }

                // Load existing translations
                $existingData = [];
                if (file_exists($targetFile)) {
                    $existingData = include $targetFile;
                    if (!is_array($existingData)) {
                        $existingData = [];
                    }
                }

                // Merge translations
                $translatedData = $this->translateArray($enData, $existingData, $lang);

                // Save file
                if (!$this->dryRun) {
                    $this->saveTranslationFile($targetFile, $translatedData);
                }

                echo "✅\n";
            }

            echo "\n";
        }

        // Print statistics
        $this->printStats();
    }

    /**
     * Dịch array đệ quy, giữ nguyên value đã có
     */
    private function translateArray($sourceArray, $existingArray, $targetLang, $path = '')
    {
        $result = [];

        foreach ($sourceArray as $key => $value) {
            $currentPath = $path ? "$path.$key" : $key;

            // Nếu là array, đệ quy
            if (is_array($value)) {
                $existingSubArray = $existingArray[$key] ?? [];
                $result[$key] = $this->translateArray(
                    $value,
                    $existingSubArray,
                    $targetLang,
                    $currentPath
                );
            }
            // Nếu là string
            else if (is_string($value)) {
                // Nếu đã có translation và không force, giữ nguyên
                if (isset($existingArray[$key]) && !empty($existingArray[$key]) && !$this->force) {
                    $result[$key] = $existingArray[$key];
                    $this->stats['keys_skipped']++;
                }
                // Nếu chưa có hoặc force, dịch mới
                else {
                    $translated = $this->translateText($value, $targetLang);

                    if ($translated !== false) {
                        $result[$key] = $translated;

                        if ($this->force && isset($existingArray[$key])) {
                            $this->stats['keys_forced']++;
                        } else {
                            $this->stats['keys_translated']++;
                        }

                        echo ".";
                    } else {
                        // Nếu dịch lỗi, giữ nguyên EN hoặc giá trị cũ
                        $result[$key] = $existingArray[$key] ?? $value;
                        $this->stats['errors']++;
                        echo "!";
                    }

                    // Delay để tránh rate limit
                    usleep(200000); // 200ms
                }
            }
            // Các kiểu khác (number, bool...) giữ nguyên
            else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Dịch text bằng Google Translate API (free endpoint)
     * Bảo vệ placeholders Laravel như :count, :attribute, :min, :max
     */
    private function translateText($text, $targetLang)
    {
        // Bỏ qua các placeholder Laravel
        if (empty(trim($text))) {
            return $text;
        }

        // Nếu có newline character (thật), chia string và dịch từng phần
        if (strpos($text, "\n") !== false) {
            $parts = explode("\n", $text);
            $translatedParts = [];

            foreach ($parts as $part) {
                if (empty(trim($part))) {
                    $translatedParts[] = $part;
                } else {
                    $translated = $this->translateSingleText($part, $targetLang);
                    $translatedParts[] = $translated !== false ? $translated : $part;

                    // Delay sau mỗi phần để tránh rate limit
                    usleep(150000); // 150ms
                }
            }

            return implode("\n", $translatedParts);
        }

        // Nếu không có newline, dịch bình thường
        return $this->translateSingleText($text, $targetLang);
    }

    /**
     * Dịch một đoạn text đơn (không có \n)
     */
    private function translateSingleText($text, $targetLang)
    {
        if (empty(trim($text))) {
            return $text;
        }

        // Tách và lưu các placeholders (format :word KHÔNG có space sau :)
        // Ví dụ: ":count monitors" -> placeholder ":count"
        // Ví dụ: "[:desc]" -> placeholder "[:desc]"
        // Nhưng "example: value" -> KHÔNG phải placeholder (có space sau :)
        $placeholders = [];
        $placeholderIndex = 0;

        // Pattern 1: Bảo vệ [:word] format
        $textWithPlaceholders = preg_replace_callback(
            '/\[:([a-zA-Z_][a-zA-Z0-9_]*)\]/u',
            function($matches) use (&$placeholders, &$placeholderIndex) {
                $placeholder = $matches[0]; // [:desc], [:attribute], etc.
                $token = "___PLACEHOLDER_{$placeholderIndex}___";
                $placeholders[$token] = $placeholder;
                $placeholderIndex++;
                return $token;
            },
            $text
        );

        // Pattern 2: Bảo vệ :word format (không có space trước :)
        $textWithPlaceholders = preg_replace_callback(
            '/(?<!\S):([a-zA-Z_][a-zA-Z0-9_]*)/u',
            function($matches) use (&$placeholders, &$placeholderIndex) {
                $placeholder = $matches[0]; // :count, :attribute, etc.
                $token = "___PLACEHOLDER_{$placeholderIndex}___";
                $placeholders[$token] = $placeholder;
                $placeholderIndex++;
                return $token;
            },
            $textWithPlaceholders
        );

        // Mapping ngôn ngữ Laravel -> Google Translate code
        $langMap = [
            'vi' => 'vi',
            'en' => 'en',
            'ja' => 'ja',
            'ko' => 'ko',
            'fr' => 'fr',
            'de' => 'de',
            'es' => 'es',
            'km' => 'km', // Khmer
            'ru' => 'ru',
            'zh' => 'zh-CN', // Chinese Simplified
        ];

        $googleLang = $langMap[$targetLang] ?? $targetLang;

        $url = "https://translate.googleapis.com/translate_a/single?" . http_build_query([
            'client' => 'gtx',
            'sl' => 'en',
            'tl' => $googleLang,
            'dt' => 't',
            'q' => $textWithPlaceholders,
        ]);

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || !$response) {
                return false;
            }

            $data = json_decode($response, true);

            // Google Translate API trả về array của các đoạn dịch
            // $data[0] = [[translated_text, original_text], [translated_text2, original_text2], ...]
            // Ghép tất cả các phần lại
            if (isset($data[0]) && is_array($data[0])) {
                $translatedParts = [];

                foreach ($data[0] as $part) {
                    if (isset($part[0])) {
                        $translatedParts[] = $part[0];
                    }
                }

                $translated = implode('', $translatedParts);

                // Khôi phục placeholders
                foreach ($placeholders as $token => $placeholder) {
                    $translated = str_replace($token, $placeholder, $translated);
                }

                return $translated;
            }

            return false;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Lưu file translation với format đẹp
     */
    private function saveTranslationFile($filePath, $data)
    {
        $content = "<?php\n\nreturn " . $this->varExportPretty($data) . ";\n";
        file_put_contents($filePath, $content);
    }

    /**
     * var_export with pretty format
     */
    private function varExportPretty($data, $indent = 0)
    {
        if (!is_array($data)) {
            return var_export($data, true);
        }

        $output = "[\n";
        $indentStr = str_repeat('    ', $indent + 1);

        foreach ($data as $key => $value) {
            $output .= $indentStr;
            $output .= var_export($key, true) . ' => ';

            if (is_array($value)) {
                $output .= $this->varExportPretty($value, $indent + 1);
            } else {
                $output .= var_export($value, true);
            }

            $output .= ",\n";
        }

        $output .= str_repeat('    ', $indent) . ']';

        return $output;
    }

    /**
     * Lấy danh sách file PHP trong folder
     */
    private function getPhpFiles($dir)
    {
        $files = [];

        if (!is_dir($dir)) {
            return $files;
        }

        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;

            if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $files[] = $path;
            }
        }

        return $files;
    }

    /**
     * In thống kê
     */
    private function printStats()
    {
        echo "=================================================\n";
        echo "   📊 TRANSLATION STATISTICS\n";
        echo "=================================================\n\n";

        echo "✅ Files processed:    {$this->stats['files_processed']}\n";
        echo "🌍 Keys translated:    {$this->stats['keys_translated']}\n";
        echo "⏭️  Keys skipped:       {$this->stats['keys_skipped']}\n";

        if ($this->force && $this->stats['keys_forced'] > 0) {
            echo "🔥 Keys forced:        {$this->stats['keys_forced']}\n";
        }

        echo "❌ Errors:             {$this->stats['errors']}\n\n";

        if ($this->dryRun) {
            echo "⚠️  DRY RUN - No files were actually modified\n\n";
        } else {
            echo "✅ Translation completed!\n\n";
        }
    }
}

echo "Usage: php auto-translate-multi-language.php [options]\n\n";
echo "Options:\n";
echo "  --dry-run         Show what would be translated without making changes\n";
echo "  --force           Re-translate existing translations (overwrite)\n";
echo "  --file=FILE       Only translate specific file (e.g., monitor.php)\n";
echo "  --lang=LANGS      Only translate to specific languages (comma-separated, e.g., ja,ko)\n";
echo "  --help, -h        Show this help message\n\n";
echo "Examples:\n";
echo "  php auto-translate-multi-language.php\n";
echo "  php auto-translate-multi-language.php --dry-run\n";
echo "  php auto-translate-multi-language.php --force\n";
echo "  php auto-translate-multi-language.php --file=monitor.php\n";
echo "  php auto-translate-multi-language.php --lang=ja,ko\n";
echo "  php auto-translate-multi-language.php --file=monitor.php --lang=ja --force\n\n";
echo "Placeholder Protection:\n";
echo "  - Protects Laravel placeholders like :count, :attribute, :min, :max\n";
echo "  - Rule: :word (no space after colon) = placeholder (protected)\n";
echo "  - Rule: : value (space after colon) = normal text (translated)\n";
echo "  - Example: ':count monitors' -> ':count モニター' (correct)\n";
echo "  - Example: 'Format: yyyy-mm-dd' -> 'フォーマット: yyyy-mm-dd' (correct)\n\n";
getch("...");

// Parse command line arguments
$options = [];
for ($i = 1; $i < $argc; $i++) {
    if ($argv[$i] === '--dry-run') {
        $options['dry-run'] = true;
    } elseif ($argv[$i] === '--force') {
        $options['force'] = true;
    } elseif (strpos($argv[$i], '--file=') === 0) {
        $options['file'] = substr($argv[$i], 7);
    } elseif (strpos($argv[$i], '--lang=') === 0) {
        $options['lang'] = substr($argv[$i], 7);
    } elseif ($argv[$i] === '--help' || $argv[$i] === '-h') {

    }
}

// Run
$translator = new AutoTranslateMultiLanguage($options);
$translator->run();
