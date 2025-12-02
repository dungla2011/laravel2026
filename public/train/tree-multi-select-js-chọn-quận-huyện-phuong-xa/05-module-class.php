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
        .select-multi-level-tree {
            margin-bottom: 20px;
        }
        .select-multi-level-tree__label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .select-multi-level-tree__select {
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

<?php
$labels = ['Chọn Tỉnh/Thành phố', 'Chọn Quận/Huyện', 'Chọn Phường/Xã', 'Chọn Khu vực'];
for ($i = 0; $i < count($labels); $i++) {
    $level = $i + 1;
    echo "<div class='select-multi-level-tree'>
            <label for='level$level' class='select-multi-level-tree__label'>{$labels[$i]}:</label>
            <select id='level$level' class='select-multi-level-tree__select' placeholder='{$labels[$i]}'" . ($i > 0 ? " disabled" : "") . ">
                <option value=''>-- {$labels[$i]} --</option>
            </select>
          </div>";
}
?>

<span id="selected-ids">Selected IDs: </span>

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    class TreeMultiSelect {
        constructor(apiBaseUrl, selectIds, labels) {
            this.apiBaseUrl = apiBaseUrl;
            this.cache = {};
            this.idToHasChild = {};
            this.labels = labels;
            this.selectedIds = {};
            this.selects = selectIds.map(id => new Choices(document.getElementById(id), {
                searchEnabled: true,
                placeholder: true,
                shouldSort: false
            }));
            this.init();
        }

        async fetchData(pid = 0) {
            if (this.cache[pid]) {
                return this.cache[pid];
            }

            try {
                const url = `${this.apiBaseUrl}?pid=${pid}`;
                if (pid === '') {
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
                this.cache[pid] = result.payload;
                result.payload.forEach(item => {
                    this.idToHasChild[item.id] = item.has_child;
                });
                return result.payload;
            } catch (error) {
                console.error('Error fetching data:', error);
                return [];
            }
        }

        async populateSelect(choicesInstance, pid = 0, level = 1) {
            const defaultLabel = this.labels[level - 1] || `-- Chọn Cấp ${level} --`;
            choicesInstance.clearChoices();
            choicesInstance.setChoices([{
                value: '',
                label: 'Đang tải...',
            }], 'value', 'label', false);
            choicesInstance.disable();

            const data = await this.fetchData(pid);
            choicesInstance.clearChoices();
            choicesInstance.setChoices([{
                value: '',
                label: defaultLabel,
                selected: true
            }], 'value', 'label', false);
            choicesInstance.setValue(['']);

            if (!Array.isArray(data)) {
                return;
            }

            if (data.length === 0) {
                return;
            }

            const choices = data.map(item => ({
                value: String(item.id),
                label: item.name
            }));
            choicesInstance.setChoices(choices, 'value', 'label', true);

            if (choices.length > 0) {
                choicesInstance.enable();
            } else {
                choicesInstance.disable();
            }
        }

        resetLowerSelects(levels) {
            levels.forEach(level => {
                level.clearChoices();
                level.disable();
                const levelNumber = level.passedElement.element.id.replace('level', '');
                const defaultLabel = this.labels[levelNumber - 1] || `-- Chọn Cấp ${levelNumber} --`;
                level.setChoices([{
                    value: '',
                    label: defaultLabel,
                }], 'value', 'label', false);
                level.setValue(['']);
            });
        }

        async handleLevelChange(level) {
            const currentSelect = this.selects[level - 1];
            const currentId = currentSelect.getValue(true);
            this.selectedIds[level] = currentId;

            this.resetLowerSelects(this.selects.slice(level));

            if (currentId) {
                await this.populateSelect(this.selects[level], currentId, level + 1);

                let nextLevel = level + 1;
                while (nextLevel < this.selects.length) {
                    const nextId = this.selects[nextLevel - 1].getValue(true);
                    await this.populateSelect(this.selects[nextLevel], nextId, nextLevel + 1);
                    nextLevel++;
                }
            }

            let str = "";
            // Update selectedIds with current values of all selects
            this.selects.forEach((select, index) => {
                const level = index + 1;
                this.selectedIds[level] = select.getValue(true);
                if(select.getValue(true))
                    str += `Cấp ${level} = ${this.selectedIds[level]} | `;
            });

            // Display selected IDs
            document.getElementById('selected-ids').textContent = 'Selected IDs: ' + str;
        }

        getSelectedIds() {
            return this.selectedIds;
        }

        init() {
            this.selects.forEach((select, index) => {
                const level = index + 1;
                select.passedElement.element.addEventListener('change', () => this.handleLevelChange(level));
            });

            this.populateSelect(this.selects[0], 0, 1);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const apiBaseUrl = 'https://mytree.vn/api/don-vi-hanh-chinh/tree';
        const selectIds = ['level1', 'level2', 'level3', 'level4'];
        const labels = <?php echo json_encode($labels); ?>;
        const treeMultiSelect = new TreeMultiSelect(apiBaseUrl, selectIds, labels);

        // Example usage of getSelectedIds
        console.log(treeMultiSelect.getSelectedIds());
    });
</script>
</body>
</html>
