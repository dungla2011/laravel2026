// Dữ liệu thần số học
let numerologyData = null;
let numerologyCalculator = null;
let testPassed = true; // Biến global để check test status

// ========== CHẠY TEST TRƯỚC KHI LOAD ==========
async function initializeApp() {
    try {
        // 1. Load numerology data
        console.log('📚 Đang tải dữ liệu...');
        const dataResponse = await fetch('/assets/thansohoc/numerology-data.json');
        numerologyData = await dataResponse.json();
        
        // 2. Khởi tạo calculator
        numerologyCalculator = new NumerologyV2(numerologyData);
        
        // 3. Load history
        loadHistory();
        
        // 4. Chạy test SAU khi load
        console.log('%c🧪 CHẠY TEST KIỂM TRA TÍNH TOÁN...', 'background: #FF9800; color: white; padding: 5px 10px; font-weight: bold;');
        testPassed = await runAutomatedTests();
        
        // 5. Nếu test FAILED → Vô hiệu hóa nút tính toán
        if (!testPassed) {
            console.error('%c⛔ TEST FAILED - VÔ HIỆU HÓA TÍNH TOÁN!', 'background: #f44336; color: white; padding: 10px; font-weight: bold; font-size: 16px;');
            
            // Vô hiệu hóa form tính toán
            const calculateBtn = document.querySelector('button[onclick="calculate()"]');
            if (calculateBtn) {
                calculateBtn.disabled = true;
                calculateBtn.classList.remove('btn-primary');
                calculateBtn.classList.add('btn-danger');
                calculateBtn.innerHTML = '⛔ Tính toán bị vô hiệu hóa do lỗi test';
            }
            
            // Hiển thị cảnh báo trên đầu trang
            const container = document.querySelector('.container');
            if (container) {
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger mt-3';
                alert.innerHTML = `
                    <h4 class="alert-heading">⚠️ Cảnh báo: Test thất bại!</h4>
                    <p>Có lỗi trong công thức tính toán. Tính năng tính toán đã bị vô hiệu hóa.</p>
                    <hr>
                    <p class="mb-0">Vui lòng Liên hệ Admin để được hỗ trợ.</p>
                `;
                container.insertBefore(alert, container.firstChild);
            }
        } else {
            console.log('%c✅ TEST PASSED - Ứng dụng hoạt động bình thường', 'background: #4CAF50; color: white; padding: 5px 10px; font-weight: bold;');
        }
        
    } catch (error) {
        console.error('❌ Lỗi khi khởi động:', error);
        alert(`❌ Lỗi khi khởi động ứng dụng:\n${error.message}`);
    }
}

// Gọi hàm init khi load trang
initializeApp();

// ========== DATE PICKER SETUP ==========
// Khởi tạo các dropdown cho mobile
function populateDateDropdowns() {
    const birthYear = document.getElementById('birthYear');
    const birthMonth = document.getElementById('birthMonth');
    const birthDay = document.getElementById('birthDay');
    const birthDate = document.getElementById('birthDate');
    
    if (!birthYear || !birthMonth || !birthDay || !birthDate) {
        console.error('Some date picker elements not found!');
        return;
    }
    
    // Populate years (current year down to 1950)
    const currentYear = new Date().getFullYear();
    for (let year = currentYear; year >= 1950; year--) {
        birthYear.options.add(new Option(year, year));
    }
    
    // Populate months (1-12)
    for (let month = 1; month <= 12; month++) {
        birthMonth.options.add(new Option('Tháng ' + month, month));
    }
    
    // Function to update days based on selected month/year
    function updateDays() {
        const year = parseInt(birthYear.value) || 2000;
        const month = parseInt(birthMonth.value) || 1;
        const daysInMonth = new Date(year, month, 0).getDate();
        const currentDay = birthDay.value;
        
        birthDay.innerHTML = '<option value="">Ngày</option>';
        for (let day = 1; day <= daysInMonth; day++) {
            const opt = new Option('Ngày ' + day, day);
            if (day == currentDay) opt.selected = true;
            birthDay.options.add(opt);
        }
    }
    
    // Set default values from birthDate input
    const [year, month, day] = birthDate.value.split('-');
    birthYear.value = year;
    birthMonth.value = parseInt(month);
    updateDays();
    birthDay.value = parseInt(day);
    
    // Sync function
    function syncToDateInput() {
        const y = birthYear.value;
        const m = String(birthMonth.value || 1).padStart(2, '0');
        const d = String(birthDay.value || 1).padStart(2, '0');
        if (y) {
            birthDate.value = `${y}-${m}-${d}`;
        }
    }
    
    // Add event listeners
    birthYear.onchange = function() { updateDays(); syncToDateInput(); };
    birthMonth.onchange = function() { updateDays(); syncToDateInput(); };
    birthDay.onchange = syncToDateInput;
    
    birthDate.onchange = function() {
        const [y, m, d] = this.value.split('-');
        birthYear.value = y;
        birthMonth.value = parseInt(m);
        updateDays();
        birthDay.value = parseInt(d);
    };
}

// Gọi khi DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', populateDateDropdowns);
} else {
    populateDateDropdowns();
}


// Hàm lưu vào localStorage (chỉ lưu tên và ngày sinh)
function saveToHistory(data) {
    let history = JSON.parse(localStorage.getItem('numerologyHistory') || '[]');
    
    // Chỉ lưu thông tin cơ bản
    const basicData = {
        fullName: data.fullName,
        birthDate: data.birthDate,
        timestamp: new Date().toISOString(),
        id: Date.now()
    };
    
    // Kiểm tra trùng (cùng tên và ngày sinh)
    const existingIndex = history.findIndex(item => 
        item.fullName === basicData.fullName && 
        item.birthDate === basicData.birthDate
    );
    
    // Nếu trùng thì xóa cái cũ
    if (existingIndex !== -1) {
        history.splice(existingIndex, 1);
    }
    
    // Thêm vào đầu mảng
    history.unshift(basicData);
    
    // Giới hạn 50 records
    if (history.length > 50) {
        history = history.slice(0, 50);
    }
    
    localStorage.setItem('numerologyHistory', JSON.stringify(history));
    loadHistory();
}

// Hàm tính toán tất cả các số từ tên và ngày sinh
function calculateAllNumbers(fullName, birthDate) {
    if (!numerologyCalculator) {
        console.error('NumerologyCalculator chưa được khởi tạo');
        return null;
    }
    
    // V2 - chuyển format từ YYYY-MM-DD sang DD/MM/YYYY
    const dateObj = new Date(birthDate);
    const dateStr = `${dateObj.getDate()}/${dateObj.getMonth() + 1}/${dateObj.getFullYear()}`;
    const calc = new NumerologyV2(fullName, dateStr, numerologyData);
    const result = calc.getAll();
    
    // Thêm thông tin cơ bản
    result.fullName = fullName;
    result.birthDate = birthDate;
    
    return result;
}

// Hàm render bảng lịch sử
function renderHistoryTable(history) {
    if (history.length === 0) {
        return '<p class="text-center text-muted py-4">Chưa có dữ liệu</p>';
    }
    
    // Tạo temp V2 để lấy columns config
    const tempV2 = new NumerologyV2("", "2000-01-01", numerologyData);
    const columns = tempV2.tableColumns;
    
    // === TABLE VIEW (Desktop) ===
    let tableHtml = `
        <div class="history-table-view">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th style="width: 60px;">Số TT</th>
                    <th>Họ tên</th>
                    <th class="detail-column">Chi tiết số chủ đạo</th>
    `;
    
    columns.forEach(col => {
        tableHtml += `<th>${col}</th>`;
    });
    
    tableHtml += `
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    history.forEach((item, index) => {
        const date = new Date(item.timestamp);
        const birthDate = new Date(item.birthDate);
        
        // Tính toán bằng V2
        const dateObj = new Date(item.birthDate);
        const dateStr = `${dateObj.getDate()}/${dateObj.getMonth() + 1}/${dateObj.getFullYear()}`;
        const calc = new NumerologyV2(item.fullName, dateStr, numerologyData);
        const calculated = calc.getAll();
        
        tableHtml += `
            <tr>
                <td class="text-center"><strong>${index + 1}</strong></td>
                <td style="cursor: pointer;" onclick='viewDetailById(${item.id})'>
                    <strong>${item.fullName}</strong><br>
                    <small class="text-muted">${birthDate.getDate()}/${birthDate.getMonth() + 1}/${birthDate.getFullYear()}</small>
                </td>
        `;
        
        // Lấy thông tin chi tiết về số chủ đạo (lifePath) để hiển thị ở cột riêng
        const lifePath = calculated['lifePath'];
        let detailContent = '';
        if (lifePath && numerologyData.soChiDao && numerologyData.soChiDao[lifePath]) {
            const chiDao = numerologyData.soChiDao[lifePath];
            detailContent = `
                <div style="font-size: 0.85rem; line-height: 1.4;">
                    <strong>${chiDao.ten}</strong><br>
                    <strong>Đặc điểm:</strong> ${chiDao.dacDiem}<br>
                    <strong>Điểm mạnh:</strong> ${chiDao.diemManh}<br>
                    <strong>Điểm yếu:</strong> ${chiDao.diemYeu}<br>
                    <strong>Nghề nghiệp:</strong> ${chiDao.ngheNghiep}
                </div>
            `;
        }
        
        tableHtml += `<td class="detail-column">${detailContent}</td>`;
        
        columns.forEach(col => {
            const propName = Object.keys(tempV2.propertyMap).find(
                key => tempV2.propertyMap[key] === col
            );
            const value = calculated[propName];
            const badgeClass = tempV2.badgeColors[col];
            
            // Lấy hint từ numberTypes
            const typeConfig = tempV2.numberTypes.find(
                type => tempV2.propertyMap[type.property] === col
            );
            
            // Lấy ý nghĩa chi tiết từ JSON dựa vào property và value
            let hintText = typeConfig ? typeConfig.hint : '';
            if (numerologyData.yNghiaChiTiet && numerologyData.yNghiaChiTiet[propName]) {
                const detailHint = numerologyData.yNghiaChiTiet[propName][value];
                if (detailHint) {
                    hintText = detailHint;
                }
            }
            
            // Lấy thông tin chi tiết từ soChiDao cho số Đường Đời (lifePath)
            let tooltipContent = '';
            if (propName === 'lifePath' && numerologyData.soChiDao && numerologyData.soChiDao[value]) {
                const chiDao = numerologyData.soChiDao[value];
                tooltipContent = `
                    <strong>${chiDao.ten}</strong><br>
                    <strong>Đặc điểm:</strong> ${chiDao.dacDiem}<br>
                    <strong>Điểm mạnh:</strong> ${chiDao.diemManh}<br>
                    <strong>Điểm yếu:</strong> ${chiDao.diemYeu}<br>
                    <strong>Nghề nghiệp:</strong> ${chiDao.ngheNghiep}
                `;
            }
            
            // Xử lý đặc biệt cho missingNumbers (array)
            if (propName === 'missingNumbers') {
                const displayValue = Array.isArray(value) && value.length > 0 
                    ? value.join(', ') 
                    : '✓';
                tableHtml += `
                    <td>
                        <span class="badge ${badgeClass}">${displayValue}</span>
                        <div class="hint-text">${hintText}</div>
                    </td>
                `;
            } else {
                const tooltipAttr = tooltipContent ? `data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="${tooltipContent.replace(/"/g, '&quot;')}"` : '';
                tableHtml += `
                    <td>
                        <span class="badge ${badgeClass}">${value}</span>${tooltipContent ? ' <span class="help-icon" ' + tooltipAttr + '>❓</span>' : ''}
                        <div class="hint-text">
                            ${hintText}
                        </div>
                    </td>
                `;
            }
        });
        
        tableHtml += `
                <td>
                    <button class="btn btn-sm btn-info" onclick='viewDetailById(${item.id})'>👁️</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteHistory(${item.id})">🗑️</button>
                </td>
            </tr>
        `;
    });
    
    tableHtml += '</tbody></table></div>';
    
    // === CARD VIEW (Mobile) ===
    let cardHtml = '<div class="history-card-view">';
    
    history.forEach((item, index) => {
        const birthDate = new Date(item.birthDate);
        const dateObj = new Date(item.birthDate);
        const dateStr = `${dateObj.getDate()}/${dateObj.getMonth() + 1}/${dateObj.getFullYear()}`;
        const calc = new NumerologyV2(item.fullName, dateStr, numerologyData);
        const calculated = calc.getAll();
        
        // Lấy thông tin chi tiết về số chủ đạo
        const lifePath = calculated['lifePath'];
        let detailContent = '';
        if (lifePath && numerologyData.soChiDao && numerologyData.soChiDao[lifePath]) {
            const chiDao = numerologyData.soChiDao[lifePath];
            detailContent = `
                <strong>${chiDao.ten}</strong><br>
                <strong>Đặc điểm:</strong> ${chiDao.dacDiem}<br>
                <strong>Điểm mạnh:</strong> ${chiDao.diemManh}<br>
                <strong>Điểm yếu:</strong> ${chiDao.diemYeu}<br>
                <strong>Nghề nghiệp:</strong> ${chiDao.ngheNghiep}
            `;
        }
        
        cardHtml += `
            <div class="history-card">
                <div class="history-card-header" style="cursor: pointer;" onclick='viewDetailById(${item.id})'>
                    <div class="history-card-title">${index + 1}. ${item.fullName}</div>
                    <div class="history-card-subtitle">${birthDate.getDate()}/${birthDate.getMonth() + 1}/${birthDate.getFullYear()}</div>
                </div>
                <div class="history-card-body">
        `;
        
        // Chi tiết số chủ đạo - Đưa lên đầu tiên
        if (detailContent) {
            cardHtml += `
                <div class="detail-column-card">
                    ${detailContent}
                </div>
            `;
        }
        
        // Hiển thị các chỉ số
        columns.forEach(col => {
            const propName = Object.keys(tempV2.propertyMap).find(
                key => tempV2.propertyMap[key] === col
            );
            const value = calculated[propName];
            const badgeClass = tempV2.badgeColors[col];
            
            const displayValue = Array.isArray(value) 
                ? (value.length > 0 ? value.join(', ') : '✓')
                : value;
            
            // Lấy hint text
            let hintText = '';
            if (numerologyData.yNghiaChiTiet && numerologyData.yNghiaChiTiet[propName]) {
                const detailHint = numerologyData.yNghiaChiTiet[propName][value];
                if (detailHint) {
                    hintText = detailHint;
                }
            }
            
            cardHtml += `
                <div class="history-card-item">
                    <div class="history-card-label">${col}</div>
                    <div class="history-card-value">
                        <span class="badge ${badgeClass}">${displayValue}</span>
                        ${hintText ? `<div class="hint-text">${hintText}</div>` : ''}
                    </div>
                </div>
            `;
        });
        
        cardHtml += `
                </div>
                <div class="history-card-footer">
                    <button class="btn btn-sm btn-info" onclick='viewDetailById(${item.id})'>👁️ Xem</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteHistory(${item.id})">🗑️ Xóa</button>
                </div>
            </div>
        `;
    });
    
    cardHtml += '</div>';
    
    return tableHtml + cardHtml;
}

// Hàm export CSV
function exportToCSV() {
    const history = JSON.parse(localStorage.getItem('numerologyHistory') || '[]');
    
    if (history.length === 0) {
        alert('Không có dữ liệu để xuất!');
        return;
    }
    
    // Export cho V2
    const tempV2 = new NumerologyV2("", "2000-01-01", numerologyData);
    const columns = tempV2.tableColumns;
    
    // Header
    let csvContent = 'STT,Họ Tên,Ngày Sinh,' + columns.join(',') + '\n';
    
    // Data rows
    history.forEach((item, index) => {
        const dateObj = new Date(item.birthDate);
        const dateStr = `${dateObj.getDate()}/${dateObj.getMonth() + 1}/${dateObj.getFullYear()}`;
        const calc = new NumerologyV2(item.fullName, dateStr, numerologyData);
        const calculated = calc.getAll();
        
        let row = `${index + 1},"${item.fullName}","${item.birthDate}"`;
        
        columns.forEach(col => {
            const propName = Object.keys(tempV2.propertyMap).find(
                key => tempV2.propertyMap[key] === col
            );
            const value = calculated[propName];
            const displayValue = Array.isArray(value) ? value.join(';') : value;
            row += `,"${displayValue}"`;
        });
        
        csvContent += row + '\n';
    });
    
    // Tạo file và download
    const BOM = '\uFEFF'; // Thêm BOM để Excel hiển thị đúng tiếng Việt
    const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    const timestamp = new Date().toISOString().slice(0, 10);
    link.setAttribute('href', url);
    link.setAttribute('download', `Than_So_Hoc_${timestamp}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Hàm export JSON (cho test case)
function exportToJSON() {
    const history = JSON.parse(localStorage.getItem('numerologyHistory') || '[]');
    
    if (history.length === 0) {
        alert('Không có dữ liệu để xuất!');
        return;
    }
    
    // Export cho V2 với tất cả các chỉ số đã tính
    const tempV2 = new NumerologyV2("", "2000-01-01", numerologyData);
    
    const exportData = history.map((item, index) => {
        const dateObj = new Date(item.birthDate);
        const dateStr = `${dateObj.getDate()}/${dateObj.getMonth() + 1}/${dateObj.getFullYear()}`;
        const calc = new NumerologyV2(item.fullName, dateStr, numerologyData);
        const calculated = calc.getAll();
        
        return {
            id: item.id,
            stt: index + 1,
            fullName: item.fullName,
            birthDate: item.birthDate,
            timestamp: item.timestamp,
            calculated: calculated
        };
    });
    
    // Format JSON đẹp
    const jsonContent = JSON.stringify(exportData, null, 2);
    
    // Tạo file và download
    const blob = new Blob([jsonContent], { type: 'application/json;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    const timestamp = new Date().toISOString().slice(0, 10);
    link.setAttribute('href', url);
    link.setAttribute('download', `Than_So_Hoc_TestData_${timestamp}.json`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Hàm khởi tạo tooltips Bootstrap
function initTooltips() {
    // Destroy old tooltips trước
    const existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    existingTooltips.forEach(el => {
        const tooltip = bootstrap.Tooltip.getInstance(el);
        if (tooltip) {
            tooltip.dispose();
        }
    });
    
    // Khởi tạo tooltips mới
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, {
        html: true,
        placement: 'top',
        trigger: 'hover'
    }));
}

// Hàm load lịch sử// Hàm load lịch sử (render giống hệt nhau cho cả 2 bảng)
function loadHistory() {
    const history = JSON.parse(localStorage.getItem('numerologyHistory') || '[]');
    
    // Render cho cả 2 bảng với cùng 1 code
    const homeHistoryTable = document.getElementById('homeHistoryTable');
    if (homeHistoryTable) {
        homeHistoryTable.innerHTML = renderHistoryTable(history);
    }
    
    const historyTable = document.getElementById('historyTable');
    if (historyTable) {
        historyTable.innerHTML = renderHistoryTable(history);
    }
    
    // Load bảng ý nghĩa các số
    loadMeaningTables();
    
    // Khởi tạo tooltips sau khi render xong
    setTimeout(initTooltips, 100);
}

// Hàm render bảng ý nghĩa các số
function loadMeaningTables() {
    if (!numerologyData || !numerologyData.yNghiaChiTiet) return;
    
    const meanings = numerologyData.yNghiaChiTiet;
    const propertyMap = {
        'lifePath': 'lifePath-table',
        'expression': 'expression-table',
        'soulUrge': 'soul-table',
        'soulNumber': 'soul-table',
        'personality': 'personality-table',
        'personalityNumber': 'personality-table',
        'maturity': 'maturity-table',
        'maturityNumber': 'maturity-table',
        'attitude': 'attitude-table',
        'attitudeNumber': 'attitude-table',
        'balance': 'balance-table',
        'balanceNumber': 'balance-table'
    };
    
    // Render từng bảng
    Object.keys(propertyMap).forEach(property => {
        const tableId = propertyMap[property];
        const tableBody = document.getElementById(tableId);
        
        if (!tableBody || !meanings[property]) return;
        
        // Tránh render trùng
        if (tableBody.children.length > 0) return;
        
        let html = '';
        const data = meanings[property];
        
        // Sắp xếp theo số
        const numbers = Object.keys(data).sort((a, b) => {
            const numA = parseInt(a);
            const numB = parseInt(b);
            return numA - numB;
        });
        
        numbers.forEach(num => {
            const meaning = data[num];
            const isMaster = num === '11' || num === '22' || num === '33';
            const rowClass = isMaster ? 'table-warning fw-bold' : '';
            
            html += `
                <tr class="${rowClass}">
                    <td class="text-center">${num}</td>
                    <td>${meaning}</td>
                </tr>
            `;
        });
        
        tableBody.innerHTML = html;
    });
}

// Hàm xóa một item
function deleteHistory(id) {
    if (!confirm('Bạn có chắc muốn xóa?')) return;
    
    let history = JSON.parse(localStorage.getItem('numerologyHistory') || '[]');
    history = history.filter(item => item.id !== id);
    localStorage.setItem('numerologyHistory', JSON.stringify(history));
    loadHistory();
}

// Hàm xóa tất cả
function clearHistory() {
    if (!confirm('Bạn có chắc muốn xóa toàn bộ lịch sử?')) return;
    
    localStorage.removeItem('numerologyHistory');
    loadHistory();
}

// Hàm toggle hiển thị cột chi tiết
function toggleHints() {
    const detailColumns = document.querySelectorAll('.detail-column');
    const toggleBtn = document.getElementById('toggleHints');
    
    // Kiểm tra trạng thái hiện tại bằng getComputedStyle
    const isVisible = detailColumns[0] && window.getComputedStyle(detailColumns[0]).display !== 'none';
    
    detailColumns.forEach(col => {
        col.style.display = isVisible ? 'none' : 'table-cell';
    });
    
    toggleBtn.textContent = isVisible ? '📋 Hiện Chi Tiết' : '📋 Ẩn Chi Tiết';
}

// Hàm xem chi tiết theo ID
function viewDetailById(id) {
    const history = JSON.parse(localStorage.getItem('numerologyHistory') || '[]');
    const item = history.find(h => h.id === id);
    
    if (!item) return;
    
    // Scroll lên top trước
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Tính toán lại các số
    const calculatedData = calculateAllNumbers(item.fullName, item.birthDate);
    
    // Fill form
    document.getElementById('fullName').value = item.fullName;
    document.getElementById('birthDate').value = item.birthDate;
    
    // Hiển thị kết quả
    displayResults(calculatedData);
    
    // Scroll to results (sau khi smooth scroll lên top hoàn tất)
    setTimeout(() => {
        document.getElementById('results').scrollIntoView({ behavior: 'smooth' });
    }, 500);
}

// Hàm hiển thị thông tin số (gọn)
function displayNumberInfo(number, elementId) {
    const element = document.getElementById(elementId);
    const numStr = number.toString();
    const meaning = numerologyData.yNghiaSo[numStr] || '';
    
    // Lấy badge color từ NumerologyV2
    const tempV2 = new NumerologyV2("", "1/1/2000", numerologyData);
    const propertyMap = tempV2.propertyMap;
    const badgeColors = tempV2.badgeColors;
    
    // Map elementId sang property name
    const idToProperty = {
        'lifePath': 'lifePath',
        'soulNumber': 'soulUrge',
        'personalityNumber': 'personality',
        'maturityNumber': 'maturity',
        'attitudeNumber': 'attitude',
        'birthDayNumber': 'birthDay'
    };
    
    const propName = idToProperty[elementId];
    let badgeClass = 'bg-primary'; // default
    
    if (propName && propertyMap[propName]) {
        const displayName = propertyMap[propName];
        badgeClass = badgeColors[displayName] || 'bg-primary';
    }
    
    element.innerHTML = `
        <div class="number-badge" style="background: none !important;">
            <span class="badge ${badgeClass}" style="font-size: 1.5rem; padding: 0.75rem 1rem;">${number}</span>
        </div>
        <div class="number-meaning">${meaning}</div>
    `;
}

// Hàm hiển thị chi tiết số chủ đạo
function displayDetailInfo(number) {
    const element = document.getElementById('detailInfo');
    const numStr = number.toString();
    const info = numerologyData.soChiDao[numStr];
    
    if (info) {
        element.innerHTML = `
            <h4>${info.ten}</h4>
            <p><strong>Đặc điểm:</strong> ${info.dacDiem}</p>
            <p><strong>Điểm mạnh:</strong> ${info.diemManh}</p>
            <p><strong>Điểm yếu:</strong> ${info.diemYeu}</p>
            <p><strong>Nghề nghiệp phù hợp:</strong> ${info.ngheNghiep}</p>
        `;
    } else {
        element.innerHTML = `<p>Không có thông tin chi tiết cho số ${number}</p>`;
    }
}

// Hàm hiển thị kết quả
function displayResults(calculatedData) {
    const fullName = calculatedData.fullName;
    const birthDate = calculatedData.birthDate;
    
    // Format ngày sinh
    let displayDate;
    if (birthDate.includes('/')) {
        displayDate = birthDate;
    } else {
        const date = new Date(birthDate);
        displayDate = `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
    }
    
    // Tạo calculator instance
    const calc = new NumerologyV2(fullName, displayDate, numerologyData);
    const calculated = calc.getAll();
    const tempV2 = new NumerologyV2("", "1/1/2000", numerologyData);
    const numberTypes = tempV2.numberTypes; // Array of objects
    
    // === TABLE VIEW (Desktop) ===
    let tableHeaderHtml = '<th>Họ Tên</th><th>Ngày Sinh</th>';
    numberTypes.forEach(type => {
        tableHeaderHtml += `<th>${type.text}</th>`;
    });
    document.getElementById('resultTableHeader').innerHTML = tableHeaderHtml;
    
    let tableBodyHtml = '<tr>';
    tableBodyHtml += `<td><strong>${fullName}</strong></td>`;
    tableBodyHtml += `<td>${displayDate}</td>`;
    
    numberTypes.forEach(type => {
        const propName = type.property;
        const value = calculated[propName];
        const badgeClass = type.badge;
        
        // Lấy hint text - bắt đầu với hint mặc định từ type config
        let hintText = type.hint || '';
        const valueStr = String(value); // Convert to string for key lookup
        
        // Override với yNghiaChiTiet nếu có
        if (numerologyData.yNghiaChiTiet && numerologyData.yNghiaChiTiet[propName]) {
            const detailHint = numerologyData.yNghiaChiTiet[propName][valueStr];
            if (detailHint) {
                hintText = detailHint;
            }
        }
        // Fallback về yNghiaSo nếu không có trong yNghiaChiTiet
        if (!hintText && numerologyData.yNghiaSo && numerologyData.yNghiaSo[valueStr]) {
            hintText = numerologyData.yNghiaSo[valueStr];
        }
        
        // Lấy tooltip cho số đường đời
        let tooltipContent = '';
        if (propName === 'lifePath' && numerologyData.soChiDao && numerologyData.soChiDao[value]) {
            const chiDao = numerologyData.soChiDao[value];
            tooltipContent = `
                <strong>${chiDao.ten}</strong><br>
                <strong>Đặc điểm:</strong> ${chiDao.dacDiem}<br>
                <strong>Điểm mạnh:</strong> ${chiDao.diemManh}<br>
                <strong>Điểm yếu:</strong> ${chiDao.diemYeu}<br>
                <strong>Nghề nghiệp:</strong> ${chiDao.ngheNghiep}
            `;
        }
        
        // Xử lý đặc biệt cho missingNumbers
        if (propName === 'missingNumbers') {
            const displayValue = Array.isArray(value) && value.length > 0 
                ? value.join(', ') 
                : '✓';
            tableBodyHtml += `
                <td>
                    <span class="badge ${badgeClass}">${displayValue}</span>
                    <div class="hint-text">${hintText}</div>
                </td>
            `;
        } else {
            const tooltipAttr = tooltipContent ? `data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="${tooltipContent.replace(/"/g, '&quot;')}"` : '';
            tableBodyHtml += `
                <td>
                    <span class="badge ${badgeClass}">${value}</span>${tooltipContent ? ' <span class="help-icon" ' + tooltipAttr + '>❓</span>' : ''}
                    <div class="hint-text">
                        ${hintText}
                    </div>
                </td>
            `;
        }
    });
    
    tableBodyHtml += '</tr>';
    document.getElementById('resultTableBody').innerHTML = tableBodyHtml;
    
    // === CARD VIEW (Mobile) ===
    const lifePath = calculated['lifePath'];
    let detailContent = '';
    if (lifePath && numerologyData.soChiDao && numerologyData.soChiDao[lifePath]) {
        const chiDao = numerologyData.soChiDao[lifePath];
        detailContent = `
            <strong>${chiDao.ten}</strong><br>
            <strong>Đặc điểm:</strong> ${chiDao.dacDiem}<br>
            <strong>Điểm mạnh:</strong> ${chiDao.diemManh}<br>
            <strong>Điểm yếu:</strong> ${chiDao.diemYeu}<br>
            <strong>Nghề nghiệp:</strong> ${chiDao.ngheNghiep}
        `;
    }
    
    let cardHtml = `
        <div class="history-card">
            <div class="history-card-header">
                <div class="history-card-title">${fullName}</div>
                <div class="history-card-subtitle">${displayDate}</div>
            </div>
            <div class="history-card-body">
    `;
    
    // Chi tiết số chủ đạo - Đưa lên đầu tiên
    if (detailContent) {
        cardHtml += `
            <div class="detail-column-card">
                ${detailContent}
            </div>
        `;
    }
    
    // Hiển thị các chỉ số
    numberTypes.forEach(type => {
        const propName = type.property;
        const value = calculated[propName];
        const badgeClass = type.badge;
        
        const displayValue = Array.isArray(value) 
            ? (value.length > 0 ? value.join(', ') : '✓')
            : value;
        
        // Lấy hint text - bắt đầu với hint mặc định từ type config
        let hintText = type.hint || '';
        const valueStr = String(value); // Convert to string for key lookup
        
        // Override với yNghiaChiTiet nếu có
        if (numerologyData.yNghiaChiTiet && numerologyData.yNghiaChiTiet[propName]) {
            const detailHint = numerologyData.yNghiaChiTiet[propName][valueStr];
            if (detailHint) {
                hintText = detailHint;
            }
        }
        // Fallback về yNghiaSo nếu không có trong yNghiaChiTiet
        if (!hintText && numerologyData.yNghiaSo && numerologyData.yNghiaSo[valueStr]) {
            hintText = numerologyData.yNghiaSo[valueStr];
        }
        
        cardHtml += `
            <div class="history-card-item">
                <div class="history-card-label">${type.text}</div>
                <div class="history-card-value">
                    <span class="badge ${badgeClass}">${displayValue}</span>
                    ${hintText ? `<div class="hint-text">${hintText}</div>` : ''}
                </div>
            </div>
        `;
    });
    
    cardHtml += `
            </div>
        </div>
    `;
    
    document.getElementById('resultCard').innerHTML = cardHtml;
    
    // Cập nhật header Chi Tiết Số Chủ Đạo với tên
    const detailHeader = document.getElementById('detailHeader');
    if (detailHeader) {
        detailHeader.innerHTML = `📖 Chi Tiết - ${fullName}`;
    }
    
    // Hiển thị chi tiết số đường đời
    if (lifePath !== undefined) {
        displayDetailInfo(lifePath);
    }
    
    // Hiển thị Hành Trình Cuộc Đời
    if (calculated.lifeStages && calculated.pinnacles && calculated.challenges) {
        displayLifeJourney(calculated);
    }
    
    // Init tooltips
    initTooltips();
    
    // Hiển thị kết quả
    document.getElementById('results').classList.remove('d-none');
}

// Hàm hiển thị Hành Trình Cuộc Đời
function displayLifeJourney(calculatedData) {
    const lifeJourneySection = document.getElementById('lifeJourneySection');
    if (!lifeJourneySection) return;
    
    // Hiển thị section
    lifeJourneySection.style.display = 'block';
    
    // 1. Giai đoạn phát triển
    const stages = calculatedData.lifeStages;
    const stagesHtml = `
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Giai Đoạn</th>
                        <th>Độ Tuổi</th>
                        <th>Số</th>
                        <th>Ý Nghĩa</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>🌱 Tuổi Trẻ</strong></td>
                        <td>Từ 0 đến ${stages.youth.endAge} tuổi</td>
                        <td><span class="badge bg-info">${stages.youth.number}</span></td>
                        <td>${getNumerologyMeaning(stages.youth.number)}</td>
                    </tr>
                    <tr>
                        <td><strong>🌳 Trưởng Thành</strong></td>
                        <td>Từ ${stages.adult.startAge} đến ${stages.adult.endAge} tuổi</td>
                        <td><span class="badge bg-success">${stages.adult.number}</span></td>
                        <td>${getNumerologyMeaning(stages.adult.number)}</td>
                    </tr>
                    <tr>
                        <td><strong>🌲 Chín Chắn</strong></td>
                        <td>Từ ${stages.mature.startAge} tuổi trở đi</td>
                        <td><span class="badge bg-primary">${stages.mature.number}</span></td>
                        <td>${getNumerologyMeaning(stages.mature.number)}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="alert alert-info mt-3">
            <strong>💡 Giải thích:</strong> Mỗi giai đoạn cuộc đời mang năng lượng của một con số khác nhau, ảnh hưởng đến cách bạn phát triển và trải nghiệm cuộc sống.
        </div>
    `;
    document.getElementById('lifeStagesContent').innerHTML = stagesHtml;
    
    // 2. Đỉnh cao (Pinnacles)
    const pinnacles = calculatedData.pinnacles;
    const pinnaclesHtml = `
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Đỉnh Cao</th>
                        <th>Tuổi Bắt Đầu</th>
                        <th>Năm</th>
                        <th>Số</th>
                        <th>Ý Nghĩa</th>
                    </tr>
                </thead>
                <tbody>
                    ${pinnacles.map((p, idx) => `
                        <tr>
                            <td><strong>${p.label}</strong></td>
                            <td>${p.age} tuổi</td>
                            <td>${p.year}</td>
                            <td><span class="badge bg-warning text-dark">${p.number}</span></td>
                            <td>${getNumerologyMeaning(p.number)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        <div class="alert alert-warning mt-3">
            <strong>⛰️ Giải thích:</strong> Các Đỉnh Cao đại diện cho những cơ hội và thách thức lớn trong các giai đoạn khác nhau của cuộc đời. Mỗi đỉnh kéo dài 9 năm.
        </div>
    `;
    document.getElementById('pinnaclesContent').innerHTML = pinnaclesHtml;
    
    // 3. Thử thách (Challenges)
    const challenges = calculatedData.challenges;
    const challengesHtml = `
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Thử Thách</th>
                        <th>Giai Đoạn</th>
                        <th>Số</th>
                        <th>Ý Nghĩa</th>
                    </tr>
                </thead>
                <tbody>
                    ${challenges.map((c, idx) => `
                        <tr>
                            <td><strong>${c.label}</strong></td>
                            <td>${c.period}</td>
                            <td><span class="badge bg-danger">${c.number}</span></td>
                            <td>${getChallengeMeaning(c.number)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        <div class="alert alert-danger mt-3">
            <strong>💪 Giải thích:</strong> Các Thử Thách cho biết những bài học quan trọng bạn cần học trong từng giai đoạn. Số 0 nghĩa là không có thử thách cụ thể.
        </div>
    `;
    document.getElementById('challengesContent').innerHTML = challengesHtml;
}

// Hàm lấy ý nghĩa số học
function getNumerologyMeaning(num) {
    const meanings = {
        1: "Độc lập, lãnh đạo, khởi đầu mới",
        2: "Hợp tác, cân bằng, ngoại giao",
        3: "Sáng tạo, giao tiếp, vui vẻ",
        4: "Ổn định, thực tế, xây dựng",
        5: "Tự do, thay đổi, phiêu lưu",
        6: "Trách nhiệm, gia đình, chăm sóc",
        7: "Tâm linh, trí tuệ, nội tâm",
        8: "Quyền lực, thành công vật chất",
        9: "Hoàn thiện, nhân đạo, từ bi",
        11: "Trực giác mạnh, sứ mệnh tâm linh",
        22: "Kiến trúc sư vĩ đại, xây dựng di sản"
    };
    return meanings[num] || "Năng lượng đặc biệt";
}

// Hàm lấy ý nghĩa thử thách
function getChallengeMeaning(num) {
    const challengeMeanings = {
        0: "Không có thử thách cụ thể - bạn có tự do lựa chọn",
        1: "Học cách tự tin và độc lập",
        2: "Học cách hợp tác và kiên nhẫn",
        3: "Học cách diễn đạt cảm xúc",
        4: "Học cách kỷ luật và tổ chức",
        5: "Học cách kiểm soát sự thay đổi",
        6: "Học cách chấp nhận trách nhiệm",
        7: "Học cách tin tưởng và mở lòng",
        8: "Học cách cân bằng vật chất và tâm linh"
    };
    return challengeMeanings[num] || "Thử thách đặc biệt";
}

// Xử lý form submit
// Xử lý form submit
document.getElementById('numerologyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Kiểm tra test status
    if (!testPassed) {
        alert('⛔ Tính năng tính toán đã bị vô hiệu hóa do lỗi test!\n\nVui lòng Liên hệ Admin.');
        return;
    }
    
    if (!numerologyData) {
        alert('Đang tải dữ liệu, vui lòng thử lại sau giây lát!');
        return;
    }
    
    const fullName = document.getElementById('fullName').value.trim();
    const birthDate = document.getElementById('birthDate').value;
    
    if (!fullName || !birthDate) {
        alert('Vui lòng nhập đầy đủ thông tin!');
        return;
    }
    
    // Tính toán các số bằng V2
    const calculatedData = calculateAllNumbers(fullName, birthDate);
    
    // Lưu vào localStorage (chỉ lưu tên và ngày sinh)
    saveToHistory({ fullName, birthDate });
    
    // Hiển thị kết quả
    displayResults(calculatedData);
});

// ========== HÀM TEST TỰ ĐỘNG ==========
async function runAutomatedTests() {
    console.log('%c🧪 BẮT ĐẦU CHẠY TEST TỰ ĐỘNG...', 'background: #4CAF50; color: white; padding: 5px 10px; font-weight: bold;');
    
    try {
        // Load test data
        const response = await fetch('/assets/thansohoc/test_data.json');
        const testData = await response.json();
        
        console.log(`📋 Tải được ${testData.length} test cases`);
        
        let passCount = 0;
        let failCount = 0;
        const failures = [];
        
        // Chạy từng test case
        testData.forEach((testCase, index) => {
            console.group(`Test #${testCase.stt}: ${testCase.fullName}`);
            
            // Tính toán lại
            const dateObj = new Date(testCase.birthDate);
            const dateStr = `${dateObj.getDate()}/${dateObj.getMonth() + 1}/${dateObj.getFullYear()}`;
            const calc = new NumerologyV2(testCase.fullName, dateStr, numerologyData);
            const actual = calc.getAll();
            const expected = testCase.calculated;
            
            // So sánh từng chỉ số
            const properties = [
                'lifePath', 'expression', 'maturity', 'personality', 
                'attitude', 'soulUrge', 'innerSelf', 'approach', 
                'intelligence', 'balance', 'missingNumbers'
            ];
            
            let hasError = false;
            const errors = [];
            
            properties.forEach(prop => {
                const actualValue = actual[prop];
                const expectedValue = expected[prop];
                
                // So sánh giá trị
                let isMatch = false;
                if (Array.isArray(actualValue) && Array.isArray(expectedValue)) {
                    // So sánh mảng
                    isMatch = JSON.stringify(actualValue.sort()) === JSON.stringify(expectedValue.sort());
                } else {
                    isMatch = actualValue === expectedValue;
                }
                
                if (!isMatch) {
                    hasError = true;
                    errors.push({
                        property: prop,
                        expected: expectedValue,
                        actual: actualValue
                    });
                    console.error(`❌ ${prop}: Expected ${JSON.stringify(expectedValue)}, Got ${JSON.stringify(actualValue)}`);
                } else {
                    console.log(`✅ ${prop}: ${JSON.stringify(actualValue)}`);
                }
            });
            
            if (hasError) {
                failCount++;
                failures.push({
                    testCase: testCase.stt,
                    name: testCase.fullName,
                    birthDate: testCase.birthDate,
                    errors: errors
                });
                console.error(`❌ TEST FAILED`);
            } else {
                passCount++;
                console.log(`✅ TEST PASSED`);
            }
            
            console.groupEnd();
        });
        
        // Tổng kết
        console.log('%c========== KẾT QUẢ TEST ==========', 'background: #2196F3; color: white; padding: 5px 10px; font-weight: bold;');
        console.log(`✅ Passed: ${passCount}/${testData.length}`);
        console.log(`❌ Failed: ${failCount}/${testData.length}`);
        
        if (failCount > 0) {
            console.error('%c⚠️ CÓ TEST THẤT BẠI!', 'background: #f44336; color: white; padding: 5px 10px; font-weight: bold;');
            console.table(failures);
            
            // Alert để báo lỗi
            alert(`⚠️ CÓ LỖI TEST!\n\n${failCount}/${testData.length} test cases bị lỗi.\n\nỨng dụng sẽ KHÔNG khởi động.\n\nVui lòng Liên hệ Admin để được hỗ trợ.`);
            
            return false; // RETURN FALSE để dừng app
        } else {
            console.log('%c🎉 TẤT CẢ TEST ĐỀU PASS!', 'background: #4CAF50; color: white; padding: 5px 10px; font-weight: bold;');
            return true; // RETURN TRUE để tiếp tục
        }
        
    } catch (error) {
        console.error('❌ Lỗi khi chạy test:', error);
        alert(`❌ Lỗi khi chạy test:\n${error.message}\n\nỨng dụng sẽ KHÔNG khởi động.`);
        return false; // RETURN FALSE nếu có exception
    }
}

// ========== HISTORY DROPDOWN ==========
const fullNameInput = document.getElementById('fullName');
const historyDropdown = document.getElementById('historyDropdown');

// Hiển thị dropdown khi focus vào input
fullNameInput.addEventListener('focus', function() {
    renderHistoryDropdown();
});

// Ẩn dropdown khi click bên ngoài
document.addEventListener('click', function(e) {
    if (!fullNameInput.contains(e.target) && !historyDropdown.contains(e.target)) {
        historyDropdown.style.display = 'none';
    }
});

// Filter dropdown khi gõ
fullNameInput.addEventListener('input', function() {
    renderHistoryDropdown(this.value.toLowerCase());
});

// Render dropdown
function renderHistoryDropdown(filter = '') {
    const history = JSON.parse(localStorage.getItem('numerologyHistory') || '[]');
    
    if (history.length === 0) {
        historyDropdown.style.display = 'none';
        return;
    }
    
    // Filter history
    const filtered = filter 
        ? history.filter(item => item.fullName.toLowerCase().includes(filter))
        : history.slice(0, 10); // Chỉ hiển thị 10 items gần nhất
    
    if (filtered.length === 0) {
        historyDropdown.style.display = 'none';
        return;
    }
    
    // Render items
    historyDropdown.innerHTML = filtered.map(item => {
        const date = new Date(item.birthDate);
        const displayDate = `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
        
        return `
            <a class="dropdown-item" href="#" onclick="selectHistoryItem(${item.id}); return false;">
                <div><strong>${item.fullName}</strong></div>
                <small class="text-muted">${displayDate}</small>
            </a>
        `;
    }).join('');
    
    historyDropdown.style.display = 'block';
}

// Chọn item từ dropdown
function selectHistoryItem(id) {
    const history = JSON.parse(localStorage.getItem('numerologyHistory') || '[]');
    const item = history.find(h => h.id === id);
    
    if (item) {
        document.getElementById('fullName').value = item.fullName;
        document.getElementById('birthDate').value = item.birthDate;
        
        // Đồng bộ với mobile dropdowns
        const [year, month, day] = item.birthDate.split('-');
        const birthYear = document.getElementById('birthYear');
        const birthMonth = document.getElementById('birthMonth');
        const birthDay = document.getElementById('birthDay');
        
        if (birthYear && birthMonth && birthDay) {
            birthYear.value = year;
            birthMonth.value = parseInt(month);
            // Trigger update days
            birthMonth.dispatchEvent(new Event('change'));
            setTimeout(() => {
                birthDay.value = parseInt(day);
            }, 10);
        }
        
        historyDropdown.style.display = 'none';
        
        // Tự động submit form để tính toán
        document.getElementById('numerologyForm').dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
}