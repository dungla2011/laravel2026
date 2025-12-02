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

// API endpoint for translation
if(isset($_POST['action']) && $_POST['action'] == 'translate') {
    header('Content-Type: application/json');

    try {
        $text = $_POST['text'] ?? '';
        $targetLang = $_POST['target_lang'] ?? '';

        if(!$text || !$targetLang) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }

        // Bảo vệ placeholders và HTML tags
        $placeholders = [];
        $placeholderIndex = 0;
        
        // Tạo unique ID để tránh bị translate nhầm
        $uniqueId = substr(md5(microtime()), 0, 6);
        
        // Bảo vệ [:word]
        $textWithPlaceholders = preg_replace_callback(
            '/\[:([a-zA-Z_][a-zA-Z0-9_]*)\]/u',
            function($matches) use (&$placeholders, &$placeholderIndex, $uniqueId) {
                $placeholder = $matches[0];
                $token = "XPHX{$uniqueId}X{$placeholderIndex}X";
                $placeholders[$token] = $placeholder;
                $placeholderIndex++;
                return $token;
            },
            $text
        );
        
        // Bảo vệ :word
        $textWithPlaceholders = preg_replace_callback(
            '/(?<!\S):([a-zA-Z_][a-zA-Z0-9_]*)/u',
            function($matches) use (&$placeholders, &$placeholderIndex, $uniqueId) {
                $placeholder = $matches[0];
                $token = "XPHX{$uniqueId}X{$placeholderIndex}X";
                $placeholders[$token] = $placeholder;
                $placeholderIndex++;
                return $token;
            },
            $textWithPlaceholders
        );

        // Bảo vệ HTML tags - sử dụng format đặc biệt hơn
        $textWithPlaceholders = preg_replace_callback(
            '/<[^>]+>/u',
            function($matches) use (&$placeholders, &$placeholderIndex, $uniqueId) {
                $tag = $matches[0];
                $token = "XHTMLX{$uniqueId}X{$placeholderIndex}X";
                $placeholders[$token] = $tag;
                $placeholderIndex++;
                return $token;
            },
            $textWithPlaceholders
        );

        $translated = null;

        // Try Google Translate API
        try {
            $url = "https://translate.googleapis.com/translate_a/single?" . http_build_query([
                'client' => 'gtx',
                'sl' => 'en',
                'tl' => $targetLang,
                'dt' => 't',
                'q' => $textWithPlaceholders,
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                
                if (isset($data[0]) && is_array($data[0])) {
                    $translatedParts = [];
                    foreach ($data[0] as $part) {
                        if (isset($part[0])) {
                            $translatedParts[] = $part[0];
                        }
                    }
                    $translated = implode('', $translatedParts);
                }
            }
        } catch(\Exception $e) {
            // Continue to fallback
        }

        if($translated) {
            // Khôi phục placeholders - linh hoạt với cả lowercase và có dấu cách
            foreach ($placeholders as $token => $placeholder) {
                // Replace exact token
                $translated = str_replace($token, $placeholder, $translated);
                
                // Replace lowercase version (Google Translate có thể lowercase)
                $translated = str_replace(strtolower($token), $placeholder, $translated);
                
                // Replace với khoảng trắng thừa
                $translated = preg_replace(
                    '/' . preg_quote($token, '/') . '\s*/ui',
                    $placeholder,
                    $translated
                );
                
                // Replace version bị cắt hoặc thêm ký tự
                $translated = preg_replace(
                    '/\s*' . preg_quote($token, '/') . '\s*/ui',
                    $placeholder,
                    $translated
                );
            }
            
            // Cleanup: Remove any remaining placeholder-like patterns
            $translated = preg_replace('/X(PH|HTML)X[a-f0-9]{6}X\d+X/ui', '', $translated);

            $translated = trim($translated);
            echo json_encode(['success' => true, 'translated' => $translated], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Translation service failed']);
        }

    } catch(\Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    exit;
}

// API endpoint to get BlockUi record data
if(isset($_GET['get_record'])) {
    header('Content-Type: application/json');
    $recordId = $_GET['get_record'];

    $record = \App\Models\BlockUi::find($recordId);
    
    if(!$record) {
        echo json_encode(['success' => false, 'message' => 'Record not found']);
        exit;
    }

    $blockUi = new \App\Models\BlockUi();
    $translatableFields = $blockUi->getTranslatableAttributes();

    $data = [
        'id' => $record->id,
        'sname' => $record->sname,
        'fields' => []
    ];

    foreach($translatableFields as $field) {
        // Use getTranslations() to get all languages, not just current locale
        if(method_exists($record, 'getTranslations')) {
            $jsonData = $record->getTranslations($field);
        } else {
            $value = $record->$field;
            $jsonData = is_string($value) ? json_decode($value, true) : $value;
        }
        
        if(!is_array($jsonData)) {
            $jsonData = ['en' => $jsonData ?: ''];
        }

        $data['fields'][$field] = $jsonData;
    }

    echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

// API endpoint to save translations
if(isset($_POST['action']) && $_POST['action'] == 'save') {
    header('Content-Type: application/json');

    try {
        $recordId = $_POST['record_id'] ?? '';
        $fieldName = $_POST['field_name'] ?? '';
        $translations = $_POST['translations'] ?? [];

        if(!$recordId || !$fieldName) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }

        if(!is_array($translations) || empty($translations)) {
            echo json_encode(['success' => false, 'message' => 'No translations data']);
            exit;
        }

        $record = \App\Models\BlockUi::find($recordId);
        
        if(!$record) {
            echo json_encode(['success' => false, 'message' => 'Record not found']);
            exit;
        }

        // Cập nhật field với JSON data
        $record->$fieldName = $translations;
        $record->save();

        echo json_encode([
            'success' => true,
            'message' => "Field '$fieldName' saved successfully!",
        ]);

    } catch(\Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    exit;
}

// Lấy danh sách ngôn ngữ
$languages = clang1::getLanguageList();
$languagesEnglish = clang1::$enableLanguageEnglish;
$flagMap = clang1::$flagMap;

// Lấy danh sách BlockUi records
$records = \App\Models\BlockUi::orderBy('sname')->get();

// Lấy translatable fields
$blockUi = new \App\Models\BlockUi();
$translatableFields = $blockUi->getTranslatableAttributes();

require "menu_lang.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlockUi Multi-Language Translation Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .record-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .record-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .record-card.active {
            border-color: #667eea;
            background: #f0f4ff;
        }
        .field-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .language-row {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .language-row:last-child {
            border-bottom: none;
        }
        .translation-input {
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
        .btn-translate {
            background: linear-gradient(135deg, #4285f4 0%, #34a853 100%);
            border: none;
            color: white;
            padding: 6px 15px;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn-translate:hover {
            transform: scale(1.05);
            color: white;
        }
        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            color: white;
            font-weight: bold;
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
        .flag-icon-lg {
            font-size: 24px;
            margin-right: 10px;
        }
        .stats-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 20px;
            font-size: 13px;
            margin-left: 10px;
        }
        .field-title {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .source-text {
            background: #fff3cd;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #ffc107;
            margin-bottom: 15px;
        }
        .search-box {
            margin-bottom: 15px;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Left Sidebar: Records List -->
            <div class="col-md-3">
                <div class="header-section">
                    <h5><i class="bi bi-database"></i> BlockUi Records</h5>
                    <small>Total: <?php echo count($records); ?> records</small>
                </div>

                <input type="text" class="search-box" id="searchBox" placeholder="🔍 Search by sname...">

                <div id="recordsList">
                    <?php foreach($records as $record): ?>
                    <div class="record-card" data-id="<?php echo $record->id; ?>" data-sname="<?php echo $record->sname; ?>">
                        <div><strong><?php echo $record->sname; ?></strong></div>
                        <small class="text-muted">ID: <?php echo $record->id; ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Main Content: Translation Editor -->
            <div class="col-md-9">
                <div class="header-section">
                    <h3><i class="bi bi-translate"></i> BlockUi Multi-Language Manager</h3>
                    <p class="mb-0">Translatable fields: <strong><?php echo implode(', ', $translatableFields); ?></strong></p>
                </div>

                <div id="editorContent">
                    <div class="alert alert-info">
                        <strong>👈 Select a record</strong> from the left sidebar to start editing translations.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const languages = <?php echo json_encode($languages); ?>;
        const languagesEnglish = <?php echo json_encode($languagesEnglish); ?>;
        const flagMap = <?php echo json_encode($flagMap); ?>;
        
        let currentRecord = null;
        let currentData = {};

        // Load record data
        function loadRecord(recordId) {
            $('#loadingOverlay').addClass('active');

            $.get('?get_record=' + recordId, function(response) {
                if(response.success) {
                    currentRecord = response.data;
                    currentData = response.data.fields;
                    renderEditor();
                } else {
                    alert('Error: ' + response.message);
                }
                $('#loadingOverlay').removeClass('active');
            }).fail(function() {
                alert('Failed to load record data');
                $('#loadingOverlay').removeClass('active');
            });
        }

        // Render editor
        function renderEditor() {
            if(!currentRecord) return;

            let html = `
                <div class="alert alert-primary">
                    <strong>Editing:</strong> ${currentRecord.sname} (ID: ${currentRecord.id})
                </div>
            `;

            // Render each field
            for(let fieldName in currentData) {
                const fieldData = currentData[fieldName];
                const enText = fieldData['en'] || '';

                html += `
                    <div class="field-section">
                        <div class="field-title">📝 Field: ${fieldName}</div>

                        <div class="mb-3">
                            <button class="btn btn-translate btn-sm" onclick="translateAllEmpty('${fieldName}')">
                                🌍 Auto-translate all empty fields
                            </button>
                            <button class="btn btn-translate btn-sm ms-2" onclick="translateAllForce('${fieldName}')" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);">
                                🔥 Force translate ALL (overwrite existing)
                            </button>
                        </div>
                `;

                // Render English first (editable but not translatable)
                html += `
                    <div class="language-row" style="background: #fff3cd; border-left: 4px solid #ffc107;">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <span class="fi fi-us flag-icon-lg"></span>
                                <strong>English</strong><br>
                                <small class="text-muted">Base language</small>
                            </div>
                            <div class="col-md-8">
                                <textarea 
                                    class="translation-input" 
                                    rows="2"
                                    data-field="${fieldName}"
                                    data-lang="en"
                                    placeholder="Enter English text (base language)..."
                                    style="border-color: #ffc107; background: #fffbf0;"
                                >${enText}</textarea>
                            </div>
                            <div class="col-md-2">
                                <span class="badge bg-warning text-dark w-100">Base Language</span>
                            </div>
                        </div>
                    </div>
                `;

                // Render each language
                for(let langCode in languages) {
                    if(langCode === 'en') continue;

                    const currentValue = fieldData[langCode] || '';
                    const flagClass = 'fi fi-' + flagMap[langCode].toLowerCase();

                    html += `
                        <div class="language-row">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <span class="${flagClass} flag-icon-lg"></span>
                                    <strong>${languagesEnglish[langCode]}</strong><br>
                                    <small class="text-muted">${languages[langCode]}</small>
                                </div>
                                <div class="col-md-8">
                                    <textarea 
                                        class="translation-input" 
                                        rows="2"
                                        data-field="${fieldName}"
                                        data-lang="${langCode}"
                                        placeholder="Enter ${languagesEnglish[langCode]} translation..."
                                    >${currentValue}</textarea>
                                </div>
                                <div class="col-md-2">
                                    <button 
                                        class="btn btn-translate btn-sm w-100" 
                                        onclick="translateSingle('${fieldName}', '${langCode}')"
                                    >
                                        🤖 Translate
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }

                html += `
                        <div class="mt-3">
                            <button class="btn btn-save w-100" onclick="saveField('${fieldName}')">
                                💾 Save "${fieldName}" Field
                            </button>
                        </div>
                    </div>
                `;
            }

            $('#editorContent').html(html);
        }

        // Translate single field
        async function translateSingle(fieldName, targetLang) {
            const sourceText = currentData[fieldName]['en'] || '';
            
            if(!sourceText) {
                alert('No English text to translate');
                return;
            }

            const $input = $(`.translation-input[data-field="${fieldName}"][data-lang="${targetLang}"]`);
            $input.prop('disabled', true);

            try {
                const result = await translateText(sourceText, targetLang);
                if(result) {
                    $input.val(result);
                    updateCurrentData(fieldName, targetLang, result);
                } else {
                    alert('Translation failed');
                }
            } catch(e) {
                alert('Error: ' + e.message);
            }

            $input.prop('disabled', false);
        }

        // Translate all empty fields
        async function translateAllEmpty(fieldName) {
            const sourceText = currentData[fieldName]['en'] || '';
            
            if(!sourceText) {
                alert('No English text to translate');
                return;
            }

            let emptyLangs = [];
            for(let lang in languages) {
                if(lang === 'en') continue;
                if(!currentData[fieldName][lang] || !currentData[fieldName][lang].trim()) {
                    emptyLangs.push(lang);
                }
            }

            if(emptyLangs.length === 0) {
                alert('All languages already have translations');
                return;
            }

            if(!confirm(`Translate to ${emptyLangs.length} empty languages?`)) {
                return;
            }

            $('#loadingOverlay').addClass('active');

            for(let lang of emptyLangs) {
                try {
                    const result = await translateText(sourceText, lang);
                    if(result) {
                        const $input = $(`.translation-input[data-field="${fieldName}"][data-lang="${lang}"]`);
                        $input.val(result);
                        updateCurrentData(fieldName, lang, result);
                    }
                    // Delay to avoid rate limit
                    await new Promise(resolve => setTimeout(resolve, 300));
                } catch(e) {
                    console.error('Translation error for ' + lang, e);
                }
            }

            $('#loadingOverlay').removeClass('active');
            alert('Auto-translation completed!');
        }

        // Force translate all languages (including existing)
        async function translateAllForce(fieldName) {
            const sourceText = currentData[fieldName]['en'] || '';
            
            if(!sourceText) {
                alert('No English text to translate');
                return;
            }

            const allLangs = [];
            for(let lang in languages) {
                if(lang === 'en') continue;
                allLangs.push(lang);
            }

            if(!confirm(`🔥 Force translate ALL ${allLangs.length} languages?\n\n⚠️ This will OVERWRITE existing translations!`)) {
                return;
            }

            $('#loadingOverlay').addClass('active');

            let success = 0;
            let failed = 0;

            for(let lang of allLangs) {
                try {
                    const result = await translateText(sourceText, lang);
                    if(result) {
                        const $input = $(`.translation-input[data-field="${fieldName}"][data-lang="${lang}"]`);
                        $input.val(result);
                        updateCurrentData(fieldName, lang, result);
                        success++;
                    } else {
                        failed++;
                    }
                    // Delay to avoid rate limit
                    await new Promise(resolve => setTimeout(resolve, 300));
                } catch(e) {
                    console.error('Translation error for ' + lang, e);
                    failed++;
                }
            }

            $('#loadingOverlay').removeClass('active');
            alert(`✅ Force translation completed!\n\n✓ Success: ${success}\n✗ Failed: ${failed}\n\nPlease review and save.`);
        }

        // Translate text via server
        function translateText(text, targetLang) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: {
                        action: 'translate',
                        text: text,
                        target_lang: targetLang
                    },
                    success: function(response) {
                        if(response.success) {
                            resolve(response.translated);
                        } else {
                            resolve(null);
                        }
                    },
                    error: function() {
                        resolve(null);
                    }
                });
            });
        }

        // Update current data
        function updateCurrentData(fieldName, lang, value) {
            if(!currentData[fieldName]) {
                currentData[fieldName] = {};
            }
            currentData[fieldName][lang] = value;
        }

        // Save field
        function saveField(fieldName) {
            // Collect all values from inputs (including English)
            const translations = {};
            
            $(`.translation-input[data-field="${fieldName}"]`).each(function() {
                const lang = $(this).data('lang');
                const value = $(this).val();
                translations[lang] = value;
            });

            $('#loadingOverlay').addClass('active');

            $.ajax({
                url: '',
                method: 'POST',
                data: {
                    action: 'save',
                    record_id: currentRecord.id,
                    field_name: fieldName,
                    translations: translations
                },
                success: function(response) {
                    if(response.success) {
                        alert('✅ ' + response.message);
                        // Update current data
                        currentData[fieldName] = translations;
                    } else {
                        alert('❌ Error: ' + response.message);
                    }
                    $('#loadingOverlay').removeClass('active');
                },
                error: function() {
                    alert('❌ Save failed');
                    $('#loadingOverlay').removeClass('active');
                }
            });
        }

        // Record card click
        $(document).on('click', '.record-card', function() {
            const recordId = $(this).data('id');
            $('.record-card').removeClass('active');
            $(this).addClass('active');
            
            // Update URL hash
            window.location.hash = 'id=' + recordId;
            
            loadRecord(recordId);
        });

        // Search functionality
        $('#searchBox').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('.record-card').each(function() {
                const sname = $(this).data('sname').toLowerCase();
                if(sname.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Track input changes
        $(document).on('input', '.translation-input', function() {
            const fieldName = $(this).data('field');
            const lang = $(this).data('lang');
            const value = $(this).val();
            updateCurrentData(fieldName, lang, value);
        });

        // Load record from URL hash on page load
        $(document).ready(function() {
            const hash = window.location.hash;
            if(hash && hash.startsWith('#id=')) {
                const recordId = hash.substring(4); // Remove '#id='
                
                // Find and activate the record card
                const $card = $(`.record-card[data-id="${recordId}"]`);
                if($card.length > 0) {
                    $card.addClass('active');
                    loadRecord(recordId);
                    
                    // Scroll to the active card
                    setTimeout(() => {
                        $card[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                }
            }
        });

        // Handle browser back/forward buttons
        $(window).on('hashchange', function() {
            const hash = window.location.hash;
            if(hash && hash.startsWith('#id=')) {
                const recordId = hash.substring(4);
                
                const $card = $(`.record-card[data-id="${recordId}"]`);
                if($card.length > 0) {
                    $('.record-card').removeClass('active');
                    $card.addClass('active');
                    loadRecord(recordId);
                }
            }
        });
    </script>
</body>
</html>
