<?php
require "menu_lang.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);


require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


if(!isSupperAdmin_()){
    die("NOT ADMINS");
}

// Handle AJAX save request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: application/json');



    try {
        $table = $_POST['table'] ?? '';
        \LadLib\Common\Database\MetaOfTableInDb::deleteClearCacheMetaApi($table);
        $arrayLanguage = json_decode($_POST['languages'] ?? '[]', true) ?? [];
        $data = json_decode($_POST['data'] ?? '{}', true) ?? [];

          // Validate inputs
        if (empty($table)) {
            throw new Exception('Table name is required');
        }
        if (empty($arrayLanguage)) {
            throw new Exception('Languages array is required');
        }

        $metaModel = \App\Models\ModelMetaInfo::class;
        $savedCount = 0;

        // Save to DB: model_meta_infos table
        foreach ($data as $field => $values) {
            // Find or create meta record for this table.field
            $meta = $metaModel::where('table_name_model', $table)
                ->where('field', $field)
                ->first();

            if (!$meta) {
                // Create new record
                $meta = new $metaModel();
                $meta->table = $table;
                $meta->field = $field;
            }

            // Build translations JSON: only save non-empty values
            $translations = [];
            foreach ($arrayLanguage as $lang) {
                if (!empty($values[$lang])) {
                    $translations[$lang] = $values[$lang];
                }
            }

            // Save to translations column
            $meta->translations = $translations;
            $meta->save();
            $savedCount++;
        }

        echo json_encode([
            'success' => true,
            'message' => "Saved {$savedCount} field translations to database!",
            'timestamp' => date('H:i:s'),
            'fieldsWritten' => $savedCount
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
    exit;
}

// Get all tables
$mTable = \LadLib\Laravel\Database\DbHelperLaravel::getAllTableName();
$urlCurrent = \LadLib\Common\UrlHelper1::getUriWithoutParam();
sort($mTable);

// Get current table
$table = request('table') ?: 'users';

// Get meta and fields
$meta = \LadLib\Common\Database\MetaTableCommon::getMetaObjFromTableName($table);
if ($meta instanceof \LadLib\Common\Database\MetaOfTableInDb);
$m2 = $meta->getShowEditAllowFieldList(1);
$m1 = $meta->getShowIndexAllowFieldList(1);
$m3 = $meta->getShowGetOneAllowFieldList(1);
$mAllField = array_unique(array_merge($m1, $m2, $m3));

// Language settings
$arrayLanguage = clang1::getLanguageListKey(); //return ['vi', 'en', 'ja', 'fr' ... ]...
$metaModel = \App\Models\ModelMetaInfo::class;

// Load existing translations from DB
$translations = [];
$metaRecords = $metaModel::where('table_name_model', $table)->get();

foreach ($metaRecords as $metaRecord) {
    $field = $metaRecord->field;
    $trans = $metaRecord->translations;

    // If translations is string, decode it
    if (is_string($trans)) {
        $trans = json_decode($trans, true) ?: [];
    }

    // Organize by language
    foreach ($arrayLanguage as $lang) {
        if (!isset($translations[$lang])) {
            $translations[$lang] = [];
        }
        $translations[$lang][$field] = $trans[$lang] ?? '';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Language Field Editor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 5px;
            background: #f5f5f5;
        }

        .header {
            background: white;
            padding: 5px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .header h1 {
            margin: 10px 2px;
            font-size: 20px;
        }

        .controls {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }

        .controls label {
            font-weight: 600;
        }

        .controls select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            min-width: 200px;
        }

        .controls button {
            padding: 8px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }

        .controls button:hover {
            background: #45a049;
        }

        .controls button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-translate {
            background: #2196F3 !important;
        }

        .btn-translate:hover {
            background: #1976D2 !important;
        }

        .translating {
            opacity: 0.6;
            background: #FFE082 !important;
        }

        .info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #004085;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 13px;
        }

        /* Toast notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            min-width: 300px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.success {
            border-left: 4px solid #4CAF50;
        }

        .toast.error {
            border-left: 4px solid #f44336;
        }

        .toast.loading {
            border-left: 4px solid #2196F3;
        }

        .toast .icon {
            font-size: 24px;
        }

        .toast .content {
            flex: 1;
        }

        .toast .message {
            font-weight: 600;
            margin-bottom: 3px;
        }

        .toast .timestamp {
            font-size: 12px;
            color: #666;
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            background: #f8f9fa;
            padding: 12px 10px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        th.field-col {
            background: #e9ecef;
            width: 200px;
        }

        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }

        tr:hover {
            background: #f8f9fa;
        }

        input[type="text"] {
            width: 100%;
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.1);
        }

        .field-name {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #333;
        }

        .search-box {
            margin-bottom: 15px;
        }

        .search-box input {
            width: 300px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .stats {
            margin-top: 15px;
            color: #666;
            font-size: 13px;
        }

        .lang-badge {
            display: inline-block;
            padding: 2px 6px;
            background: #667eea;
            color: white;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="header" style="position: relative">
        <h1>🌍 Multi-Language Field Editor</h1>

        <form method="GET" style="margin-bottom: 10px;">
            <div class="controls">
                <label>Select Table:</label>
                <select name="table" onchange="this.form.submit()">
                    <?php foreach ($mTable as $tblName): ?>
                        <?php if (!\LadLib\Common\Database\MetaTableCommon::getModelFromTableName($tblName)) continue; ?>
                        <option value="<?= htmlspecialchars($tblName) ?>" <?= $tblName === $table ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tblName) ?>
                        </option>
                    <?php endforeach; ?>
                </select> <span style="color: #666; font-size: 13px;">
                Table: <strong><?= htmlspecialchars($table) ?></strong> |
                Fields: <strong><?= count($mAllField) ?></strong> |
                Languages: <strong><?= count($arrayLanguage) ?></strong>
            </span>
                <input style="max-width: 200px" type="text" id="searchBox" placeholder="🔍 Search fields..." onkeyup="filterTable()">
            </div>
        </form>

        <div class="controls" style="position: fixed; right: 10px; top: 5px; z-index: 100000">
            <button type="button" id="translateBtn" class="btn-translate" onclick="translateEmptyFields()">🌐 Translate Empty</button>
            <button type="button" id="retranslateBtn" class="btn-translate" onclick="retranslateAll()">🔄 Re-Translate All</button>
            <button type="button" id="saveBtn" onclick="saveChanges()">💾 Save All Changes</button>
        </div>
    </div>

    <div class="table-container">


        <table id="dataTable">
            <thead>
                <tr>
                    <th class="field-col">Field Name</th>
                    <th class="field-col">Default</th>
                    <?php foreach ($arrayLanguage as $lang): ?>
                        <th><?= strtoupper($lang) ?> <span class="lang-badge"><?= $lang ?></span></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mAllField as $field): ?>
                    <tr>
                        <td class="field-name"><?= htmlspecialchars($field) ?></td>
                        <td class="field-name default-vi"><?php

                            $orgDesc = $meta->getDescOfField($field);

                            echo htmlspecialchars("$orgDesc")

                            ?></td>
                        <?php foreach ($arrayLanguage as $lang): ?>
                            <td>
                                <input
                                    type="text"
                                    class="translation-input lang-<?= htmlspecialchars($lang) ?>"
                                    data-field="<?= htmlspecialchars($field) ?>"
                                    data-lang="<?= htmlspecialchars($lang) ?>"
                                    value="<?= htmlspecialchars($translations[$lang][$field] ?? '') ?>"
                                    placeholder="Enter <?= $lang ?> translation..."
                                >
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="stats">
            Total fields: <span id="totalCount"><?= count($mAllField) ?></span> |
            Showing: <span id="visibleCount"><?= count($mAllField) ?></span>
        </div>
    </div>

    <script>
        // Toast notification function
        function showToast(message, type = 'success', timestamp = '') {
            const existingToast = document.querySelector('.toast');
            if (existingToast) {
                existingToast.remove();
            }

            const icons = {
                success: '✅',
                error: '❌',
                loading: '⏳'
            };

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div class="icon">${icons[type]}</div>
                <div class="content">
                    <div class="message">${message}</div>
                    ${timestamp ? `<div class="timestamp">${timestamp}</div>` : ''}
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => toast.classList.add('show'), 10);

            if (type !== 'loading') {
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            return toast;
        }

        // Google Translate function
        async function translateText(text, targetLang) {
            // Using Google Translate API (free, no API key needed)
            const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=${targetLang}&dt=t&q=${encodeURIComponent(text)}`;

            try {
                const response = await fetch(url);
                const data = await response.json();

                // Parse response - format: [[["translated","original",null,null,3]]]
                if (data && data[0] && data[0][0] && data[0][0][0]) {
                    return data[0][0][0];
                }
                return text;
            } catch (error) {
                console.error('Translation error:', error);
                return text;
            }
        }

        // Core translate function (reusable)
        async function performTranslation(onlyEmpty = true) {
            const translateBtn = document.getElementById('translateBtn');
            const retranslateBtn = document.getElementById('retranslateBtn');
            const saveBtn = document.getElementById('saveBtn');

            translateBtn.disabled = true;
            retranslateBtn.disabled = true;
            saveBtn.disabled = true;

            const message = onlyEmpty ? 'Translating empty fields...' : 'Re-translating all fields...';
            const loadingToast = showToast(message, 'loading');

            const rows = document.querySelectorAll('#dataTable tbody tr');
            let translatedCount = 0;
            let skippedCount = 0;

            for (const row of rows) {
                const fieldNameCell = row.querySelector('.field-name');
                const defaultViCell = row.querySelector('.default-vi');
                const inputs = row.querySelectorAll('.translation-input');

                if (!defaultViCell) continue;

                const viText = defaultViCell.textContent.trim();
                const fieldName = fieldNameCell ? fieldNameCell.textContent.trim() : '';

                // Skip if default-vi text equals fieldname (case insensitive)
                if (viText.toLowerCase() === fieldName.toLowerCase()) {
                    skippedCount++;
                    continue;
                }

                // Skip if empty
                if (!viText) {
                    skippedCount++;
                    continue;
                }

                // Translate inputs
                for (const input of inputs) {
                    // Skip if onlyEmpty=true and input has value
                    if (onlyEmpty && input.value.trim() !== '') {
                        continue;
                    }

                    const lang = input.dataset.lang;

                    // Map language codes for Google Translate API (sync with PHP)
                    const langMap = <?= json_encode(array_combine($arrayLanguage, $arrayLanguage)) ?>;

                    const targetLang = langMap[lang] || lang;

                    // Add visual feedback
                    input.classList.add('translating');

                    try {
                        const translated = await translateText(viText, targetLang);
                        input.value = translated;
                        translatedCount++;

                        // Mark as changed
                        changed = true;
                    } catch (error) {
                        console.error(`Error translating to ${lang}:`, error);
                    } finally {
                        input.classList.remove('translating');
                    }

                    // Small delay to avoid rate limiting
                    await new Promise(resolve => setTimeout(resolve, 100));
                }
            }

            loadingToast.remove();
            translateBtn.disabled = false;
            retranslateBtn.disabled = false;
            saveBtn.disabled = false;

            if (translatedCount > 0) {
                showToast(`✅ Translated ${translatedCount} fields! (Skipped ${skippedCount})`, 'success');
            } else {
                showToast(`ℹ️ No fields to translate (Skipped ${skippedCount})`, 'success');
            }
        }

        // Translate only empty fields
        async function translateEmptyFields() {
            await performTranslation(true);
        }

        // Re-translate all fields (with confirmation)
        async function retranslateAll() {
            const rows = document.querySelectorAll('#dataTable tbody tr');
            let totalFields = 0;

            // Count how many fields will be translated
            rows.forEach(row => {
                const defaultViCell = row.querySelector('.default-vi');
                const fieldNameCell = row.querySelector('.field-name');
                const inputs = row.querySelectorAll('.translation-input');

                if (!defaultViCell) return;

                const viText = defaultViCell.textContent.trim();
                const fieldName = fieldNameCell ? fieldNameCell.textContent.trim() : '';

                // Skip invalid
                if (!viText || viText.toLowerCase() === fieldName.toLowerCase()) return;

                totalFields += inputs.length;
            });

            if (totalFields === 0) {
                showToast('ℹ️ No fields to translate', 'error');
                return;
            }

            // Confirm dialog
            const confirmed = confirm(
                `⚠️ This will RE-TRANSLATE ${totalFields} fields (including non-empty ones).\n\n` +
                `All existing translations will be overwritten!\n\n` +
                `Continue?`
            );

            if (!confirmed) {
                showToast('❌ Re-translation cancelled', 'error');
                return;
            }

            await performTranslation(false);
        }

        // Save changes via AJAX
        function saveChanges() {
            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = true;

            const loadingToast = showToast('Saving changes...', 'loading');

            // Collect all translation data
            const data = {};
            document.querySelectorAll('.translation-input').forEach(input => {
                const field = input.dataset.field;
                const lang = input.dataset.lang;

                if (!data[field]) {
                    data[field] = {};
                }
                data[field][lang] = input.value;
            });

            const formData = new FormData();
            formData.append('action', 'save');
            formData.append('table', '<?= htmlspecialchars($table) ?>');
            formData.append('languages', JSON.stringify(<?= json_encode($arrayLanguage) ?>));
            formData.append('data', JSON.stringify(data));

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers.get('content-type'));

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        throw new Error('Server returned non-JSON response. Check console for details.');
                    });
                }

                return response.json();
            })
            .then(result => {
                console.log('Result:', result);
                loadingToast.remove();
                saveBtn.disabled = false;

                if (result.success) {
                    showToast(result.message, 'success', result.timestamp);
                    changed = false; // Reset change tracking
                } else {
                    showToast(result.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingToast.remove();
                saveBtn.disabled = false;
                showToast('Error: ' + error.message, 'error');
            });
        }

        // Keyboard shortcut: Ctrl+S to save
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveChanges();
            }
        });

        function filterTable() {
            const search = document.getElementById('searchBox').value.toLowerCase();
            const table = document.getElementById('dataTable');
            const rows = table.getElementsByTagName('tr');
            let visibleCount = 0;

            for (let i = 1; i < rows.length; i++) {
                const fieldName = rows[i].cells[0].textContent.toLowerCase();
                const inputs = rows[i].getElementsByTagName('input');
                let hasMatch = fieldName.includes(search);

                // Also search in translation values
                for (let input of inputs) {
                    if (input.value.toLowerCase().includes(search)) {
                        hasMatch = true;
                        break;
                    }
                }

                if (hasMatch) {
                    rows[i].style.display = '';
                    visibleCount++;
                } else {
                    rows[i].style.display = 'none';
                }
            }

            document.getElementById('visibleCount').textContent = visibleCount;
        }

        // Warn on unsaved changes
        let changed = false;
        document.querySelectorAll('input[type="text"]').forEach(el => {
            el.addEventListener('input', () => changed = true);
        });

        window.addEventListener('beforeunload', (e) => {
            if (changed) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes!';
            }
        });
    </script>
</body>
</html>
