<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Task</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="/adminlte/plugins/jquery/jquery.min.js"></script>

    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-color: #dee2e6;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        h1 {
            color: var(--dark-color);
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: var(--secondary-color);
        }

        .btn-success {
            background-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: #27ae60;
        }

        .btn-danger {
            background-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .task-tree {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .task-list {
            list-style-type: none;
        }

        .task-item {
            border: 1px solid var(--border-color);
            border-radius: 4px;
            margin-bottom: 10px;
            background-color: white;
            transition: all 0.3s;
        }

        .task-item:hover {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .task-item.dragging {
            opacity: 0.5;
            border: 2px dashed var(--primary-color);
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid var(--border-color);
        }

        .task-header:hover {
            background-color: #f9f9f9;
        }

        .task-title {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .toggle-icon {
            cursor: pointer;
            transition: transform 0.3s;
        }

        .collapsed .toggle-icon {
            transform: rotate(-90deg);
        }

        .task-actions {
            display: flex;
            gap: 10px;
        }

        .task-action {
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-size: 14px;
            transition: color 0.3s;
        }

        .task-action:hover {
            color: var(--primary-color);
        }

        .task-content {
            padding: 10px 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .subtasks {
            margin-left: 30px;
            padding-top: 10px;
        }

        .priority {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            color: white;
        }

        .priority-low {
            background-color: #3498db;
        }

        .priority-medium {
            background-color: #f39c12;
        }

        .priority-high {
            background-color: #e74c3c;
        }

        .priority-urgent {
            background-color: #c0392b;
        }

        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 5px;
        }

        .status-not-started {
            background-color: #ecf0f1;
            color: #7f8c8d;
        }

        .status-in-progress {
            background-color: #3498db;
            color: white;
        }

        .status-completed {
            background-color: #2ecc71;
            color: white;
        }

        .status-pending {
            background-color: #f39c12;
            color: white;
        }

        .status-canceled {
            background-color: #e74c3c;
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            border-radius: 5px;
            width: 500px;
            max-width: 90%;
            margin: 50px auto;
            animation: modalFade 0.3s;
        }

        @keyframes modalFade {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 18px;
            font-weight: bold;
        }

        .close-modal {
            cursor: pointer;
            font-size: 20px;
            color: #999;
        }

        .close-modal:hover {
            color: #333;
        }

        .modal-body {
            padding: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .modal-footer {
            padding: 15px;
            border-top: 1px solid var(--border-color);
            text-align: right;
        }

        .dropzone {
            border: 2px dashed #ccc;
            border-radius: 4px;
            padding: 10px;
            margin-top: 5px;
            transition: all 0.3s;
        }

        .dropzone.dragover {
            background-color: rgba(52, 152, 219, 0.1);
            border-color: var(--primary-color);
        }

        .task-detail-info {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .task-detail-info p {
            margin-bottom: 5px;
        }

        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        #dragGhost {
            position: absolute;
            z-index: 9999;
            pointer-events: none;
            opacity: 0.8;
            width: 300px;
            background: white;
            border: 1px solid var(--primary-color);
            border-radius: 4px;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .drop-indicator {
            height: 3px;
            background-color: var(--primary-color);
            margin: 5px 0;
            display: none;
        }

        .drop-indicator.active {
            display: block;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 4px solid var(--primary-color);
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Quản lý Task</h1>
        <button id="add-task-btn" class="btn btn-success">
            <i class="fas fa-plus"></i> Thêm Task
        </button>
    </header>

    <div id="alerts"></div>

    <div class="task-tree">
        <div id="loading" class="loading">
            <div class="spinner"></div>
        </div>
        <ul id="task-list" class="task-list"></ul>
    </div>
</div>

<!-- Modal Task -->
<div id="task-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="modal-title">Thêm Task</h2>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="task-form">
                <input type="hidden" id="task-id">
                <input type="hidden" id="parent-id">

                <div class="form-group">
                    <label for="title" class="form-label">Tiêu đề <span style="color: red">*</span></label>
                    <input type="text" id="title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea id="description" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select id="status" class="form-control">
                        <option value="not_started">Chưa bắt đầu</option>
                        <option value="in_progress">Đang thực hiện</option>
                        <option value="completed">Hoàn thành</option>
                        <option value="pending">Đang chờ</option>
                        <option value="canceled">Đã hủy</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority" class="form-label">Mức độ ưu tiên</label>
                    <select id="priority" class="form-control">
                        <option value="low">Thấp</option>
                        <option value="medium" selected>Trung bình</option>
                        <option value="high">Cao</option>
                        <option value="urgent">Khẩn cấp</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="due-date" class="form-label">Hạn chót</label>
                    <input type="date" id="due-date" class="form-control">
                </div>

                <div class="form-group">
                    <label for="assigned-to" class="form-label">Giao cho</label>
                    <select id="assigned-to" class="form-control">
                        <option value="">-- Chọn người thực hiện --</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" id="cancel-task">Hủy</button>
            <button type="button" class="btn btn-success" id="save-task">Lưu</button>
        </div>
    </div>
</div>

<!-- Modal Task Detail -->
<div id="task-detail-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="detail-title">Chi tiết Task</h2>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div id="task-detail-content"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" id="close-detail">Đóng</button>
            <button type="button" class="btn" id="edit-task">Sửa</button>
        </div>
    </div>
</div>

<!-- Drag Ghost Element -->
<div id="dragGhost" style="display: none;"></div>

<script>
    // Base API URL
    const API_URL = '/api';

    // Utility Functions
    const $ = selector => document.querySelector(selector);
    const $$ = selector => document.querySelectorAll(selector);

    const showAlert = (message, type = 'success') => {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;

        $('#alerts').appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    };

    const showLoading = () => {
        $('#loading').style.display = 'flex';
    };

    const hideLoading = () => {
        $('#loading').style.display = 'none';
    };

    // Task Management Functions
    let taskData = [];
    let users = [];

    const fetchTasks = async () => {
        showLoading();
        try {
            const response = await fetch(`${API_URL}/task-info/list`);
            if (!response.ok) throw new Error('Failed to fetch tasks');

            var data = await response.json();

            data = data.payload

            console.log(" Datax ", data);

            taskData = data.data || [];
            renderTaskTree(taskData);
        } catch (error) {
            console.error('Error fetching tasks:', error);
            showAlert('Không thể tải danh sách task. Vui lòng thử lại sau.', 'danger');
        } finally {
            hideLoading();
        }
    };

    const fetchUsers = async () => {
        try {
            // Replace with your actual user API
            const response = await fetch(`${API_URL}/user/list`);
            if (!response.ok) throw new Error('Failed to fetch users');

            var data = await response.json();
            data = data.payload
            console.log(" Datax ", data);

            users = data.data || [];

            // Populate assigned-to dropdown
            const select = $('#assigned-to');
            select.innerHTML = '<option value="">-- Chọn người thực hiện --</option>';

            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.email;
                select.appendChild(option);
            });

        } catch (error) {
            console.error('Error fetching users:', error);
        }
    };

    // Modify the createTask function to handle new task placement
    const createTask = async (taskData) => {
        try {
            const response = await fetch(`${API_URL}/task-info/add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(taskData)
            });

            if (!response.ok) throw new Error('Failed to create task');

            const result = await response.json();
            showAlert('Tạo task thành công!');

            // Return the created task data
            return result.data;
        } catch (error) {
            console.error('Error creating task:', error);
            showAlert('Không thể tạo task. Vui lòng thử lại.', 'danger');
            return null;
        }
    };

    const updateTask = async (id, taskData) => {
        try {
            const response = await fetch(`${API_URL}/task-info/update/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(taskData)
            });

            if (!response.ok) throw new Error('Failed to update task');

            const result = await response.json();
            showAlert('Cập nhật task thành công!');
            return result.data;
        } catch (error) {
            console.error('Error updating task:', error);
            showAlert('Không thể cập nhật task. Vui lòng thử lại.', 'danger');
            return null;
        }
    };

    const deleteTask = async (id) => {
        if (!confirm('Bạn có chắc chắn muốn xóa task này?')) return false;

        try {
            const response = await fetch(`${API_URL}/task-info/delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            });

            if (!response.ok) throw new Error('Failed to delete task');

            showAlert('Xóa task thành công!');
            return true;
        } catch (error) {
            console.error('Error deleting task:', error);
            showAlert('Không thể xóa task. Vui lòng thử lại.', 'danger');
            return false;
        }
    };

    const moveTask = async (taskId, parentId, beforeId = null) => {
        try {
            const payload = {
                parent_id: parentId || null
            };

            if (beforeId) {
                // payload.before_id = beforeId;
            }

            const response = await fetch(`${API_URL}/task-info/update/${taskId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) throw new Error('Failed to move task');

            showAlert('Di chuyển task thành công!');
            return true;
        } catch (error) {
            console.error('Error moving task:', error);
            showAlert('Không thể di chuyển task. Vui lòng thử lại.', 'danger');
            return false;
        }
    };

    // UI Rendering Functions
    const renderTaskTree = (tasks, parentElement = $('#task-list')) => {
        parentElement.innerHTML = '';

        if (tasks.length === 0) {
            parentElement.innerHTML = '<p>Chưa có task nào.</p>';
            return;
        }

        tasks.forEach(task => {
            const taskItem = document.createElement('li');
            taskItem.className = 'task-item';
            taskItem.dataset.id = task.id;
            taskItem.draggable = true;

            const taskHeader = document.createElement('div');
            taskHeader.className = 'task-header';

            const taskTitle = document.createElement('div');
            taskTitle.className = 'task-title';

            // Toggle icon for subtasks
            const toggleIcon = document.createElement('i');
            toggleIcon.className = `fas fa-caret-down toggle-icon ${task.children && task.children.length ? '' : 'hidden'}`;
            toggleIcon.onclick = (e) => {
                e.stopPropagation();
                const subTasks = taskItem.querySelector('.subtasks');
                if (subTasks) {
                    taskItem.classList.toggle('collapsed');
                    subTasks.style.display = subTasks.style.display === 'none' ? 'block' : 'none';
                }
            };

            const titleText = document.createElement('span');
            titleText.textContent = task.title;

            const prioritySpan = document.createElement('span');
            prioritySpan.className = `priority priority-${task.priority}`;
            prioritySpan.textContent = {
                'low': 'Thấp',
                'medium': 'TB',
                'high': 'Cao',
                'urgent': 'Khẩn'
            }[task.priority] || 'TB';

            const statusSpan = document.createElement('span');
            statusSpan.className = `status status-${task.status}`;
            statusSpan.textContent = {
                'not_started': 'Chưa bắt đầu',
                'in_progress': 'Đang làm',
                'completed': 'Hoàn thành',
                'pending': 'Đang chờ',
                'canceled': 'Đã hủy'
            }[task.status] || 'Chưa bắt đầu';

            taskTitle.appendChild(toggleIcon);
            taskTitle.appendChild(titleText);
            taskTitle.appendChild(prioritySpan);
            taskTitle.appendChild(statusSpan);

            const taskActions = document.createElement('div');
            taskActions.className = 'task-actions';

            // View button
            const viewBtn = document.createElement('button');
            viewBtn.className = 'task-action';
            viewBtn.innerHTML = '<i class="fas fa-eye"></i>';
            viewBtn.title = 'Xem chi tiết';
            viewBtn.onclick = (e) => {
                e.stopPropagation();
                showTaskDetail(task.id);
            };

            // Add subtask button
            const addSubtaskBtn = document.createElement('button');
            addSubtaskBtn.className = 'task-action';
            addSubtaskBtn.innerHTML = '<i class="fas fa-plus"></i>';
            addSubtaskBtn.title = 'Thêm task con';
            addSubtaskBtn.onclick = (e) => {
                e.stopPropagation();
                showTaskModal(null, task.id);
            };

            // Edit button
            const editBtn = document.createElement('button');
            editBtn.className = 'task-action';
            editBtn.innerHTML = '<i class="fas fa-edit"></i>';
            editBtn.title = 'Sửa';
            editBtn.onclick = (e) => {
                e.stopPropagation();
                showTaskModal(task.id);
            };

            // Delete button
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'task-action';
            deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
            deleteBtn.title = 'Xóa';
            deleteBtn.onclick = async (e) => {
                e.stopPropagation();
                if (await deleteTask(task.id)) {
                    taskItem.remove();
                }
            };

            taskActions.appendChild(viewBtn);
            taskActions.appendChild(addSubtaskBtn);
            taskActions.appendChild(editBtn);
            taskActions.appendChild(deleteBtn);

            taskHeader.appendChild(taskTitle);
            taskHeader.appendChild(taskActions);

            taskItem.appendChild(taskHeader);

            // If task has children, render them recursively
            if (task.children && task.children.length > 0) {
                const subTasks = document.createElement('div');
                subTasks.className = 'subtasks';

                const subTaskList = document.createElement('ul');
                subTaskList.className = 'task-list';

                renderTaskTree(task.children, subTaskList);

                subTasks.appendChild(subTaskList);
                taskItem.appendChild(subTasks);
            }

            // Set up drag and drop
            setupDragAndDrop(taskItem);

            parentElement.appendChild(taskItem);

            // Add drop indicator
            const dropIndicator = document.createElement('div');
            dropIndicator.className = 'drop-indicator';
            parentElement.appendChild(dropIndicator);
        });
    };

    const setupDragAndDrop = (taskItem) => {
        taskItem.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', taskItem.dataset.id);
            taskItem.classList.add('dragging');

            // Create ghost element
            const ghost = $('#dragGhost');
            ghost.textContent = taskItem.querySelector('.task-title span').textContent;
            ghost.style.display = 'block';

            e.dataTransfer.setDragImage(ghost, 0, 0);

            setTimeout(() => {
                ghost.style.display = 'none';
            }, 0);
        });

        taskItem.addEventListener('dragend', () => {
            taskItem.classList.remove('dragging');
            $$('.drop-indicator').forEach(indicator => {
                indicator.classList.remove('active');
            });
        });

        taskItem.addEventListener('dragover', (e) => {
            e.preventDefault();

            const draggingId = e.dataTransfer.getData('text/plain');
            if (draggingId === taskItem.dataset.id) return;

            const rect = taskItem.getBoundingClientRect();
            const y = e.clientY - rect.top;
            const height = rect.height;

            // Remove all active indicators
            $$('.drop-indicator').forEach(indicator => {
                indicator.classList.remove('active');
            });

            let dropIndicator;

            if (y < height / 3) {
                // Drop before this task
                dropIndicator = taskItem.previousElementSibling;
                if (dropIndicator && dropIndicator.classList.contains('drop-indicator')) {
                    dropIndicator.classList.add('active');
                }
            } else if (y > height * 2/3) {
                // Drop after this task
                dropIndicator = taskItem.nextElementSibling;
                if (dropIndicator && dropIndicator.classList.contains('drop-indicator')) {
                    dropIndicator.classList.add('active');
                }
            } else {
                // Drop as a child of this task
                taskItem.classList.add('drop-target');
            }
        });

        taskItem.addEventListener('dragleave', () => {
            taskItem.classList.remove('drop-target');
        });

        taskItem.addEventListener('drop', async (e) => {
            e.preventDefault();
            taskItem.classList.remove('drop-target');

            const taskId = e.dataTransfer.getData('text/plain');
            if (taskId === taskItem.dataset.id) return;

            const rect = taskItem.getBoundingClientRect();
            const y = e.clientY - rect.top;
            const height = rect.height;

            let parentId = null;
            let beforeId = null;

            if (y < height / 3) {
                // Drop before this task
                parentId = taskItem.parentElement.closest('.task-item')?.dataset.id || null;
                beforeId = taskItem.dataset.id;
            } else if (y > height * 2/3) {
                // Drop after this task
                parentId = taskItem.parentElement.closest('.task-item')?.dataset.id || null;

                // Find next sibling that is a task-item
                let nextTask = taskItem.nextElementSibling;
                while (nextTask && !nextTask.classList.contains('task-item')) {
                    nextTask = nextTask.nextElementSibling;
                }

                beforeId = nextTask?.dataset.id || null;
            } else {
                // Drop as a child of this task
                parentId = taskItem.dataset.id;
            }

            const success = await moveTask(taskId, parentId, beforeId);
            if (success) {
                await fetchTasks(); // Refresh the task tree
            }
        });
    };

    const showTaskDetail = async (taskId) => {
        showLoading();
        try {
            const response = await fetch(`${API_URL}/task-info/get/${taskId}`);
            if (!response.ok) throw new Error('Failed to fetch task details');

            const result = await response.json();
            const task = result.data;

            if (!task) throw new Error('Task not found');

            $('#detail-title').textContent = `Chi tiết Task: ${task.title}`;

            const content = $('#task-detail-content');
            content.innerHTML = `
                    <div class="task-detail-info">
                        <p><strong>Tiêu đề:</strong> ${task.title}</p>
                        <p><strong>Mô tả:</strong> ${task.description || 'Không có mô tả'}</p>
                        <p><strong>Trạng thái:</strong> <span class="status status-${task.status}">
                            ${
                {
                    'not_started': 'Chưa bắt đầu',
                    'in_progress': 'Đang làm',
                    'completed': 'Hoàn thành',
                    'pending': 'Đang chờ',
                    'canceled': 'Đã hủy'
                }[task.status] || 'Chưa bắt đầu'
            }
                        </span></p>
                        <p><strong>Ưu tiên:</strong> <span class="priority priority-${task.priority}">
                            ${
                {
                    'low': 'Thấp',
                    'medium': 'Trung bình',
                    'high': 'Cao',
                    'urgent': 'Khẩn cấp'
                }[task.priority] || 'Trung bình'
            }
                        </span></p>
                        <p><strong>Hạn chót:</strong> ${task.due_date || 'Không có hạn chót'}</p>
                        <p><strong>Người tạo:</strong> ${task.user_id ? getUserName(task.user_id) : 'Không xác định'}</p>
                        <p><strong>Giao cho:</strong> ${task.assigned_to ? getUserName(task.assigned_to) : 'Chưa giao'}</p>
                        <p><strong>Ngày tạo:</strong> ${formatDate(task.created_at)}</p>
                        <p><strong>Cập nhật lần cuối:</strong> ${formatDate(task.updated_at)}</p>
                    </div>
                `;

            $('#task-detail-modal').style.display = 'block';

            // Set up edit button
            $('#edit-task').onclick = () => {
                $('#task-detail-modal').style.display = 'none';
                showTaskModal(taskId);
            };

        } catch (error) {
            console.error('Error fetching task details:', error);
            showAlert('Không thể tải chi tiết task. Vui lòng thử lại sau.', 'danger');
        } finally {
            hideLoading();
        }
    };

    const showTaskModal = async (taskId = null, parentId = null) => {
        // Reset form
        $('#task-form').reset();

        // Update modal title
        $('#modal-title').textContent = taskId ? 'Cập nhật Task' : 'Thêm Task';

        // Set parent ID if provided
        if (parentId) {
            $('#parent-id').value = parentId;
        } else {
            $('#parent-id').value = '';
        }

        if (taskId) {
            // Edit mode - fetch task details
            showLoading();
            try {
                const response = await fetch(`${API_URL}/task-info/get/${taskId}`);
                if (!response.ok) throw new Error('Failed to fetch task details');

                const result = await response.json();
                const task = result.data;

                if (!task) throw new Error('Task not found');

                // Fill the form with task data
                $('#task-id').value = task.id;
                $('#title').value = task.title;
                $('#description').value = task.description || '';
                $('#status').value = task.status;
                $('#priority').value = task.priority;
                $('#due-date').value = task.due_date || '';
                $('#assigned-to').value = task.assigned_to || '';

            } catch (error) {
                console.error('Error fetching task details:', error);
                showAlert('Không thể tải chi tiết task. Vui lòng thử lại sau.', 'danger');
                return;
            } finally {
                hideLoading();
            }
        } else {
            // Add mode - clear task ID
            $('#task-id').value = '';
        }

        // Show the modal
        $('#task-modal').style.display = 'block';
    };

    // Helper Functions
    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';

        const date = new Date(dateString);
        return date.toLocaleString('vi-VN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const getUserName = (userId) => {
        const user = users.find(u => u.id == userId);
        return user ? user.name : `User ID ${userId}`;
    };

    // Helper function to create a task element
    const createTaskElement = (task) => {
        const taskItem = document.createElement('li');
        taskItem.className = 'task-item';
        taskItem.dataset.id = task.id;
        taskItem.draggable = true;

        const taskHeader = document.createElement('div');
        taskHeader.className = 'task-header';

        const taskTitle = document.createElement('div');
        taskTitle.className = 'task-title';

        // Toggle icon for subtasks
        const toggleIcon = document.createElement('i');
        toggleIcon.className = 'fas fa-caret-down toggle-icon hidden';
        toggleIcon.onclick = (e) => {
            e.stopPropagation();
            const subTasks = taskItem.querySelector('.subtasks');
            if (subTasks) {
                taskItem.classList.toggle('collapsed');
                subTasks.style.display = subTasks.style.display === 'none' ? 'block' : 'none';
            }
        };

        const titleText = document.createElement('span');
        titleText.textContent = task.title;

        const prioritySpan = document.createElement('span');
        prioritySpan.className = `priority priority-${task.priority}`;
        prioritySpan.textContent = {
            'low': 'Thấp',
            'medium': 'TB',
            'high': 'Cao',
            'urgent': 'Khẩn'
        }[task.priority] || 'TB';

        const statusSpan = document.createElement('span');
        statusSpan.className = `status status-${task.status}`;
        statusSpan.textContent = {
            'not_started': 'Chưa bắt đầu',
            'in_progress': 'Đang làm',
            'completed': 'Hoàn thành',
            'pending': 'Đang chờ',
            'canceled': 'Đã hủy'
        }[task.status] || 'Chưa bắt đầu';

        taskTitle.appendChild(toggleIcon);
        taskTitle.appendChild(titleText);
        taskTitle.appendChild(prioritySpan);
        taskTitle.appendChild(statusSpan);

        const taskActions = document.createElement('div');
        taskActions.className = 'task-actions';

        // View button
        const viewBtn = document.createElement('button');
        viewBtn.className = 'task-action';
        viewBtn.innerHTML = '<i class="fas fa-eye"></i>';
        viewBtn.title = 'Xem chi tiết';
        viewBtn.onclick = (e) => {
            e.stopPropagation();
            showTaskDetail(task.id);
        };

        // Add subtask button
        const addSubtaskBtn = document.createElement('button');
        addSubtaskBtn.className = 'task-action';
        addSubtaskBtn.innerHTML = '<i class="fas fa-plus"></i>';
        addSubtaskBtn.title = 'Thêm task con';
        addSubtaskBtn.onclick = (e) => {
            e.stopPropagation();
            showTaskModal(null, task.id);
        };

        // Edit button
        const editBtn = document.createElement('button');
        editBtn.className = 'task-action';
        editBtn.innerHTML = '<i class="fas fa-edit"></i>';
        editBtn.title = 'Sửa';
        editBtn.onclick = (e) => {
            e.stopPropagation();
            showTaskModal(task.id);
        };

        // Delete button
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'task-action';
        deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
        deleteBtn.title = 'Xóa';
        deleteBtn.onclick = async (e) => {
            e.stopPropagation();
            if (await deleteTask(task.id)) {
                taskItem.remove();
            }
        };

        taskActions.appendChild(viewBtn);
        taskActions.appendChild(addSubtaskBtn);
        taskActions.appendChild(editBtn);
        taskActions.appendChild(deleteBtn);

        taskHeader.appendChild(taskTitle);
        taskHeader.appendChild(taskActions);

        taskItem.appendChild(taskHeader);

        // Set up drag and drop
        setupDragAndDrop(taskItem);

        return taskItem;
    };

    // Event Listeners
    document.addEventListener('DOMContentLoaded', () => {
        // Initial data fetch
        fetchTasks();
        fetchUsers();

        // Add task button
        $('#add-task-btn').addEventListener('click', () => {
            showTaskModal();
        });

        // Close modal buttons
        $$('.close-modal').forEach(button => {
            button.addEventListener('click', () => {
                $$('.modal').forEach(modal => {
                    modal.style.display = 'none';
                });
            });
        });

        // Cancel buttons
        $('#cancel-task').addEventListener('click', () => {
            $('#task-modal').style.display = 'none';
        });

        $('#close-detail').addEventListener('click', () => {
            $('#task-detail-modal').style.display = 'none';
        });

        // Save task button
// Update the save-task button event handler
// Sửa hàm save task
        $('#save-task').addEventListener('click', async () => {
            const taskId = $('#task-id').value;
            const title = $('#title').value.trim();

            if (!title) {
                showAlert('Tiêu đề không được để trống!', 'danger');
                return;
            }

            let taskData = {
                title,
                description: $('#description').value.trim(),
                status: $('#status').value,
                priority: $('#priority').value,
                due_date: $('#due-date').value || null,
                assigned_to: $('#assigned-to').value || null,
                parent_id: $('#parent-id').value || null
            };

            let success = false;

            if (taskId) {
                // Update existing task
                success = await updateTask(taskId, taskData);
            } else {
                // Create new task
                const newTask = await createTask(taskData);
                success = newTask !== null;
            }

            if (success) {
                $('#task-modal').style.display = 'none';
                // Luôn làm mới toàn bộ danh sách để đảm bảo hiển thị đúng
                fetchTasks();
            }
        });
        // Close modals when clicking outside
        window.addEventListener('click', (e) => {
            // Use $$ instead of $ to get all elements matching the selector
            $$('.modal').forEach(modal => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    });
</script>


</body>
</html>
