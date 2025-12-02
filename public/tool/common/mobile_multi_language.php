<?php



error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

require '../../../vendor/autoload.php';
$app = require_once '../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

ob_end_clean();

if(!isSupperAdmin_()){
    die("NOT ADMINS");
}

// Xử lý upload file .arb
if(isset($_POST['action']) && $_POST['action'] == 'parse_arb'){
    header('Content-Type: application/json');

    try {
        if(!isset($_FILES['arb_file']) || $_FILES['arb_file']['error'] !== UPLOAD_ERR_OK){
            echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
            exit;
        }

        $file = $_FILES['arb_file'];
        $filename = $file['name'];

        // Check file extension
        if(!preg_match('/\.arb$/i', $filename)){
            echo json_encode(['success' => false, 'message' => 'File must be .arb format']);
            exit;
        }

        // Read and parse JSON
        $content = file_get_contents($file['tmp_name']);
        $data = json_decode($content, true);

        if(json_last_error() !== JSON_ERROR_NONE){
            echo json_encode(['success' => false, 'message' => 'Invalid JSON format: ' . json_last_error_msg()]);
            exit;
        }

        // Extract translation keys (skip @-prefixed metadata)
        $translations = [];
        foreach($data as $key => $value){
            // Skip metadata keys that start with @
            if(strpos($key, '@') === 0) continue;

            // Only get string values
            if(is_string($value)){
                $translations[$key] = $value;
            }
        }

        if(empty($translations)){
            echo json_encode(['success' => false, 'message' => 'No valid translation keys found in file']);
            exit;
        }

        // Check which keys already exist in 'en'
        $existingKeys = DB::table('translations')
            ->where('language_code', 'en')
            ->whereIn('translation_key', array_keys($translations))
            ->pluck('translation_key')
            ->toArray();

        $newKeys = [];
        $existingKeysData = [];

        foreach($translations as $key => $value){
            if(in_array($key, $existingKeys)){
                $existingKeysData[] = ['key' => $key, 'value' => $value];
            } else {
                $newKeys[] = ['key' => $key, 'value' => $value];
            }
        }

        echo json_encode([
            'success' => true,
            'total' => count($translations),
            'new_keys' => $newKeys,
            'existing_keys' => $existingKeysData,
            'new_count' => count($newKeys),
            'existing_count' => count($existingKeysData)
        ]);

    } catch(\Exception $e){
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    exit;
}

// Xử lý parse ARB content từ paste (tương tự parse_arb nhưng nhận JSON thay vì file)
if(isset($_POST['action']) && $_POST['action'] == 'parse_arb_paste'){
    header('Content-Type: application/json');

    try {
        if(!isset($_POST['json_content']) || empty($_POST['json_content'])){
            echo json_encode(['success' => false, 'message' => 'No JSON content provided']);
            exit;
        }

        // Parse JSON content
        $data = json_decode($_POST['json_content'], true);

        if(json_last_error() !== JSON_ERROR_NONE){
            echo json_encode(['success' => false, 'message' => 'Invalid JSON format: ' . json_last_error_msg()]);
            exit;
        }

        // Extract translation keys (skip @-prefixed metadata)
        $translations = [];
        foreach($data as $key => $value){
            // Skip metadata keys that start with @
            if(strpos($key, '@') === 0) continue;

            // Only get string values
            if(is_string($value)){
                $translations[$key] = $value;
            }
        }

        if(empty($translations)){
            echo json_encode(['success' => false, 'message' => 'No valid translation keys found in JSON']);
            exit;
        }

        // Check which keys already exist in 'en'
        $existingKeys = DB::table('translations')
            ->where('language_code', 'en')
            ->whereIn('translation_key', array_keys($translations))
            ->pluck('translation_key')
            ->toArray();

        $newKeys = [];
        $existingKeysData = [];

        foreach($translations as $key => $value){
            if(in_array($key, $existingKeys)){
                $existingKeysData[] = ['key' => $key, 'value' => $value];
            } else {
                $newKeys[] = ['key' => $key, 'value' => $value];
            }
        }

        echo json_encode([
            'success' => true,
            'total' => count($translations),
            'new_keys' => $newKeys,
            'existing_keys' => $existingKeysData,
            'new_count' => count($newKeys),
            'existing_count' => count($existingKeysData)
        ]);

    } catch(\Exception $e){
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    exit;
}

// Xử lý import keys đã xác nhận
if(isset($_POST['action']) && $_POST['action'] == 'import_arb'){
    header('Content-Type: application/json');

    try {
        $keys = json_decode($_POST['keys'], true);

        if(!is_array($keys) || empty($keys)){
            echo json_encode(['success' => false, 'message' => 'No keys to import']);
            exit;
        }

        $inserted = 0;
        $updated = 0;

        foreach($keys as $item){
            $key = $item['key'];
            $value = $item['value'];

            // Check if exists
            $existing = DB::table('translations')
                ->where('language_code', 'en')
                ->where('translation_key', $key)
                ->first();

            if($existing){
                // Update existing
                DB::table('translations')
                    ->where('id', $existing->id)
                    ->update([
                        'translation_value' => $value,
                        'updated_at' => now()
                    ]);
                $updated++;
            } else {
                // Insert new
                DB::table('translations')->insert([
                    'language_code' => 'en',
                    'translation_key' => $key,
                    'translation_value' => $value,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $inserted++;
            }
        }

        echo json_encode([
            'success' => true,
            'message' => "Import successful! Inserted: $inserted, Updated: $updated",
            'inserted' => $inserted,
            'updated' => $updated
        ]);

    } catch(\Exception $e){
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    exit;
}

// Xử lý AJAX save
if(isset($_POST['action']) && $_POST['action'] == 'save'){
    header('Content-Type: application/json');

    try {
        $languageCode = $_POST['language_code'] ?? '';
        $translations = $_POST['translations'] ?? [];

        // Debug log
        file_put_contents('/tmp/translation_debug.log',
            "Language: $languageCode\n" .
            "POST data: " . print_r($_POST, true) . "\n" .
            "Translations: " . print_r($translations, true) . "\n",
            FILE_APPEND
        );

        if(!$languageCode){
            echo json_encode(['success' => false, 'message' => 'Language code is required']);
            exit;
        }

        if(!is_array($translations) || empty($translations)){
            echo json_encode([
                'success' => false,
                'message' => 'No translations data received. Please enter at least one translation.',
                'debug' => [
                    'translations_type' => gettype($translations),
                    'translations_count' => is_array($translations) ? count($translations) : 0,
                    'post_keys' => array_keys($_POST)
                ]
            ]);
            exit;
        }

        $updated = 0;
        $inserted = 0;

        foreach($translations as $key => $value){
            // Skip empty values
            if(empty($value)) continue;

            // Check if exists
            $existing = DB::table('translations')
                ->where('language_code', $languageCode)
                ->where('translation_key', $key)
                ->first();

            if($existing){
                // Update
                DB::table('translations')
                    ->where('id', $existing->id)
                    ->update([
                        'translation_value' => $value,
                        'updated_at' => now()
                    ]);
                $updated++;
            } else {
                // Insert
                DB::table('translations')->insert([
                    'language_code' => $languageCode,
                    'translation_key' => $key,
                    'translation_value' => $value,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $inserted++;
            }
        }

        echo json_encode([
            'success' => true,
            'message' => "Successfully saved! Updated: $updated, Inserted: $inserted",
            'updated' => $updated,
            'inserted' => $inserted
        ]);

    } catch(\Exception $e){
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    exit;
}

// API endpoint to get translations for a language
if(isset($_GET['get_lang'])) {
    header('Content-Type: application/json');
    $langCode = $_GET['get_lang'];

    $translations = DB::table('translations')
        ->where('language_code', $langCode)
        ->pluck('translation_value', 'translation_key')
        ->toArray();

    echo json_encode($translations);
    exit;
}

// API endpoint for translation (server-side to avoid CORS)
if(isset($_POST['action']) && $_POST['action'] == 'translate') {
    header('Content-Type: application/json');

    try {
        $text = $_POST['text'] ?? '';
        $targetLang = $_POST['target_lang'] ?? '';

        if(!$text || !$targetLang) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }

        // Try multiple free translation APIs
        $translated = null;

        // Method 1: Lingva Translate (free, no API key, no rate limit)
        try {
            // Use rawurlencode to avoid + for spaces
            $url = "https://lingva.ml/api/v1/en/" . rawurlencode($targetLang) . "/" . rawurlencode($text);
            $response = @file_get_contents($url);
            if($response) {
                $data = json_decode($response, true);
                if(isset($data['translation'])) {
                    // Decode the translation to fix any encoding issues
                    $translated = html_entity_decode($data['translation'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
        } catch(\Exception $e) {
            // Continue to next method
        }

        // Method 2: Translate.argosopentech.com (free, open source)
        if(!$translated) {
            try {
                $url = "https://translate.argosopentech.com/translate";
                $postData = json_encode([
                    'q' => $text,
                    'source' => 'en',
                    'target' => $targetLang,
                    'format' => 'text'
                ], JSON_UNESCAPED_UNICODE);

                $context = stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-Type: application/json\r\n",
                        'content' => $postData,
                        'timeout' => 10
                    ]
                ]);

                $response = @file_get_contents($url, false, $context);
                if($response) {
                    $data = json_decode($response, true);
                    if(isset($data['translatedText'])) {
                        $translated = $data['translatedText'];
                        $translated = $data['translatedText'];
                    }
                }
            } catch(\Exception $e) {
                // Continue to next method
            }
        }

        // Method 3: MyMemory with retry
        if(!$translated) {
            try {
                // Add delay to avoid rate limit
                usleep(500000); // 500ms delay

                // Use rawurlencode and replace + back to space
                $encodedText = rawurlencode($text);
                $url = "https://api.mymemory.translated.net/get?q=" . $encodedText . "&langpair=en|" . $targetLang;
                $response = @file_get_contents($url);
                if($response) {
                    $data = json_decode($response, true);
                    if(isset($data['responseData']['translatedText'])) {
                        $translated = $data['responseData']['translatedText'];
                    }
                }
            } catch(\Exception $e) {
                // Last resort failed
            }
        }

        if($translated) {
            // Clean up the translation - remove extra spaces and fix encoding
            $translated = trim($translated);
            $translated = preg_replace('/\s+/', ' ', $translated); // Replace multiple spaces with single space

            echo json_encode(['success' => true, 'translated' => $translated], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'All translation services failed']);
        }

    } catch(\Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    exit;
}

// Lấy danh sách ngôn ngữ từ clang1
$languages = clang1::getLanguageList();
$languagesEnglish = clang1::$enableLanguageEnglish;
$flagMap = clang1::$flagMap;

// Lấy tất cả translation keys từ English (làm chuẩn)
$enKeys = DB::table('translations')
    ->where('language_code', 'en')
    ->orderBy('translation_key')
    ->get();
require "menu_lang.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Multi-Language Translation Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container-fluid {
            /*max-width: 1600px;*/
            margin: 0 auto;
            /*padding: 10px;*/
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .language-tabs {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .language-tab {
            padding: 10px 20px;
            margin: 5px;
            border: 2px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            transition: all 0.3s;
        }
        .language-tab:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        .language-tab.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .translation-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table-responsive {
            /*max-height: 600px;*/
            overflow-y: auto;
        }
        .translation-input, .english-input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }


        .translation-input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .btn-translate {
            background: linear-gradient(135deg, #4285f4 0%, #34a853 100%);
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            transition: all 0.3s;
            color: white;
        }
        .btn-translate:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            color: white;
        }
        .translate-progress {
            display: none;
            margin-top: 10px;
            padding: 10px;
            background: #e3f2fd;
            border-radius: 5px;
            font-size: 14px;
        }
        .progress-bar-custom {
            height: 20px;
            background: #4285f4;
            border-radius: 10px;
            transition: width 0.3s;
        }
        .stats-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stats-number {
            /*font-size: 32px;*/
            font-weight: bold;
            color: #667eea;
        }
        .flag-icon-lg {
            font-size: 24px;
            margin-right: 10px;
        }
        .search-box {
            margin-bottom: 20px;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }
        .key-column {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        .en-column {
            background-color: #fff3cd;
            font-style: italic;
            color: #856404;
        }
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .loading-overlay.active {
            display: flex;
        }
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <div class="container-fluid">
        <!-- Header -->

        <h3><i class="bi bi-globe"></i> Mobile Multi-Language Translation Manager</h3>

        <!-- Stats -->
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?php echo count($enKeys); ?> Total Translation Keys </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?php echo count($languages); ?> Languages </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number" id="unsavedCount">0 Unsaved Changes</div>

                </div>
            </div>
        </div>

        <!-- Upload ARB File Section -->
        <div class="language-tabs">
            <h5 class="mb-3">Upload ARB File (English translations):</h5>
            <div class="d-flex align-items-center gap-3">
                <input type="file" id="arbFileInput" accept=".arb" class="form-control" style="max-width: 400px;">
                <button class="btn btn-primary" id="uploadArbBtn">
                    📁 Upload & Preview
                </button>
                <button class="btn btn-success" id="pasteArbBtn">
                    📋 Paste ARB Content
                </button>
                <small class="text-muted">Upload .arb file or paste ARB content to add new translation keys for English</small>
            </div>
        </div>

        <!-- Language Tabs -->
        <div class="language-tabs">
            <h5 class="mb-3">Select Language to Edit:</h5>
            <?php foreach($languages as $code => $nativeName): ?>
                <?php if($code == 'en') continue; // Skip English ?>
                <div class="language-tab" data-lang="<?php echo $code; ?>">
                    <span class="fi fi-<?php echo strtolower($flagMap[$code] ?? 'us'); ?> flag-icon-lg"></span>
                    <strong><?php echo $languagesEnglish[$code] ?? $code; ?></strong>
                    <br>
                    <small><?php echo $nativeName; ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Search Box -->
        <input type="text" class="search-box" id="searchBox" placeholder="🔍 Search by key or English text...">

        <!-- ARB Import Confirmation Modal -->
        <div class="modal fade" id="arbConfirmModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm ARB Import</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Total Keys:</strong> <span id="totalKeys">0</span> |
                            <strong>New Keys:</strong> <span id="newKeysCount">0</span> |
                            <strong>Existing Keys:</strong> <span id="existingKeysCount">0</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" id="includeNew" checked>
                                <strong>Import New Keys (<span id="newKeysCount2">0</span>)</strong>
                            </label>
                        </div>
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-bordered">
                                <thead class="table-success sticky-top">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="40%">Key</th>
                                        <th width="55%">Value</th>
                                    </tr>
                                </thead>
                                <tbody id="newKeysList"></tbody>
                            </table>
                        </div>

                        <div class="mb-3 mt-3">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" id="includeExisting">
                                <strong>Update Existing Keys (<span id="existingKeysCount2">0</span>)</strong>
                            </label>
                        </div>
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-bordered">
                                <thead class="table-warning sticky-top">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="40%">Key</th>
                                        <th width="55%">New Value</th>
                                    </tr>
                                </thead>
                                <tbody id="existingKeysList"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="btnConfirmImport">
                            ✅ Confirm Import
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paste ARB Content Modal -->
        <div class="modal fade" id="pasteArbModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">📋 Paste ARB Content</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Instructions:</strong> Paste your ARB file content (JSON format) below.
                            <br>Expected format: <code>{ "key": "value", "@key": { "metadata" } }</code>
                        </div>
                        
                        <div class="mb-3">
                            <label for="arbContentTextarea" class="form-label"><strong>ARB JSON Content:</strong></label>
                            <textarea 
                                class="form-control font-monospace" 
                                id="arbContentTextarea" 
                                rows="20" 
                                placeholder='Paste ARB content here, for example:
{
  "welcome": "Welcome to our app",
  "@welcome": {
    "description": "Welcome message"
  },
  "hello": "Hello {name}",
  "@hello": {
    "placeholders": {
      "name": {}
    }
  }
}'
                                style="font-size: 13px;"
                            ></textarea>
                        </div>
                        
                        <div class="alert alert-warning" id="pasteJsonError" style="display: none;">
                            <strong>⚠️ Invalid JSON:</strong> <span id="pasteJsonErrorMsg"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="btnParsePastedArb">
                            ✅ Parse & Preview
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Translation Table -->
        <div class="translation-table" id="translationTable" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>
                    <span class="fi fi-<span id="currentFlag"></span> flag-icon-lg"></span>
                    Editing: <span id="currentLanguageName"></span>
                </h5>
                <div>
                    <button class="btn btn-warning btn-save" id="btnSaveEng">
                        <i class="bi bi-save"></i> 💾 Save English Only
                    </button>
                    <button class="btn btn-success btn-translate" id="btnGoogleTranslate">
                        <i class="bi bi-translate"></i> 🌐 Google Translate Empty Fields
                    </button>
                    <button class="btn btn-primary btn-save" id="btnSave">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="translate-progress" id="translateProgress">
                <div class="mb-2">
                    <strong>Translating...</strong> <span id="translateStatus">0 / 0</span>
                </div>
                <div style="background: #ddd; border-radius: 10px; height: 20px;">
                    <div class="progress-bar-custom" id="progressBar" style="width: 0%"></div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th width="5%" class="text-center">#</th>
                            <th width="25%" class="key-column">Translation Key</th>
                            <th width="35%" class="en-column">English (Base)</th>
                            <th width="35%">Translation</th>
                        </tr>
                    </thead>
                    <tbody id="translationBody">
                        <!-- Will be filled by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // PHP data to JavaScript
        const translations = <?php echo json_encode($enKeys); ?>;
        const languages = <?php echo json_encode($languages); ?>;
        const languagesEnglish = <?php echo json_encode($languagesEnglish); ?>;
        const flagMap = <?php echo json_encode($flagMap); ?>;

        let currentLanguage = '';
        let currentTranslations = {};
        let englishChanges = {}; // Track English (Base) changes
        let unsavedChanges = 0;
        let originalData = {};
        let originalEnglishData = {}; // Track original English values

        // Load translations for selected language
        function loadTranslations(langCode) {
            currentLanguage = langCode;
            $('#loadingOverlay').addClass('active');

            // Update UI
            $('#currentLanguageName').text(languagesEnglish[langCode] + ' (' + languages[langCode] + ')');
            $('#currentFlag').text(flagMap[langCode].toLowerCase());
            $('.language-tab').removeClass('active');
            $(`.language-tab[data-lang="${langCode}"]`).addClass('active');

            // Get existing translations for this language
            $.get('?get_lang=' + langCode, function(data) {
                if(data && typeof data === 'object') {
                    currentTranslations = data;
                } else {
                    currentTranslations = {};
                }

                originalData = JSON.parse(JSON.stringify(currentTranslations));
                
                // Initialize English tracking
                englishChanges = {};
                originalEnglishData = {};
                translations.forEach(item => {
                    originalEnglishData[item.translation_key] = item.translation_value;
                });
                
                renderTable();
                $('#translationTable').show();
                $('#loadingOverlay').removeClass('active');
            }).fail(function() {
                currentTranslations = {};
                originalData = {};
                renderTable();
                $('#translationTable').show();
                $('#loadingOverlay').removeClass('active');
            });
        }

        // Render translation table
        function renderTable(searchTerm = '') {
            const tbody = $('#translationBody');
            tbody.empty();

            let counter = 0;
            translations.forEach((item, index) => {
                const key = item.translation_key;
                const enValue = item.translation_value;

                // Search filter
                if(searchTerm) {
                    const search = searchTerm.toLowerCase();
                    if(!key.toLowerCase().includes(search) &&
                       !enValue.toLowerCase().includes(search)) {
                        return;
                    }
                }

                counter++;
                const currentValue = currentTranslations[key] || '';

                const row = `
                    <tr>
                        <td class="text-center">${counter}</td>
                        <td class="key-column"><code>${key}</code></td>
                        <td class="en-column">
                            <input type="text"
                                   class="english-input"
                                   data-key="${key}"
                                   value="${enValue}"
                                   placeholder="Enter English text..."
                                   style="border-color: #90caf9; background: #e3f2fd;">
                        </td>
                        <td>
                            <input type="text"
                                   class="translation-input"
                                   data-key="${key}"
                                   value="${currentValue}"
                                   placeholder="Enter ${languagesEnglish[currentLanguage]} translation...">
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        // Track changes
        $(document).on('input', '.translation-input', function() {
            const key = $(this).data('key');
            const value = $(this).val();
            currentTranslations[key] = value;

            // Count unsaved
            unsavedChanges = 0;
            for(let k in currentTranslations) {
                if(currentTranslations[k] !== (originalData[k] || '')) {
                    unsavedChanges++;
                }
            }
            // Add English changes count
            for(let k in englishChanges) {
                if(englishChanges[k] !== originalEnglishData[k]) {
                    unsavedChanges++;
                }
            }

            $('#unsavedCount').text(unsavedChanges);
        });

        // Track English (Base) changes
        $(document).on('input', '.english-input', function() {
            const key = $(this).data('key');
            const value = $(this).val();
            englishChanges[key] = value;

            // Update the translations array in memory
            const item = translations.find(t => t.translation_key === key);
            if(item) {
                item.translation_value = value;
            }

            // Count unsaved
            unsavedChanges = 0;
            for(let k in currentTranslations) {
                if(currentTranslations[k] !== (originalData[k] || '')) {
                    unsavedChanges++;
                }
            }
            // Add English changes count
            for(let k in englishChanges) {
                if(englishChanges[k] !== originalEnglishData[k]) {
                    unsavedChanges++;
                }
            }

            $('#unsavedCount').text(unsavedChanges);
        });

        // Save English Only button
        $('#btnSaveEng').click(function() {
            if(Object.keys(englishChanges).length === 0) {
                alert('No English changes to save');
                return;
            }

            if(!confirm(`Save ${Object.keys(englishChanges).length} English changes?`)) {
                return;
            }

            console.log('Saving English changes:', englishChanges);

            $('#loadingOverlay').addClass('active');

            const englishFormData = new FormData();
            englishFormData.append('action', 'save');
            englishFormData.append('language_code', 'en');

            for(let key in englishChanges) {
                if(englishChanges[key]) {
                    englishFormData.append('translations[' + key + ']', englishChanges[key]);
                }
            }

            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: englishFormData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#loadingOverlay').removeClass('active');
                    console.log('English save response:', response);

                    if(response.success) {
                        alert('✅ English saved: ' + response.message);
                        
                        // Update original English data
                        for(let k in englishChanges) {
                            originalEnglishData[k] = englishChanges[k];
                            
                            // Update translations array in memory
                            const item = translations.find(t => t.translation_key === k);
                            if(item) {
                                item.translation_value = englishChanges[k];
                            }
                        }
                        englishChanges = {};
                        
                        // Recalculate unsaved count (only current language changes remain)
                        unsavedChanges = 0;
                        for(let k in currentTranslations) {
                            if(currentTranslations[k] !== (originalData[k] || '')) {
                                unsavedChanges++;
                            }
                        }
                        $('#unsavedCount').text(unsavedChanges);
                        
                        // NO reload - data updated in memory
                    } else {
                        alert('❌ Error saving English: ' + response.message);
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').removeClass('active');
                    console.log('Error response:', xhr.responseText);
                    alert('❌ Error saving English: ' + xhr.responseText);
                }
            });
        });

        // Save translations
        $('#btnSave').click(function() {
            if(Object.keys(currentTranslations).length === 0 && Object.keys(englishChanges).length === 0) {
                alert('No translations to save');
                return;
            }

            console.log('Saving translations for:', currentLanguage);
            console.log('Data to save:', currentTranslations);
            console.log('English changes:', englishChanges);

            $('#loadingOverlay').addClass('active');

            // Convert to FormData for proper POST
            const formData = new FormData();
            formData.append('action', 'save');
            formData.append('language_code', currentLanguage);

            // Add each translation
            for(let key in currentTranslations) {
                if(currentTranslations[key]) { // Only send non-empty values
                    formData.append('translations[' + key + ']', currentTranslations[key]);
                }
            }

            // Save English changes separately
            let englishSavePromise = Promise.resolve();
            if(Object.keys(englishChanges).length > 0) {
                const englishFormData = new FormData();
                englishFormData.append('action', 'save');
                englishFormData.append('language_code', 'en');

                for(let key in englishChanges) {
                    if(englishChanges[key]) {
                        englishFormData.append('translations[' + key + ']', englishChanges[key]);
                    }
                }

                englishSavePromise = fetch(window.location.href, {
                    method: 'POST',
                    body: englishFormData
                }).then(r => r.json());
            }

            // Save current language translations
            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Server response:', response);

                    // Wait for English save to complete
                    englishSavePromise.then(function(englishResponse) {
                        $('#loadingOverlay').removeClass('active');

                        if(response.success) {
                            let message = '✅ ' + response.message;
                            if(englishResponse && englishResponse.success) {
                                message += '\n✅ English: ' + englishResponse.message;
                            }
                            alert(message);
                            
                            unsavedChanges = 0;
                            $('#unsavedCount').text('0');
                            originalData = JSON.parse(JSON.stringify(currentTranslations));
                            
                            // Update original English data
                            for(let k in englishChanges) {
                                originalEnglishData[k] = englishChanges[k];
                            }
                            englishChanges = {};
                        } else {
                            alert('❌ Error: ' + response.message);
                        }
                    }).catch(function(err) {
                        $('#loadingOverlay').removeClass('active');
                        alert('❌ Error saving English: ' + err.message);
                    });
                },
                error: function(xhr) {
                    $('#loadingOverlay').removeClass('active');
                    console.log('Error response:', xhr.responseText);
                    alert('❌ Error saving translations: ' + xhr.responseText);
                }
            });
        });

        // Google Translate function (using server-side API to avoid CORS)
        async function translateText(text, targetLang) {
            try {
                const formData = new FormData();
                formData.append('action', 'translate');
                formData.append('text', text);
                formData.append('target_lang', targetLang);

                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if(data.success && data.translated) {
                    return data.translated;
                }

                console.warn('Translation failed:', data.message);
                return null;
            } catch(error) {
                console.error('Translation error:', error);
                return null;
            }
        }

        // Map language codes to translation API format (only special cases)
        function mapLanguageCode(code) {
            // Special mappings for translation APIs that require different format
            const specialMapping = {
                'zh': 'zh-CN',  // Chinese Simplified
                'km': 'km',     // Khmer (Cambodian)
            };

            // Return special mapping if exists, otherwise use original code from clang1
            return specialMapping[code] || code;
        }

        // Google Translate Button Click
        $('#btnGoogleTranslate').click(async function() {
            if(!currentLanguage) {
                alert('Please select a language first');
                return;
            }

            // Get all empty input fields
            const emptyInputs = $('.translation-input').filter(function() {
                return !$(this).val().trim();
            });

            if(emptyInputs.length === 0) {
                alert('✅ All fields are already filled!');
                return;
            }

            if(!confirm(`Found ${emptyInputs.length} empty fields. Translate them using Google Translate?`)) {
                return;
            }

            // Show progress
            $('#translateProgress').show();
            $('#btnGoogleTranslate').prop('disabled', true);

            const total = emptyInputs.length;
            let completed = 0;
            let success = 0;
            let failed = 0;

            const targetLang = mapLanguageCode(currentLanguage);

            // Translate each empty field
            for(let i = 0; i < emptyInputs.length; i++) {
                const input = $(emptyInputs[i]);
                const key = input.data('key');

                // Find English text from translations array
                const enItem = translations.find(t => t.translation_key === key);
                if(!enItem) continue;

                const enText = enItem.translation_value;

                // Update progress
                $('#translateStatus').text(`${completed + 1} / ${total} - Translating: ${key}`);
                $('#progressBar').css('width', ((completed / total) * 100) + '%');

                // Translate
                const translated = await translateText(enText, targetLang);

                if(translated) {
                    input.val(translated);
                    currentTranslations[key] = translated;
                    success++;

                    // Highlight the input
                    input.css('background-color', '#d4edda');
                    setTimeout(() => {
                        input.css('background-color', '');
                    }, 2000);
                } else {
                    failed++;
                    // Mark failed input with red background
                    input.css('background-color', '#f8d7da');
                    setTimeout(() => {
                        input.css('background-color', '');
                    }, 2000);
                }

                completed++;

                // Longer delay to avoid rate limiting (1 second per request)
                await new Promise(resolve => setTimeout(resolve, 1000));
            }

            // Update progress bar to 100%
            $('#progressBar').css('width', '100%');
            $('#translateStatus').text(`✅ Completed: ${success} success, ${failed} failed`);

            // Hide progress after 3 seconds
            setTimeout(() => {
                $('#translateProgress').hide();
                $('#btnGoogleTranslate').prop('disabled', false);
            }, 3000);

            // Update unsaved count
            unsavedChanges = 0;
            for(let k in currentTranslations) {
                if(currentTranslations[k] !== (originalData[k] || '')) {
                    unsavedChanges++;
                }
            }
            $('#unsavedCount').text(unsavedChanges);

            alert(`✅ Translation completed!\n✓ Success: ${success}\n✗ Failed: ${failed}\n\nPlease review and click "Save Changes"`);
        });

        // Upload ARB file handler
        let parsedArbData = null;

        $('#uploadArbBtn').click(function() {
            const fileInput = $('#arbFileInput')[0];
            const file = fileInput.files[0];

            if(!file) {
                alert('❌ Please select an .arb file first');
                return;
            }

            // Show loading
            $('#loadingOverlay').addClass('active');

            const formData = new FormData();
            formData.append('action', 'parse_arb');
            formData.append('arb_file', file);

            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#loadingOverlay').removeClass('active');

                    if(response.success) {
                        parsedArbData = response;

                        // Update modal with data
                        $('#totalKeys').text(response.total);
                        $('#newKeysCount').text(response.new_count);
                        $('#newKeysCount2').text(response.new_count);
                        $('#existingKeysCount').text(response.existing_count);
                        $('#existingKeysCount2').text(response.existing_count);

                        // Populate new keys table
                        const newKeysList = $('#newKeysList');
                        newKeysList.empty();
                        response.new_keys.forEach((item, index) => {
                            newKeysList.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td><code>${item.key}</code></td>
                                    <td>${item.value}</td>
                                </tr>
                            `);
                        });

                        // Populate existing keys table
                        const existingKeysList = $('#existingKeysList');
                        existingKeysList.empty();
                        response.existing_keys.forEach((item, index) => {
                            existingKeysList.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td><code>${item.key}</code></td>
                                    <td>${item.value}</td>
                                </tr>
                            `);
                        });

                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('arbConfirmModal'));
                        modal.show();

                    } else {
                        alert('❌ Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').removeClass('active');
                    alert('❌ Error uploading file: ' + xhr.responseText);
                }
            });
        });

        // Paste ARB button handler
        $('#pasteArbBtn').click(function() {
            $('#arbContentTextarea').val('');
            $('#pasteJsonError').hide();
            const modal = new bootstrap.Modal(document.getElementById('pasteArbModal'));
            modal.show();
        });

        // Parse pasted ARB content
        $('#btnParsePastedArb').click(function() {
            const content = $('#arbContentTextarea').val().trim();
            
            if(!content) {
                $('#pasteJsonErrorMsg').text('Content is empty');
                $('#pasteJsonError').show();
                return;
            }

            // Try to parse JSON
            let arbData;
            try {
                arbData = JSON.parse(content);
            } catch(e) {
                $('#pasteJsonErrorMsg').text(e.message);
                $('#pasteJsonError').show();
                return;
            }

            // Validate it's an object
            if(typeof arbData !== 'object' || Array.isArray(arbData)) {
                $('#pasteJsonErrorMsg').text('ARB content must be a JSON object');
                $('#pasteJsonError').show();
                return;
            }

            // Hide error
            $('#pasteJsonError').hide();

            // Show loading
            $('#loadingOverlay').addClass('active');

            // Close paste modal
            bootstrap.Modal.getInstance(document.getElementById('pasteArbModal')).hide();

            // Send to server for processing
            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {
                    action: 'parse_arb_paste',
                    json_content: JSON.stringify(arbData)
                },
                success: function(response) {
                    $('#loadingOverlay').removeClass('active');

                    if(response.success) {
                        parsedArbData = response;

                        // Update modal with data
                        $('#totalKeys').text(response.total);
                        $('#newKeysCount').text(response.new_count);
                        $('#newKeysCount2').text(response.new_count);
                        $('#existingKeysCount').text(response.existing_count);
                        $('#existingKeysCount2').text(response.existing_count);

                        // Populate new keys table
                        const newKeysList = $('#newKeysList');
                        newKeysList.empty();
                        response.new_keys.forEach((item, index) => {
                            newKeysList.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td><code>${item.key}</code></td>
                                    <td>${item.value}</td>
                                </tr>
                            `);
                        });

                        // Populate existing keys table
                        const existingKeysList = $('#existingKeysList');
                        existingKeysList.empty();
                        response.existing_keys.forEach((item, index) => {
                            existingKeysList.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td><code>${item.key}</code></td>
                                    <td>${item.value}</td>
                                </tr>
                            `);
                        });

                        // Show confirmation modal
                        const modal = new bootstrap.Modal(document.getElementById('arbConfirmModal'));
                        modal.show();

                    } else {
                        alert('❌ Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').removeClass('active');
                    alert('❌ Error processing ARB content: ' + xhr.responseText);
                }
            });
        });

        // Confirm import handler
        $('#btnConfirmImport').click(function() {
            if(!parsedArbData) {
                alert('❌ No data to import');
                return;
            }

            const includeNew = $('#includeNew').is(':checked');
            const includeExisting = $('#includeExisting').is(':checked');

            if(!includeNew && !includeExisting) {
                alert('❌ Please select at least one option (New Keys or Existing Keys)');
                return;
            }

            let keysToImport = [];

            if(includeNew) {
                keysToImport = keysToImport.concat(parsedArbData.new_keys);
            }

            if(includeExisting) {
                keysToImport = keysToImport.concat(parsedArbData.existing_keys);
            }

            if(keysToImport.length === 0) {
                alert('❌ No keys to import');
                return;
            }

            // Show loading
            $('#loadingOverlay').addClass('active');

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('arbConfirmModal')).hide();

            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {
                    action: 'import_arb',
                    keys: JSON.stringify(keysToImport)
                },
                success: function(response) {
                    $('#loadingOverlay').removeClass('active');

                    if(response.success) {
                        alert('✅ ' + response.message);

                        // Reset file input
                        $('#arbFileInput').val('');
                        parsedArbData = null;

                        // Reload page to show new keys
                        location.reload();
                    } else {
                        alert('❌ Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').removeClass('active');
                    alert('❌ Error importing keys: ' + xhr.responseText);
                }
            });
        });

        // Language tab click
        $('.language-tab').click(function() {
            const langCode = $(this).data('lang');
            
            // Update URL hash
            window.location.hash = 'lang=' + langCode;
            
            loadTranslations(langCode);
        });

        // Auto-load language from URL hash on page load
        $(document).ready(function() {
            const hash = window.location.hash;
            if(hash && hash.startsWith('#lang=')) {
                const langCode = hash.substring(6); // Remove '#lang='
                
                // Check if language exists
                if(languages[langCode]) {
                    // Auto-click the language tab
                    $(`.language-tab[data-lang="${langCode}"]`).click();
                }
            }
        });

        // Search functionality
        $('#searchBox').on('input', function() {
            const searchTerm = $(this).val();
            renderTable(searchTerm);
        });

        // Warn before leaving with unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if(unsavedChanges > 0) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        });
    </script>
</body>
</html>
