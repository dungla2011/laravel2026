<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start(); // Start output buffering

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

ob_end_clean(); // Clear any Laravel output

if(!isSupperAdmin_()){
    die("NOT ADMINS");
}

// Handle AJAX save request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    while (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: application/json');

    try {
        $data = json_decode($_POST['data'] ?? '{}', true) ?? [];
        $menuModel = \App\Models\MenuTree::class;
        $updated = 0;

        // Update each menu's translations column in DB
        foreach ($data as $menuId => $translations) {
            $menu = $menuModel::find($menuId);
            if ($menu) {
                $menu->translations = $translations;
                $menu->save();
                $updated++;
            }
        }

        echo json_encode([
            'success' => true,
            'message' => "Saved {$updated} menu translations successfully!",
            'timestamp' => date('H:i:s')
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Get menu data
$menuModel = \App\Models\MenuTree::class;
$allMenus = $menuModel::orderBy('orders', 'desc')->get()->toArray();

// Get root menus (parent_id = 0)
$rootMenus = array_values(array_filter($allMenus, function($menu) {
    return $menu['parent_id'] == 0;
}));

// Function to get all descendants recursively
function getAllDescendants($menus, $parentId) {
    $descendants = [];
    foreach ($menus as $menu) {
        if ($menu['parent_id'] == $parentId) {
            $descendants[] = $menu;
            // Get children of this menu
            $children = getAllDescendants($menus, $menu['id']);
            $descendants = array_merge($descendants, $children);
        }
    }
    return $descendants;
}

// Count children recursively
function countChildren($menus, $parentId) {
    $count = 0;
    foreach ($menus as $menu) {
        if ($menu['parent_id'] == $parentId) {
            $count++;
            $count += countChildren($menus, $menu['id']);
        }
    }
    return $count;
}

// Load existing translations from DB
$translations = [];
foreach ($allMenus as $menu) {
    if (!empty($menu['translations'])) {
        // If translations is already array (from toArray())
        $translations[$menu['id']] = is_string($menu['translations'])
            ? json_decode($menu['translations'], true)
            : $menu['translations'];
    }
}

// Language list from clang1
$languageNames = clang1::getLanguageList();
$languages = clang1::getLanguageListKey();

// Ensure data is valid for JSON encoding
if (empty($allMenus)) $allMenus = [];
if (empty($rootMenus)) $rootMenus = [];
if (empty($translations)) $translations = [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Translation Editor</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 5px;
            background: #f5f5f5;
        }

        .header {
            background: white;
            padding: 5px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            color: #333;
        }

        .header .controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
        }

        .btn-secondary {
            background: #2196F3;
            color: white;
        }

        .btn-secondary:hover {
            background: #1976D2;
        }

        .table-container {
            background: white;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: auto;
            max-height: calc(100vh - 200px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        th.col-id {
            width: 60px;
            background: #e9ecef;
        }

        th.col-name {
            width: 250px;
            background: #e9ecef;
        }

        th.col-lang {
            width: 180px;
        }

        td.col-id {
            background: #f8f9fa;
            font-weight: 600;
            text-align: center;
        }

        td.col-name {
            background: #fff9e6;
            font-weight: 600;
        }

        .menu-tabs {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .menu-tab {
            padding: 10px 20px;
            border: 2px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            background: white;
            transition: all 0.3s;
            font-weight: 600;
            font-size: 14px;
        }

        .menu-tab:hover {
            background: #f0f0f0;
            border-color: #2196F3;
        }

        .menu-tab.active {
            background: #2196F3;
            color: white;
            border-color: #2196F3;
        }

        .menu-tab .count {
            display: inline-block;
            background: rgba(0,0,0,0.1);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 8px;
        }

        .menu-tab.active .count {
            background: rgba(255,255,255,0.3);
        }

        .menu-link {
            display: block;
            color: #666;
            font-size: 12px;
            font-weight: normal;
            margin-top: 4px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }

        input[type="text"] {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
            font-family: inherit;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.1);
        }

        tr:hover {
            background: #f8f9fa;
        }

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

        .stats {
            margin-top: 10px;
            color: #666;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/menu_lang.php'; ?>
    
    <div class="header">
        <h1>🌍 Menu Translation Editor</h1>
        <div class="controls">
            <button class="btn btn-primary" onclick="saveTranslations()">💾 Save All</button>
            <button class="btn btn-secondary" onclick="autoTranslate()">🌐 Auto Translate Empty</button>
            <span class="stats">Root menus: <strong><?= count($rootMenus) ?></strong> | Total items: <strong><?= count($allMenus) ?></strong> | Languages: <strong><?= count($languages) ?></strong></span>
        </div>
    </div>

    <!-- Menu Tabs -->
    <div class="menu-tabs">
        <?php if (!empty($rootMenus)): ?>
            <?php foreach ($rootMenus as $index => $root):
                $childCount = countChildren($allMenus, $root['id']);
            ?>
                <button class="menu-tab <?= $index === 0 ? 'active' : '' ?>"
                        data-menu-id="<?= $root['id'] ?>"
                        onclick="loadMenuChildren(<?= $root['id'] ?>)">
                    <?= htmlspecialchars($root['name']) ?>
                    <span class="count"><?= $childCount ?></span>
                </button>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="color: #999; padding: 10px;">No root menus found (parent_id = 0)</div>
        <?php endif; ?>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-name">Original Name</th>
                    <?php foreach ($languages as $lang): ?>
                        <th class="col-lang"><?= $languageNames[$lang] ?> (<?= $lang ?>)</th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody id="menu-tbody">
                <!-- Will be populated by JavaScript -->
            </tbody>
        </table>
    </div>

    <script>
        // All menus data from PHP
        const allMenus = <?= json_encode($allMenus, JSON_UNESCAPED_UNICODE) ?>;
        const translations = <?= json_encode($translations, JSON_UNESCAPED_UNICODE) ?>;
        const languages = <?= json_encode($languages) ?>;
        const rootMenus = <?= json_encode($rootMenus, JSON_UNESCAPED_UNICODE) ?>;
        let changed = false;
        let currentRootId = rootMenus.length > 0 ? rootMenus[0].id : 0;

        // Load children of a root menu
        function loadMenuChildren(rootId) {
            currentRootId = rootId;

            // Update URL hash
            window.location.hash = 'menu=' + rootId;

            // Update active tab
            document.querySelectorAll('.menu-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`[data-menu-id="${rootId}"]`).classList.add('active');

            // Get all descendants
            const descendants = getAllDescendants(rootId);

            // Render table
            renderTable(descendants);
        }

        // Get all descendants recursively
        function getAllDescendants(parentId) {
            const result = [];
            allMenus.forEach(menu => {
                if (menu.parent_id == parentId) {
                    result.push(menu);
                    const children = getAllDescendants(menu.id);
                    result.push(...children);
                }
            });
            return result;
        }

        // Render table rows
        function renderTable(menus) {
            const tbody = document.getElementById('menu-tbody');

            if (menus.length === 0) {
                const totalCols = 2 + languages.length; // ID + Name + all languages (including en, vi)
                tbody.innerHTML = `<tr><td colspan="${totalCols}" class="no-data">No child menus found</td></tr>`;
                return;
            }

            let html = '';
            menus.forEach(menu => {
                html += '<tr>';
                html += `<td class="col-id">${menu.id}</td>`;
                html += `<td class="col-name" title="${escapeHtml(menu.name)}">`;
                html += `<span>${escapeHtml(menu.name)}</span>`;
                if (menu.link) {
                    html += `<span class="menu-link">${escapeHtml(menu.link)}</span>`;
                }
                html += '</td>';

                // Language columns - EN first, then others
                languages.forEach(lang => {
                    const value = translations[menu.id] && translations[menu.id][lang] ? translations[menu.id][lang] : '';
                    // Get EN translation as source for auto-translate
                    const enValue = translations[menu.id] && translations[menu.id]['en'] ? translations[menu.id]['en'] : menu.name;
                    
                    html += '<td>';
                    html += `<input type="text" class="translation-input" `;
                    html += `data-menu-id="${menu.id}" `;
                    html += `data-lang="${lang}" `;
                    html += `data-original="${escapeHtml(enValue)}" `;
                    html += `value="${escapeHtml(value)}" `;
                    if (lang === 'en') {
                        html += `style="border-color: #ffc107; background: #fffbf0;" `;
                    }
                    html += `placeholder="Enter ${lang} translation...">`;
                    html += '</td>';
                });

                html += '</tr>';
            });

            tbody.innerHTML = html;

            // Re-attach event listeners
            document.querySelectorAll('.translation-input').forEach(el => {
                el.addEventListener('input', () => changed = true);
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        // Load first menu on page load
        window.addEventListener('DOMContentLoaded', () => {
            // Check for hash in URL
            const hash = window.location.hash;
            let menuIdToLoad = currentRootId;
            
            if (hash && hash.startsWith('#menu=')) {
                const menuId = parseInt(hash.substring(6)); // Remove '#menu='
                
                // Check if menu exists in rootMenus
                const menuExists = rootMenus.find(m => m.id === menuId);
                if (menuExists) {
                    menuIdToLoad = menuId;
                }
            }
            
            if (menuIdToLoad > 0) {
                loadMenuChildren(menuIdToLoad);
            }
        });

        function showToast(message, type = 'success') {
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
                <div class="icon" style="font-size: 24px;">${icons[type]}</div>
                <div class="content">
                    <div class="message" style="font-weight: 600;">${message}</div>
                </div>
            `;

            document.body.appendChild(toast);            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);

            if (type !== 'loading') {
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            return toast;
        }

        async function translateText(text, targetLang) {
            // Use the language code directly (already from clang1)
            // Translate from English (en) to target language
            const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=${targetLang}&dt=t&q=${encodeURIComponent(text)}`;

            try {
                const response = await fetch(url);
                const data = await response.json();
                if (data && data[0] && data[0][0] && data[0][0][0]) {
                    return data[0][0][0];
                }
                return text;
            } catch (error) {
                console.error('Translation error:', error);
                return text;
            }
        }

        async function autoTranslate() {
            const loadingToast = showToast('Translating empty fields from English...', 'loading');
            const inputs = document.querySelectorAll('.translation-input');
            let translatedCount = 0;

            for (const input of inputs) {
                const targetLang = input.dataset.lang;
                
                // Skip English field - it's the source
                if (targetLang === 'en') {
                    continue;
                }
                
                if (input.value.trim() !== '') {
                    continue; // Skip non-empty fields
                }

                const originalText = input.dataset.original; // This is now EN text
                
                if (!originalText || originalText.trim() === '') continue;

                input.style.backgroundColor = '#FFE082';

                try {
                    const translated = await translateText(originalText, targetLang);
                    input.value = translated;
                    translatedCount++;
                    changed = true;
                } catch (error) {
                    console.error(`Error translating to ${targetLang}:`, error);
                } finally {
                    input.style.backgroundColor = '';
                }

                await new Promise(resolve => setTimeout(resolve, 100));
            }

            loadingToast.remove();

            if (translatedCount > 0) {
                showToast(`✅ Translated ${translatedCount} fields from English!`, 'success');
            } else {
                showToast('ℹ️ No empty fields to translate', 'success');
            }
        }

        function saveTranslations() {
            const loadingToast = showToast('Saving...', 'loading');

            const data = {};
            document.querySelectorAll('.translation-input').forEach(input => {
                const menuId = input.dataset.menuId;
                const lang = input.dataset.lang;

                if (!data[menuId]) {
                    data[menuId] = {};
                }
                data[menuId][lang] = input.value;
            });

            const formData = new FormData();
            formData.append('action', 'save');
            formData.append('data', JSON.stringify(data));

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                loadingToast.remove();

                if (result.success) {
                    showToast(result.message, 'success');
                    changed = false;
                } else {
                    showToast(result.message, 'error');
                }
            })
            .catch(error => {
                loadingToast.remove();
                showToast('Error: ' + error.message, 'error');
            });
        }

        // Track changes
        document.querySelectorAll('input[type="text"]').forEach(el => {
            el.addEventListener('input', () => changed = true);
        });

        // Keyboard shortcut: Ctrl+S to save
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveTranslations();
            }
        });

        // Warn on unsaved changes
        window.addEventListener('beforeunload', (e) => {
            if (changed) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes!';
            }
        });
    </script>
</body>
</html>
