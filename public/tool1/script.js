document.addEventListener('DOMContentLoaded', () => {
    // Khởi tạo Choices.js cho từng select
    const citySelect = new Choices('#city', {
        searchEnabled: true,
        placeholder: true,
        shouldSort: false
    });

    const districtSelect = new Choices('#district', {
        searchEnabled: true,
        placeholder: true,
        shouldSort: false
    });

    const wardSelect = new Choices('#ward', {
        searchEnabled: true,
        placeholder: true,
        shouldSort: false
    });

    const streetSelect = new Choices('#street', {
        searchEnabled: true,
        placeholder: true,
        shouldSort: false
    });

    // API base URL
    const API_BASE_URL = 'https://mytree.vn/api/don-vi-hanh-chinh/tree';

    // Hàm lấy dữ liệu từ API
    const fetchData = async (pid = 0) => {
        try {
            const url = `${API_BASE_URL}?pid=${pid}`;
            console.log(`Fetching data from: ${url}`);
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json();
            if (result.code !== 1) {
                throw new Error(`API error! code: ${result.code}`);
            }
            console.log(`Data fetched for pid=${pid}:`, result.payload);
            return result.payload;
        } catch (error) {
            console.error('Error fetching data:', error);
            return [];
        }
    };

    // Hàm populate select
    const populateSelect = async (choicesInstance, pid = 0) => {
        // Hiển thị loading
        choicesInstance.clearChoices();
        choicesInstance.setChoices([{
            value: '',
            label: 'Đang tải...',
        }], 'value', 'label', false);
        choicesInstance.disable();

        const data = await fetchData(pid);
        // Xóa các lựa chọn hiện tại
        choicesInstance.clearChoices();
        // Thêm lựa chọn mặc định
        choicesInstance.setChoices([{
            value: '',
            label: '-- Chọn --',
        }], 'value', 'label', false);

        // Kiểm tra dữ liệu trả về
        if (!Array.isArray(data)) {
            console.error(`Data for pid=${pid} is not an array:`, data);
            return;
        }

        // Thêm các lựa chọn mới
        const choices = data.map(item => ({
            value: item.id,
            label: item.name,
            disabled: !item.has_child // Nếu không có con, disable để không cho mở rộng thêm
        }));
        console.log(`Populating select with pid=${pid}:`, choices);
        choicesInstance.setChoices(choices, 'value', 'label', true);

        // Kích hoạt lại select nếu có lựa chọn
        if (choices.length > 1) {
            choicesInstance.enable();
        } else {
            choicesInstance.disable();
        }
    };

    // Tải dữ liệu cho Thành phố khi tải trang
    populateSelect(citySelect, 0);

    // Sự kiện khi thay đổi Thành phố
    citySelect.passedElement.element.addEventListener('change', async (event) => {
        const cityId = event.target.value;
        console.log('Thành phố được chọn:', cityId);
        if (cityId) {
            // Vô hiệu hóa và dọn sạch các select cấp dưới
            districtSelect.disable();
            wardSelect.disable();
            streetSelect.disable();
            districtSelect.clearChoices();
            wardSelect.clearChoices();
            streetSelect.clearChoices();
            // Tải Quận/Huyện dựa trên Thành phố đã chọn
            await populateSelect(districtSelect, cityId);
        } else {
            // Nếu không chọn Thành phố, vô hiệu hóa và dọn sạch các select cấp dưới
            districtSelect.disable();
            wardSelect.disable();
            streetSelect.disable();
            districtSelect.clearChoices();
            wardSelect.clearChoices();
            streetSelect.clearChoices();
        }
    });

    // Sự kiện khi thay đổi Quận/Huyện
    districtSelect.passedElement.element.addEventListener('change', async (event) => {
        const districtId = event.target.value;
        console.log('Quận/Huyện được chọn:', districtId);
        if (districtId) {
            wardSelect.disable();
            streetSelect.disable();
            wardSelect.clearChoices();
            streetSelect.clearChoices();
            await populateSelect(wardSelect, districtId);
        } else {
            wardSelect.disable();
            streetSelect.disable();
            wardSelect.clearChoices();
            streetSelect.clearChoices();
        }
    });

    // Sự kiện khi thay đổi Phường/Xã
    wardSelect.passedElement.element.addEventListener('change', async (event) => {
        const wardId = event.target.value;
        console.log('Phường/Xã được chọn:', wardId);
        if (wardId) {
            streetSelect.disable();
            streetSelect.clearChoices();
            await populateSelect(streetSelect, wardId);
        } else {
            streetSelect.disable();
            streetSelect.clearChoices();
        }
    });

    // Sự kiện khi thay đổi Đường phố (nếu cần xử lý thêm)
    streetSelect.passedElement.element.addEventListener('change', (event) => {
        const streetId = event.target.value;
        console.log('Đường phố được chọn:', streetId);
        // Xử lý nếu cần thiết khi người dùng chọn Đường phố
    });
});
