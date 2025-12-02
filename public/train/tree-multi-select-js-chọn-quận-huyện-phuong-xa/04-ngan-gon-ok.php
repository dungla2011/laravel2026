<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chọn Phân Cấp</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .select-container {
            margin-bottom: 20px;
        }
        .select-container__label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .select-container__select {
            width: 300px;
            padding: 8px;
            font-size: 16px;
        }
        .choices__inner {
            min-height: auto;
        }
    </style>
</head>
<body>

<h1>Chọn Phân Cấp</h1>

<div class="select-container">
    <label for="level1" class="select-container__label">Cấp 1:</label>
    <select id="level1" class="select-container__select" placeholder="Chọn Cấp 1">
        <option value="">-- Chọn Cấp 1 --</option>
    </select>
</div>

<div class="select-container">
    <label for="level2" class="select-container__label">Cấp 2:</label>
    <select id="level2" class="select-container__select" placeholder="Chọn Cấp 2" disabled>
        <option value="">-- Chọn Cấp 2 --</option>
    </select>
</div>

<div class="select-container">
    <label for="level3" class="select-container__label">Cấp 3:</label>
    <select id="level3" class="select-container__select" placeholder="Chọn Cấp 3" disabled>
        <option value="">-- Chọn Cấp 3 --</option>
    </select>
</div>

<div class="select-container">
    <label for="level4" class="select-container__label">Cấp 4:</label>
    <select id="level4" class="select-container__select" placeholder="Chọn Cấp 4" disabled>
        <option value="">-- Chọn Cấp 4 --</option>
    </select>
</div>

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // API base URL
        const API_BASE_URL = 'https://mytree.vn/api/don-vi-hanh-chinh/tree';

        // Cache để lưu trữ dữ liệu đã fetch
        const cache = {};

        // Bản đồ từ ID đến has_child
        const idToHasChild = {};

        // Hàm lấy dữ liệu từ API
        const fetchData = async (pid = 0) => {
            if (cache[pid]) {
                console.log(`Fetching data for pid=${pid} from cache`);
                return cache[pid];
            }

            try {
                const url = `${API_BASE_URL}?pid=${pid}`;

                console.log(`Fetching data from: ${url}`);
                if(pid === ''){
                    console.log("PID Empty, so return Empty data");
                    return [];
                }

                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();
                if (result.code !== 1) {
                    throw new Error(`API error! code: ${result.code}`);
                }
                console.log(`Data fetched for pid=${pid}:`, result.payload);
                cache[pid] = result.payload;
                result.payload.forEach(item => {
                    idToHasChild[item.id] = item.has_child;
                });
                return result.payload;
            } catch (error) {
                console.error('Error fetching data:', error);
                return [];
            }
        };

        // Hàm populate select
        const populateSelect = async (choicesInstance, pid = 0, level = 1) => {
            const defaultLabel = `-- Chọn Cấp ${level} --`;
            choicesInstance.clearChoices();
            choicesInstance.setChoices([{
                value: '',
                label: 'Đang tải...',
            }], 'value', 'label', false);
            choicesInstance.disable();

            const data = await fetchData(pid);
            choicesInstance.clearChoices();
            choicesInstance.setChoices([{
                value: '',
                label: defaultLabel,
                selected: true
            }], 'value', 'label', false);
            choicesInstance.setValue(['']);

            if (!Array.isArray(data)) {
                console.error(`Data for pid=${pid} is not an array:`, data);
                return;
            }

            if (data.length === 0) {
                console.log(`No data found for pid=${pid}`);
                return;
            }

            const choices = data.map(item => ({
                value: String(item.id),
                label: item.name
            }));
            console.log(`Populating select with pid=${pid}:`, choices);
            choicesInstance.setChoices(choices, 'value', 'label', true);

            if (choices.length > 0) {
                choicesInstance.enable();
            } else {
                choicesInstance.disable();
            }
        };

        // Hàm reset các select cấp dưới
        const resetLowerSelects = (levels) => {
            levels.forEach(level => {
                level.clearChoices();
                level.disable();
                const levelNumber = level.passedElement.element.id.replace('level', '');
                level.setChoices([{
                    value: '',
                    label: `-- Chọn Cấp ${levelNumber} --`,
                }], 'value', 'label', false);
                level.setValue(['']);
            });
        };

        // --- Bắt đầu phần code được tối ưu hóa ---

        // Lấy tất cả các select element
        const selectElements = document.querySelectorAll('select[id^="level"]');
        const selects = Array.from(selectElements).map(select => new Choices(select, {
            searchEnabled: true,
            placeholder: true,
            shouldSort: false
        }));

        // Hàm xử lý sự kiện change cho tất cả các cấp
        const handleLevelChange = async (level) => {
            const currentSelect = selects[level - 1];
            const currentId = currentSelect.getValue(true);
            console.log(`Cấp ${level} được chọn:`, currentId);

            // Reset các select cấp dưới
            resetLowerSelects(selects.slice(level));

            if (currentId) {
                // Populate select cấp tiếp theo
                await populateSelect(selects[level], currentId, level + 1);

                // Tự động populate các cấp con (nếu cần)
                let nextLevel = level + 1;
                while (nextLevel < selects.length) {
                    const nextId = selects[nextLevel - 1].getValue(true);
                    await populateSelect(selects[nextLevel], nextId, nextLevel + 1);
                    nextLevel++;
                }
            }
        };

        // Gắn sự kiện change cho từng select
        selects.forEach((select, index) => {
            const level = index + 1;
            select.passedElement.element.addEventListener('change', () => handleLevelChange(level));
        });

        // Tải dữ liệu cho Cấp 1 khi tải trang
        populateSelect(selects[0], 0, 1);
    });
</script>
</body>
</html>
