<?php

use Elasticsearch\Endpoints\Cat\Help;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;
use App\Models\EventInfo_Meta;
?>
<script>

    // Function to build HTML table from user data
    function buildHtmlTable(data) {

        console.log(" buildHtmlTable ... ");

        // Extract unique organizations for dropdown
        const uniqueOrganizations = [];
        data.forEach(user => {
            if (user.organization && !uniqueOrganizations.includes(user.organization)) {
                uniqueOrganizations.push(user.organization);
            }
        });
        // Sort organizations alphabetically
        uniqueOrganizations.sort();

        // Create organization dropdown options
        let orgOptions = `<option value="all"> - Đơn vị - </option>`;
        uniqueOrganizations.forEach(org => {
            orgOptions += `<option value="${org}">${org}</option>`;
        });

        // Extract unique groups for dropdown
        const uniqueGroups = [];
        data.forEach(user => {
            if (user.parent_name && !uniqueGroups.includes(user.parent_name)) {
                uniqueGroups.push(user.parent_name);
            }
        });
        // Sort groups alphabetically
        uniqueGroups.sort();

        // Create group dropdown options
        let groupOptions = `<option value="all"> - Nhóm - </option>`;
        uniqueGroups.forEach(group => {
            groupOptions += `<option value="${group}">${group}</option>`;
        });

        // Create the HTML table template string
        let tableHtml = `
<table class="table table-bordered table-striped select_all1" id="tbl_user_list_tin">

    <tr style="border-top: 0px solid gray!important">
      <th scope="col">#</th>
      <th scope="col" style="min-width: 200px">
      <input type="text" placeholder="Họ tên" class="form-control form-control-sm name_select">
      </th>
      <th scope="col" style="display: ">
      <input type="text" placeholder="Email" class="form-control form-control-sm email_select">
      </th>
      <th scope="col" style="max-width: 100px; display: none">
        <input type="text" placeholder="Số phone" class="form-control form-control-sm phone_select">
       </th>
      <th scope="col" style="max-width: 150px">
      <input type="text" placeholder="Tổ chức" class="form-control form-control-sm org-filter">
      </th>
      <th scope="col" style="min-width: 180px;">
      <select class="form-control form-control-sm group-filter">
        ${groupOptions}
      </select>
      </th>
      <th scope="col" style="text-align: center; width: 100px">
      <input type="text" placeholder="Ghi chú" class="form-control form-control-sm note_select">

      </th>
        <th scope="col" style="text-align: center; width: 100px">
          <select class="form-control form-control-sm select_lang">
                <option value="">-Ngôn ngữ</option>
                <option value="vi">Việt</option>
                <option value="en">Anh</option>
          </select>
      </th>

      <th scope="col" style="text-align: center; width: 100px">
      <select class="form-control form-control-sm confirm_join_at">
            <option value="">-Xác nhận</option>
            <option value="1">Đã xác nhận</option>
            <option value="0">Chưa xác nhận</option>
      </select>

      </th>

      <th scope="col" style="text-align: center; width: 100px">
          <select class="form-control form-control-sm deny_join_at">
                <option value="">-Từ chối</option>
                <option value="1">Đã từ chối</option>
                <option value="0">Chưa từ chối</option>
          </select>
      </th>
      <th scope="col" style="text-align: center; width: 100px">
          <select class="form-control form-control-sm attend_at">
                <option value="">-CheckIn</option>
                <option value="1">Đã CheckIn</option>
                <option value="0">Chưa CheckIn</option>
          </select>
      </th>
    </tr>

  `;

        let nShow = 0;
        // Process data array and add rows
        data.forEach((user, index) => {
            nShow++;
            // DEBUG: Log user data to check confirm_join_at values
            if (user.confirm_join_at) {
                console.log(`Debug User ${index + 1}: ${user.name}, confirm_join_at: "${user.confirm_join_at}"`);
            }

            tableHtml += `
    <tr class="user-row debug12345" data-organization="${user.organization || ''}" data-group="${user.parent_name || ''}" data-confirm="${user.confirm_join_at ? '1' : '0'}">
      <td>${index + 1}</td>
      <td class="for_search user_full_name">${user.title || ''} ${user.name || ''}</td>
      <td style="display: " class="for_search email_to_send1">${user.email || ''}</td>
      <td style="display: none" class="for_search">${user.phone || ''}</td>
      <td class="for_search">${user.organization || ''}</td>
      <td class="for_search">${user.parent_name || ''}</td>
      <td class="for_search">${user.note_u || user.note_eau || ''}</td>
      <td class="for_search select_lang1" style="text-align: center">${user.language || 'vi'}</td>
      <td class="for_search confirm-status" style="text-align: center" title="confirm_join_at: ${user.confirm_join_at || 'null'}">${user.confirm_join_at ? '✔' : ''}</td>
      <td class="for_search" style="text-align: center">${user.deny_join_at ? '✔' : ''}</td>
      <td class="for_search" style="text-align: center">${user.attend_at ? '✔️' : ''}</td>
    </tr>`;
        });

        // Close the table
        tableHtml += `

</table>`;

        return tableHtml;
    }



    // Helper function to format date
    function formatDate(dateString) {
        if (!dateString) return '';

        const date = new Date(dateString);

        // Check if date is valid
        if (isNaN(date.getTime())) return dateString;

        // Format as DD/MM/YYYY HH:MM
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return `${day}/${month}/${year} ${hours}:${minutes}`;
    }


    document.addEventListener('DOMContentLoaded', () => {
        // Thêm các style để modal luôn hiển thị ở vị trí cố định
        const modalStyle = document.createElement('style');
        modalStyle.innerHTML = `
                    #exampleModalCenter {
                        padding: 0 !important;
                    }

                    #exampleModalCenter .modal-dialog {
                        position: fixed !important;
                        top: 50% !important;
                        left: 50% !important;
                        transform: translate(-50%, -50%) !important;
                        margin: 0 !important;
                        max-height: 90vh;
                        width: 90%;
                        max-width: 1200px;
                    }

                    #exampleModalCenter .modal-content {
                        height: 85vh;
                        max-height: 85vh;
                        display: flex;
                        flex-direction: column;
                    }

                    #exampleModalCenter .modal-header,
                    #exampleModalCenter .modal-footer {
                        flex-shrink: 0;
                    }

                    #exampleModalCenter .modal-body {
                        overflow-y: auto;
                        flex-grow: 1;
                    }

                    /* Đảm bảo thanh cuộn chỉ xuất hiện khi cần thiết */
                    #exampleModalCenter .table-responsive {
                        overflow-y: auto;
                        max-height: 100%;
                    }
                `;
        document.head.appendChild(modalStyle);

        // Add CSS for highlighting filtered selects
        const filterStyle = document.createElement('style');
        filterStyle.innerHTML = `
            .select_all1 select.filtered-select {
                background-color: #ffdddd;
                color: #cc0000;
                border-color: #cc0000;
            }
            .select_all1 input.filtered-input {
                background-color: #ffdddd;
                color: #cc0000;
                border-color: #cc0000;
            }
        `;
        document.head.appendChild(filterStyle);

        // Xử lý sự kiện khi modal mở
        const modal = document.getElementById('exampleModalCenter');
        if (modal) {
            // Khi modal hiển thị
            modal.addEventListener('show.bs.modal', function() {
                document.body.classList.add('modal-open');

                // Tạo một observer để theo dõi thay đổi của content trong modal
                const observer = new MutationObserver(() => {
                    // Mỗi khi nội dung modal thay đổi, đảm bảo modal vẫn ở vị trí giữa
                    const modalDialog = modal.querySelector('.modal-dialog');
                    if (modalDialog) {
                        modalDialog.style.position = 'fixed';
                        modalDialog.style.top = '50%';
                        modalDialog.style.left = '50%';
                        modalDialog.style.transform = 'translate(-50%, -50%)';
                    }
                });

                // Theo dõi thay đổi trong modal-body
                const modalBody = modal.querySelector('.modal-body');
                if (modalBody) {
                    observer.observe(modalBody, {
                        childList: true,
                        subtree: true,
                        characterData: true,
                        attributes: true
                    });
                }
            });

            // Khi modal đóng
            modal.addEventListener('hidden.bs.modal', function() {
                document.body.classList.remove('modal-open');
            });
        }
    });

    // Debounce timer để tránh gọi filterUserRows nhiều lần
    let filterDebounceTimer = null;

    function debouncedFilterUserRows() {
        clearTimeout(filterDebounceTimer);
        filterDebounceTimer = setTimeout(() => {
            filterUserRows();
        }, 100); // Đợi 100ms trước khi gọi
    }

    // Hàm lọc dữ liệu kết hợp nhiều tiêu chí
    function filterUserRows() {
        console.log(" filterUserRows2 ... ");

        // Lấy giá trị tìm kiếm chung
        const generalSearchValue = document.querySelector('input.for_search')?.value?.trim().toLowerCase() || '';

        // Lấy giá trị từ các filter cụ thể
        const orgFilter = document.querySelector('.org-filter');
        const groupFilter = document.querySelector('.group-filter');
        const phoneFilter = document.querySelector('.phone_select');
        const nameFilter = document.querySelector('.name_select');
        const emailFilter = document.querySelector('.email_select');
        const noteFilter = document.querySelector('.note_select');
        const confirmFilter = document.querySelector('.confirm_join_at');
        const denyFilter = document.querySelector('.deny_join_at');
        const attendFilter = document.querySelector('.attend_at');
        const langFilter = document.querySelector('.select_lang');

        const orgText = orgFilter ? orgFilter.value.trim().toLowerCase() : '';
        const selectedGroup = groupFilter ? groupFilter.value : 'all';
        const phoneText = phoneFilter ? phoneFilter.value.trim().toLowerCase() : '';
        const nameText = nameFilter ? nameFilter.value.trim().toLowerCase() : '';
        const emailText = emailFilter ? emailFilter.value.trim().toLowerCase() : '';
        const noteText = noteFilter ? noteFilter.value.trim().toLowerCase() : '';
        const confirmValue = confirmFilter ? confirmFilter.value : '';
        const denyValue = denyFilter ? denyFilter.value : '';
        const attendValue = attendFilter ? attendFilter.value : '';
        const langValue = langFilter ? langFilter.value : '';

        // DEBUG: Log confirm filter value
        if (confirmValue !== '') {
            console.log("🔍 Filtering by confirm_join_at:", confirmValue);
        }

        const rows = document.querySelectorAll('.user-row');
        console.log("🔍 DEBUG: Total rows found:", rows.length); // ← DEBUG
        let nShow = 0;

        // Xóa highlight cũ
        document.querySelectorAll('td.for_search').forEach(td => {
            td.classList.remove('highlight');
        });

        rows.forEach(row => {
            const rowOrg = row.getAttribute('data-organization');
            const rowGroup = row.getAttribute('data-group');

            // Get cell contents
            const orgCell = row.querySelector('td:nth-child(5)');
            const phoneCell = row.querySelector('td:nth-child(4)');
            const nameCell = row.querySelector('td:nth-child(2)');
            const emailCell = row.querySelector('td:nth-child(3)');
            const noteCell = row.querySelector('td:nth-child(7)');
            const confirmCell = row.querySelector('td:nth-child(9)'); // Fixed: confirm is column 9, not 8
            const langCell = row.querySelector('td:nth-child(8)'); // Fixed: lang is column 8, not 9
            const denyCell = row.querySelector('td:nth-child(10)');
            const attendCell = row.querySelector('td:nth-child(11)');

            // Get text values from cells
            const orgValue = orgCell ? orgCell.textContent.toLowerCase() : '';
            const phoneValue = phoneCell ? phoneCell.textContent.toLowerCase() : '';
            const nameValue = nameCell ? nameCell.textContent.toLowerCase() : '';
            const emailValue = emailCell ? emailCell.textContent.toLowerCase() : '';
            const noteValue = noteCell ? noteCell.textContent.toLowerCase() : '';

            // Check if cells have checkmark (✓) for status - IMPROVED LOGIC
            // Method 1: Check via data attribute (more reliable)
            const dataConfirm = row.getAttribute('data-confirm');
            const hasConfirmFromData = dataConfirm === '1';

            // Method 2: Check via cell text content (fallback)
            const hasConfirmFromText = confirmCell ? (confirmCell.textContent.trim() === '✔' || confirmCell.textContent.trim() === '✓') : false;

            // Use data attribute as primary method, text as fallback
            const hasConfirm = hasConfirmFromData;

            const language = langCell ? (langCell.textContent.trim().toLowerCase() === 'en' ? 'en' : 'vi') : 'vi';
            const hasDeny = denyCell ? (denyCell.textContent.trim() === '✔' || denyCell.textContent.trim() === '✓') : false;
            const hasAttend = attendCell ? (attendCell.textContent.trim() === '✔️' || attendCell.textContent.trim() === '✔' || attendCell.textContent.trim() === '✓') : false;

            // DEBUG: Log confirm cell details if filtering
            if (confirmValue !== '') {
                console.log(`Row ${nShow + 1}: dataConfirm: "${dataConfirm}", hasConfirmFromData: ${hasConfirmFromData}, hasConfirmFromText: ${hasConfirmFromText}, hasConfirm: ${hasConfirm}`);
                console.log(`  confirmCell text: "${confirmCell ? confirmCell.textContent.trim() : 'NULL'}"`);
            }

            // ===== KIỂM TRA TÌM KIẾM CHUNG =====
            let matchesGeneralSearch = true;
            if (generalSearchValue) {
                const allRowText = (nameValue + ' ' + emailValue + ' ' + phoneValue + ' ' + orgValue + ' ' + noteValue).toLowerCase();
                matchesGeneralSearch = allRowText.includes(generalSearchValue);

                // Highlight matching cells nếu tìm thấy
                if (matchesGeneralSearch) {
                    [nameCell, emailCell, phoneCell, orgCell, noteCell].forEach(cell => {
                        if (cell && cell.textContent.toLowerCase().includes(generalSearchValue)) {
                            cell.classList.add('highlight');
                        }
                    });
                }
            }

            // ===== KIỂM TRA CÁC FILTER CỤ THỂ =====
            const matchesGroup = selectedGroup === 'all' || rowGroup === selectedGroup;
            const matchesOrg = !orgText || orgValue.includes(orgText);

            // Enhanced phone matching for comma-separated terms
            let matchesPhone = true;
            if (phoneText) {
                const phoneTerms = phoneText.split(',').map(term => term.trim()).filter(Boolean);
                matchesPhone = phoneTerms.length === 0 ||
                    phoneTerms.some(term => phoneValue.includes(term));
            }

            const matchesName = !nameText || nameValue.includes(nameText);

            // Enhanced email matching for comma-separated terms
            let matchesEmail = true;
            if (emailText) {
                const emailTerms = emailText.split(',').map(term => term.trim()).filter(Boolean);
                matchesEmail = emailTerms.length === 0 ||
                    emailTerms.some(term => emailValue.includes(term));
            }

            const matchesNote = !noteText || noteValue.includes(noteText);

            // Match conditions for status dropdowns - IMPROVED LOGIC
            const matchesConfirm =
                confirmValue === '' ||
                (confirmValue === '1' && hasConfirm) ||
                (confirmValue === '0' && !hasConfirm);

            const matchesLang =
                langValue === '' ||
                (langValue === language);

            const matchesDeny =
                denyValue === '' ||
                (denyValue === '1' && hasDeny) ||
                (denyValue === '0' && !hasDeny);

            const matchesAttend =
                attendValue === '' ||
                (attendValue === '1' && hasAttend) ||
                (attendValue === '0' && !hasAttend);

            // DEBUG: Log filter results if filtering by confirm
            if (confirmValue !== '') {
                console.log(`Row ${nShow + 1}: matchesConfirm: ${matchesConfirm}, confirmValue: ${confirmValue}, hasConfirm: ${hasConfirm}`);
            }

            // ===== KẾT HỢP TẤT CẢ ĐIỀU KIỆN =====
            // Show row if ALL conditions match (including general search)
            if (matchesGeneralSearch && matchesOrg && matchesGroup && matchesPhone && matchesName &&
                matchesEmail && matchesNote && matchesConfirm && matchesDeny &&
                matchesAttend && matchesLang) {
                row.style.display = ''; // Show the row
                nShow++;
            } else {
                row.style.display = 'none'; // Hide the row
            }
        });

        $(".numberOfMemberToSend").html("Đang chọn <b style='color: red'> " + nShow + " </b> thành viên");
        console.log("Number of rows shown: ", nShow);
        $("#n_user_select").text(nShow);



        // DEBUG: Summary if filtering by confirm
        if (confirmValue !== '') {
            console.log(`🔍 SUMMARY: Filtering by confirm_join_at="${confirmValue}", showing ${nShow} rows`);
        }
    }

    let dataToExportExcel = null

    function getUserDataList(dataId){

        console.log("Call getUserDataList ...");

        // return;

        showWaittingIcon();
        fetch('/api/event-info/getUserListEvent?eid=' + dataId)
            .then(response => response.json())
            .then(data => {
                console.log("Get data...", data.payload);

                dataToExportExcel = data.payload;

                hideWaittingIcon()

                // Generate the HTML table
                const htmlTable = buildHtmlTable(data.payload);

                // Populate the modal body with the fetched content
                // Only insert into ONE container to prevent duplicate tables
                const containerTableListUserAll = document.querySelector('#table_list_user_all');
                const containerUserList = document.querySelector('#exampleModalCenter .modal-body .user_list');

                if(containerTableListUserAll && containerTableListUserAll.style.display !== 'none'){
                    containerTableListUserAll.innerHTML = htmlTable;
                } else if(containerUserList && containerUserList.style.display !== 'none'){
                    containerUserList.innerHTML = htmlTable;
                } else if(containerTableListUserAll){
                    containerTableListUserAll.innerHTML = htmlTable;
                } else {
                    containerUserList.innerHTML = htmlTable;
                }

                // Thêm event listener sau khi nội dung HTML đã được thêm vào DOM
                setTimeout(function() {
                    // Remove existing event listeners to prevent duplicates
                    const existingGroupFilter = document.querySelector('.group-filter');
                    const existingOrgFilter = document.querySelector('.org-filter');
                    const existingPhoneFilter = document.querySelector('.phone_select');
                    const existingNameFilter = document.querySelector('.name_select');
                    const existingEmailFilter = document.querySelector('.email_select');
                    const existingNoteFilter = document.querySelector('.note_select');
                    const existingConfirmFilter = document.querySelector('.confirm_join_at');
                    const existingDenyFilter = document.querySelector('.deny_join_at');
                    const existingAttendFilter = document.querySelector('.attend_at');
                    const existingLangFilter = document.querySelector('.select_lang');

                    // Clone and replace elements to remove all event listeners
                    if (existingGroupFilter) {
                        const newGroupFilter = existingGroupFilter.cloneNode(true);
                        existingGroupFilter.parentNode.replaceChild(newGroupFilter, existingGroupFilter);
                    }
                    if (existingOrgFilter) {
                        const newOrgFilter = existingOrgFilter.cloneNode(true);
                        existingOrgFilter.parentNode.replaceChild(newOrgFilter, existingOrgFilter);
                    }
                    if (existingPhoneFilter) {
                        const newPhoneFilter = existingPhoneFilter.cloneNode(true);
                        existingPhoneFilter.parentNode.replaceChild(newPhoneFilter, existingPhoneFilter);
                    }
                    if (existingNameFilter) {
                        const newNameFilter = existingNameFilter.cloneNode(true);
                        existingNameFilter.parentNode.replaceChild(newNameFilter, existingNameFilter);
                    }
                    if (existingEmailFilter) {
                        const newEmailFilter = existingEmailFilter.cloneNode(true);
                        existingEmailFilter.parentNode.replaceChild(newEmailFilter, existingEmailFilter);
                    }
                    if (existingNoteFilter) {
                        const newNoteFilter = existingNoteFilter.cloneNode(true);
                        existingNoteFilter.parentNode.replaceChild(newNoteFilter, existingNoteFilter);
                    }
                    if (existingConfirmFilter) {
                        const newConfirmFilter = existingConfirmFilter.cloneNode(true);
                        existingConfirmFilter.parentNode.replaceChild(newConfirmFilter, existingConfirmFilter);
                    }
                    if (existingDenyFilter) {
                        const newDenyFilter = existingDenyFilter.cloneNode(true);
                        existingDenyFilter.parentNode.replaceChild(newDenyFilter, existingDenyFilter);
                    }
                    if (existingAttendFilter) {
                        const newAttendFilter = existingAttendFilter.cloneNode(true);
                        existingAttendFilter.parentNode.replaceChild(newAttendFilter, existingAttendFilter);
                    }
                    if (existingLangFilter) {
                        const newLangFilter = existingLangFilter.cloneNode(true);
                        existingLangFilter.parentNode.replaceChild(newLangFilter, existingLangFilter);
                    }

                    // Now add fresh event listeners
                    // // Xử lý lọc theo tổ chức
                    // const orgFilter1 = document.querySelector('.org-filter111');
                    // if (orgFilter1) {
                    //     orgFilter1.addEventListener('change', function() {
                    //         filterUserRows();
                    //         // Highlight if a filter is active
                    //         this.classList.toggle('filtered-select', this.value !== 'all');
                    //     });
                    // } else {
                    //     console.error('Cannot find .org-filter element');
                    // }

                    // Xử lý lọc theo nhóm
                    const groupFilter = document.querySelector('.group-filter');
                    if (groupFilter) {
                        groupFilter.addEventListener('change', function() {
                            filterUserRows();
                            // Highlight if a filter is active
                            this.classList.toggle('filtered-select', this.value !== 'all');
                        });
                    } else {
                        console.error('Cannot find .group-filter element');
                    }

                    const orgFilter = document.querySelector('.org-filter');
                    if (orgFilter) {
                        orgFilter.addEventListener('keyup', function() {
                            filterUserRows();
                            // Highlight if a filter is active
                            this.classList.toggle('filtered-input', this.value.trim() !== '');
                        });
                    } else {
                        console.error('Cannot find .orgFilter element');
                    }

                    // Xử lý lọc theo số điện thoại
                    const phoneFilter = document.querySelector('.phone_select');
                    if (phoneFilter) {
                        phoneFilter.addEventListener('keyup', function() {
                            filterUserRows();
                            // Highlight if a filter is active
                            this.classList.toggle('filtered-input', this.value.trim() !== '');
                        });
                    } else {
                        console.error('Cannot find .phone_select element');
                    }

                    // Xử lý lọc theo tên
                    const nameFilter = document.querySelector('.name_select');
                    if (nameFilter) {
                        nameFilter.addEventListener('keyup', function() {
                            filterUserRows();
                            // Highlight if a filter is active
                            this.classList.toggle('filtered-input', this.value.trim() !== '');
                        });
                    } else {
                        console.error('Cannot find .name_select element');
                    }

                    // Xử lý lọc theo email
                    const emailFilter = document.querySelector('.email_select');
                    if (emailFilter) {
                        emailFilter.addEventListener('keyup', function() {
                            filterUserRows();
                            // Highlight if a filter is active
                            this.classList.toggle('filtered-input', this.value.trim() !== '');
                        });
                    } else {
                        console.error('Cannot find .email_select element');
                    }

                    // Xử lý lọc theo ghi chú
                    const noteFilter = document.querySelector('.note_select');
                    if (noteFilter) {
                        noteFilter.addEventListener('keyup', function() {
                            filterUserRows();
                            // Highlight if a filter is active
                            this.classList.toggle('filtered-input', this.value.trim() !== '');
                        });
                    } else {
                        console.error('Cannot find .note_select element');
                    }

                    // Xử lý lọc theo trạng thái xác nhận
                    const confirmFilter = document.querySelector('.confirm_join_at');
                    if (confirmFilter) {
                        confirmFilter.addEventListener('change', function() {
                            filterUserRows();
                            // Highlight if a filter is active
                            this.classList.toggle('filtered-select', this.value !== '');
                        });
                    } else {
                        console.error('Cannot find .confirm_join_at element');
                    }

                    // Xử lý lọc theo trạng thái từ chối
                    const denyFilter = document.querySelector('.deny_join_at');
                    if (denyFilter) {
                        denyFilter.addEventListener('change', function() {
                            filterUserRows();
                            // Highlight if a filter is active
                            this.classList.toggle('filtered-select', this.value !== '');
                        });
                    } else {
                        console.error('Cannot find .deny_join_at element');
                    }

                    // Xử lý lọc theo trạng thái check-in
                    const attendFilter = document.querySelector('.attend_at');
                    if (attendFilter) {
                        attendFilter.addEventListener('change', function() {
                            filterUserRows();
                            // Highlight if a filter is active
                            this.classList.toggle('filtered-select', this.value !== '');
                        });
                    } else {
                        console.error('Cannot find .attend_at element');
                    }

                    // Xử lý lọc theo ngôn ngữ
                    // NOTE: existingLangFilter đã được clone ở trên (line 520)
                    const langFilter = document.querySelector('.select_lang');
                    if (langFilter) {
                        // Attach listener cho newLangFilter (đã được clone từ existingLangFilter)
                        langFilter.addEventListener('change', function() {
                            console.log('Language filter changed to:', this.value);
                            debouncedFilterUserRows(); // ← Dùng debounced version
                            // Highlight if a filter is active
                            this.classList.toggle('filtered-select', this.value !== '');
                        });
                    } else {
                        console.error('Cannot find .select_lang element');
                    }

                }, 100); // Đợi 100ms để đảm bảo DOM đã được cập nhật
            })
            .catch(error => console.error('Error fetching content:', error));
    }


    // DEBUG FUNCTION: Test filter manually in console
    window.debugConfirmFilter = function() {
        console.log("=== DEBUG CONFIRM FILTER ===");
        const confirmFilter = document.querySelector('.confirm_join_at');
        const currentValue = confirmFilter ? confirmFilter.value : 'not found';
        console.log("Current confirm filter value:", currentValue);

        const rows = document.querySelectorAll('.user-row');
        console.log("Total rows found:", rows.length);

        rows.forEach((row, index) => {
            const dataConfirm = row.getAttribute('data-confirm');
            const confirmCell = row.querySelector('.confirm-status');
            const confirmText = confirmCell ? confirmCell.textContent.trim() : '';
            const displayStyle = row.style.display;

            console.log(`Row ${index + 1}:`);
            console.log(`  data-confirm: "${dataConfirm}"`);
            console.log(`  cell text: "${confirmText}"`);
            console.log(`  display: "${displayStyle}"`);
        });

        console.log("=== END DEBUG ===");
    };

    document.addEventListener('DOMContentLoaded', () => {


        document.getElementById('exampleModalCenter')?.addEventListener('show.bs.modal', function (event) {
            // Make an AJAX request to fetch content from the API
            getUserDataList(<?php use App\Components\Helper1;echo $objData->id ?>);
        });

    });

    document.querySelector('.sub_event_zone')?.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove_ev'))
        {
            //
            let id = event.target.getAttribute("data-id");
            // let id = $(this).prop("data-id");
            console.log("Remove id = ", id);
            let url = "/api/event-info/removeSubEvent";
            let user_token = jctool.getCookie('_tglx863516839');
            showWaittingIcon();
            $.ajax({
                url: url,
                type: 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader
                },
                data: {sub_id: id},
                success: function (data, status) {
                    hideWaittingIcon();
                    console.log("Data ret2: ", data, " \nStatus: ", status);
                    if (data.code) {
                        showToastInfoTop(data.payload);

                        //Remove div
                        $(".sub_event_info[data-id='" + id + "']").remove();

                    } else {
                        alert("Có lỗi: " + JSON.stringify(data))
                    }
                    console.log("Data: ", data, " \nStatus: ", status);
                },
                error: function (data) {
                    hideWaittingIcon();
                    console.log(" DATAx ", data);
                    if (data.responseJSON && data.responseJSON.message)
                        alert('Error call api: ' + "\n" + data.responseJSON.message)
                    else
                        alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                }

            });

        }

    })
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectElement = document.getElementById('dynamic-select_child_<?php echo $objData->id ?>');

        if(!selectElement) {
            console.error('Select element with ID dynamic-select_child_<?php echo $objData->id ?> not found.');
            return;
        }

        const choices_sub_event = new Choices(selectElement, {
            shouldSort: false,
            searchEnabled: true,
            removeItemButton: true,
            placeholder: true,
            itemSelectText: 'Select',
        });

        const defaultValue = <?php  echo $objData->parent_id ?? 0 ?>; // Replace with the actual default value

        fetch('<?php echo Helper1::isMemberModule() ? '/api/member-event-info/list?soby_s1=desc&limit=30' : '/api/event-info/list?soby_s1=desc&limit=100'; ?>')
            .then(response => response.json())
            .then(data => {
                if (data.code === 1 && data.payload?.data) {
                    const options = data.payload.data.map(item => ({
                        value: item.id,
                        label: "(" + item.id + ") " + item.name,
                    }));
                    choices_sub_event.setChoices(options, 'value', 'label', true);
                    // choices_sub_event.setChoiceByValue(defaultValue); // Set the default selected option
                } else {
                    console.error('Invalid API response:', data);
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });

        selectElement.addEventListener('choice', function(event) {
            let val = event.detail.value;
            console.log('--- Option selected Child:', val);

            //Lấy thông tin html sub event
            let url = "/api/event-info/addSubEventAndGetHtml";
            let user_token = jctool.getCookie('_tglx863516839');
            showWaittingIcon();
            $.ajax({
                url: url,
                type: 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader
                },
                data: {parent_id: '<?php echo $objData->id ?>' , sub_id: val},
                success: function (data, status) {
                    hideWaittingIcon();
                    console.log("Data ret2: ", data, " \nStatus: ", status);
                    if (data.code) {

                        showToastInfoTop(data.message);

                        let sub_event_zone = document.querySelector(".sub_event_zone");
                        // sub_event_zone.insertAdjacentHTML("beforeend", data.payload);
                        sub_event_zone.insertAdjacentHTML("afterbegin", data.payload);


                        //Sau khi add xong, reset lai select
                        // After adding the new option, reset the select element to the first option
                        choices_sub_event.setChoiceByValue('0');


                    } else {
                        alert("Có lỗi: " + JSON.stringify(data))
                    }
                    console.log("Data: ", data, " \nStatus: ", status);
                },
                error: function (data) {
                    hideWaittingIcon();
                    console.log(" DATAx ", data);
                    if (data.responseJSON && data.responseJSON.message)
                        alert('Error call api: ' + "\n" + data.responseJSON.message)
                    else
                        alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                }

            });


            // document.querySelector("input.input_value_to_post[data-field='parent_id']").value = val;
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectElement = document.getElementById('dynamic-select_<?php echo $objData->id ?>');
        if(!selectElement) {
            console.error('Select element with ID dynamic-select_<?php echo $objData->id ?> not found.');
            return;
        }
        const choices = new Choices(selectElement, {
            shouldSort: false,
            searchEnabled: true,
            removeItemButton: true,
            placeholder: true,
            itemSelectText: 'Select',
        });

        const defaultValue = <?php  echo $objData->parent_id ?? 0 ?>; // Replace with the actual default value

        fetch('<?php echo Helper1::isMemberModule() ? '/api/member-event-info/list?soby_s1=desc&limit=30' : '/api/event-info/list?soby_s1=desc&limit=100'; ?>')
            .then(response => response.json())
            .then(data => {
                if (data.code === 1 && data.payload?.data) {
                    const options = data.payload.data.map(item => ({
                        value: item.id,
                        label: "(" + item.id + ") " + item.name,
                    }));
                    choices.setChoices(options, 'value', 'label', true);
                    choices.setChoiceByValue(defaultValue); // Set the default selected option
                } else {
                    console.error('Invalid API response:', data);
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });

        selectElement.addEventListener('choice', function(event) {
            let val = event.detail.value;
            console.log('--- Option selected:', val);



            document.querySelector("input.input_value_to_post[data-field='parent_id']").value = val;
        });
    });
</script>

<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

<script>

    document.addEventListener('DOMContentLoaded', function () {


        $("#btn_chanel_save").on("click", function () {
            let chanel_name = $("#chanel_name").val();
            console.log(" VAL = ", chanel_name);
            let user_token = jctool.getCookie('_tglx863516839');
            let url = "/api/event-info/saveEventChannel";
            showWaittingIcon();
            $.ajax({
                url: url,
                type: 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                },
                data: {chanel_name: chanel_name},
                success: function (data, status) {
                    hideWaittingIcon();
                    console.log("Data ret2: ", data, " \nStatus: ", status);
                    if (data.code) {
                        showToastInfoTop(data.payload)
                    } else {
                        alert("Có lỗi: " + JSON.stringify(data))
                    }
                    console.log("Data: ", data, " \nStatus: ", status);
                },
                error: function (data) {
                    hideWaittingIcon();
                    console.log(" DATAx ", data);
                    if (data.responseJSON && data.responseJSON.message)
                        alert('Error call api: ' + "\n" + data.responseJSON.message)
                    else
                        alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                }
            });
        })

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;
        var pusher = new Pusher('e2d3c27e21727e9f9804', {
            cluster: 'ap1'
        });
        var channel = pusher.subscribe("<?php echo EventInfo_Meta::getEventChanelName() ?>");
        channel.bind('my-event-pusher-web-<?php echo $objData->id ?? '' ?>', function (data) {
            //alert(JSON.stringify(data));
            console.log("Data = ", data);

            // document.getElementById('cont').insertAdjacentHTML("afterend","<br> Add text: " + data.message);
            // document.getElementById('event_send_status_' + data.event_id).innerHTML = data.message;
            //Get by Class
            document.querySelectorAll('.event_send_status_' + data.event_id).forEach(function(elm){
                elm.innerHTML = data.message;
            });

        });

    });

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        $(".action_zone2").on('click', function () {
            $(".action_zone21").toggle();
        });
        $(".action_zone1").on('click', function () {
            $(".action_zone11").toggle();
        });

        $("#sendTin3").on('click', function () {
            console.log(" Click sendTin3 ...");

            //confirm js
            if (!confirm("Bạn có chắc chắn muốn gửi tin cho tất cả thành viên đã chọn không?\n\n" +
                "- Kiểm tra kỹ danh sách và nội dung để tránh gửi nhầm, lặp lại...\n- Các tin sẽ được gửi tuần tự\n- Mỗi email/sms có thể cần vài giây để hoàn thành việc gửi\n" +
                "- Theo dõi trạng thái gửi tin ở thông tin sự kiện này Hoặc trong phần Log của từng 'Lệnh gửi tin'\n\n" +
                "Bấm 'OK' để gửi, hoặc 'Huỷ' để bỏ qua!")){
                return;
            }

            //Tim tat ca email trong td.email_to_send1
            let emailList = '';
            $(".email_to_send1").each(function () {
                //Các TD cha của nó phải là không là display none
                if ($(this).closest('tr').css('display') == 'none') {
                    return;
                }
                emailList += $(this).text() + ',';
            });

            console.log("emailList = ", emailList);

            //get option of message_content_send
            let message_field_send = $("#message_field_send").val();

            let typeX = null
            if(message_field_send.startsWith('sms')){
                typeX = 'sms'
            }
            if(message_field_send.startsWith('content')){
                typeX = 'email'
            }

            if(!typeX){
                alert("Chưa chọn loại tin gửi, hãy chọn loại tin gửi trước khi gửi!")
                return;
            }

            sendTinJs(<?php echo $objData->id ?>, typeX, message_field_send, emailList);

        })

        $(".sync_sms").on('click', function () {
            let evid = $(this).attr("data-ev-id");
            console.log("sync_sms tin ...", evid);
            // return;
            let user_token = jctool.getCookie('_tglx863516839');
            let url = "/api/event-info/syncSms?cmd=sync_sms_request&evid=" + evid;
            showWaittingIcon();
            $.ajax({
                url: url,
                type: 'GET',
                async: false,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                },
                success: function (data, status) {
                    hideWaittingIcon();
                    console.log("Data ret3: ", data, " \nStatus: ", status);
                    if (data.code) {
                        showToastInfoTop(data.payload)
                    } else {
                        alert("Có lỗi: " + JSON.stringify(data))
                    }
                },
                error: function (data) {
                    hideWaittingIcon();
                    console.log(" DATAx ", data);
                    if (data.responseJSON && data.responseJSON.message)
                        alert('Error call api: ' + "\n" + data.responseJSON.message)
                    else
                        alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                }
            });
        })


        $(".select_user_type").on('change', function () {
            console.log(" VAL = ", $(this).val());
            if ($(this).val() == 'only_list_user') {
                console.log(" Val = only_list_user");


            }
        })

    async function sendEventInfo(event_id, typeX, select_content, user_email_send_override, select_user_type) {
        const user_token = jctool.getCookie('_tglx863516839');
        const url = "/api/event-info/sendTinAll";
        let dataPost = {
            event_id,
            typeX,
            select_content,
            user_email_send_override,
            select_user_type
        };

        console.log("Data send: ", dataPost);

        try {

            showWaittingIcon();
            try {
                // First API call
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${user_token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dataPost)
                });

                const data = await response.json();
                console.log("Data returned: ", data);


                // Handle response for first call
                if (data.payload === -1) {
                    const confirmSend = confirm(
                        "Lệnh Gửi tin tương tự đã được thực hiện và đã đánh dấu hoàn thành trước đây, bạn muốn gửi lại không?\n\n" +
                        "- Chú ý: Việc này có thể sẽ GỬI LẠI cho các user đã nhận tin rồi."
                    );

                    if (confirmSend) {
                        showWaittingIcon();
                        try {
                            dataPost.force_send = 1;

                            // Second API call (forced send)
                            const forceResponse = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Authorization': `Bearer ${user_token}`,
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(dataPost)
                            });

                            const forceData = await forceResponse.json();

                            console.log("forceData = " , forceData);

                            $('#previewMessage').modal('hide');

                            if(!forceData || !forceData.message){
                                alert("Có lỗi: " + JSON.stringify(forceData));
                            }
                            else{
                                alert(forceData.message);
                            }

                            return forceData; // Nếu cần trả về dữ liệu
                        } catch (error) {
                            console.error("Error during forced API call:", error);
                            throw error; // Ném lỗi để xử lý ở cấp cao hơn nếu cần
                        } finally {
                            hideWaittingIcon(); // Sẽ luôn chạy, dù thành công hay thất bại
                        }
                        // const forceData = await forceResponse.json();
                        // console.log("Force send data: ", forceData);
                        //
                        // hideWaittingIcon();
                        //
                        // if (forceData.message) {
                        //     showToastInfoTop(forceData.message, '', 10000);
                        //     if (forceData.payload === 2 || forceData.payload === 3) {
                        //         sendSmsAll(wsGlobal);
                        //     }
                        // } else {
                        //     alert("Có lỗi: " + JSON.stringify(forceData));
                        // }
                    }
                } else if (data.message) {
                    if (data.payload === 2 || data.payload === 3) {
                        // sendSmsAll(wsGlobal);
                    }
                    alert(data.message);
                } else {
                    alert("Có lỗi: " + JSON.stringify(data));
                }

            } catch (error) {
                console.error("Error during API call:", error);
                throw error; // Ném lỗi để xử lý ở cấp cao hơn nếu cần
            } finally {
                hideWaittingIcon(); // Luôn chạy dù thành công hay thất bại
            }


        } catch (error) {
            console.log("Error: ", error);

            if (error.response && error.response.json) {
                const errorData = await error.response.json();
                alert("Error call api: " + errorData.message);
            } else {
                alert("Error call api: " + url + "\n\n" + JSON.stringify(error).substr(0, 1000));
            }
        }
        finally {
            hideWaittingIcon();

        }
    }

    function sendTinJs(event_id, typeX, select_content, user_email_send_override){

        console.log("sendTinJs ...", event_id, typeX, select_content);

        let select_user_type = $('.select_user_type[data-ev-id=' + event_id + ']').val();
        ;
        console.log("select_user_type = ", select_user_type);



        if (user_email_send_override) {
            // if (!confirm("Bạn sẽ gửi mail cho danh sách sau:\n" + user_email_send_override)) {
            //     return;
            // }
        }

        console.log(" select_mail_content Type = ", select_content);
        let contentVi = '';
        let contentEn = '';
        let typeCont = '';
        if (select_content.startsWith('sms_content')) {

            if (!confirm(`** Chú ý: Gửi SMS cần đề phòng gửi nhiều tin trùng lặp, tốn chi phí, và có thể coi là SPAM và bị chặn ...
- Lệnh gửi trên web sẽ đưa các tin nhắn vào hàng đợi, và sẽ chờ được gửi từ App trên Android
 `)) {
                return;
            }

            typeCont = "SMS";
//Kiem tra xem content Rong thi bao loi:
            //let content = tinymce.get(this.id).getContent()
            let idContVi = "edit_text_area_" + select_content;
            let idConEn = "edit_text_area_" + select_content + "_en";
            contentVi = document.getElementById(idContVi).value;
            contentEn = document.getElementById(idConEn).value;

        } else {
            typeCont = "Mail";
            //Kiem tra xem content Rong thi bao loi:
            //let content = tinymce.get(this.id).getContent()
            let idContVi = "edit_rich_text_" + select_content;
            let idConEn = "edit_rich_text_" + select_content + "_en";
            contentVi = tinymce.get(idContVi)?.getContent();
            contentEn = tinymce.get(idConEn)?.getContent();
        }

        // Bỏ qua vì đã có chỉ định gửi cho user cụ thể, content cụ thể
        if(0){
            console.log(" contentVi = ", contentVi);
            console.log(" contentEn = ", contentEn);

            if (contentEn.length == 0 && contentVi.length == 0) {
                alert(`Có lỗi:\nChưa có Nội dung ${typeCont} ${select_content} Tiếng Anh và Việt, Bạn hãy nhập nội dung trước khi gửi!`);
                return;
            }
            if (contentEn.length == 0 && contentVi.length > 0) {
                if (!confirm(`* Cảnh báo *\nNội dung ${typeCont} ${select_content} Tiếng Việt đã có và sẽ gửi đi
    nhưng Chưa có Nội dung ${typeCont} ${select_content} Tiếng Anh dành cho các User Tiếng Anh
    Việc gửi với nội dung chưa có này sẽ bị bỏ qua!
    \nBấm OK để vẫn thực hiện gửi nội dung Tiếng Việt cho các user Tiếng Việt, và bỏ qua nội dung với các User Tiếng Anh?`)) {
                    return;
                }
            }
            if (contentVi.length == 0 && contentEn.length > 0) {
                if (!confirm(`* Cảnh báo *\nNội dung ${typeCont} ${select_content} Tiếng Anh đã có và sẽ gửi đi
    nhưng Chưa có Nội dung ${typeCont} ${select_content} Tiếng Việt dành cho các User Tiếng Việt,
    Việc gửi với nội dung chưa có này sẽ bị bỏ qua!
    \nBấm OK để vẫn thực hiện gửi nội dung Tiếng Anh cho các user Tiếng Anh, và bỏ qua nội dung với các User Tiếng Việt?`)) {
                    return;
                }
            }
        }

        sendEventInfo(event_id, typeX, select_content, user_email_send_override, select_user_type);

/*
        let user_token = jctool.getCookie('_tglx863516839');

        let url = "/api/event-info/sendTinAll";
        let dataPost = {
            event_id: event_id,
            typeX: typeX,
            select_content: select_content,
            user_email_send_override: user_email_send_override,
            select_user_type: select_user_type
        };
        console.log("Data send: ", dataPost);

        // alert(" Bạn đợi ít phút và click lại!");
        // return;

        showWaittingIcon()
        $.ajax({
            url: url,
            type: 'POST',
            data: dataPost,
            async: false,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
            },
            success: function (data, status) {
                hideWaittingIcon();
                console.log("Data ret4: ", data, " \nStatus: ", status);
                showWaittingIcon()
                if (data.payload == -1) {
                    if (confirm("Lệnh Gửi tin tương tự đã được thực hiện và đã đánh dấu hoàn thành trước đây, bạn muốn gửi lại không?\n\n" +
                        "- Chú ý: Việc này có thể sẽ GỬI LẠI cho các user đã nhận tin rồi.")) {
                        dataPost.force_send = 1;
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: dataPost,
                            async: false, //Taij sao lai async nay
                            beforeSend: function (xhr) {
                                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                            },
                            success: function (data, status) {
                                hideWaittingIcon();
                                console.log("Data ret5: ", data, " \nStatus: ", status);

                                if (data.message) {
                                    showToastInfoTop(data.message, '', 10000);
                                    if(data.payload == 2 ||  data.payload == 3){
                                        sendSmsAll(wsGlobal);
                                    }

                                } else {
                                    alert("Có lỗi: " + JSON.stringify(data))
                                }
                            },
                            error: function (data) {
                                hideWaittingIcon();
                                console.log(" DATAx ", data);
                                if (data.responseJSON && data.responseJSON.message)
                                    alert("Error call api: " + data.responseJSON.message)
                                else
                                    alert("Error call api: " + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                            }
                        })
                    }
                } else if (data.message) {

                    if(data.payload == 2 ||  data.payload == 3){
                        sendSmsAll(wsGlobal);
                    }

                    alert(data.message);


                } else {
                    alert("Có lỗi: " + JSON.stringify(data))
                }
            },
            error: function (data) {
                hideWaittingIcon();
                console.log(" DATAx ", data);
                if (data.responseJSON && data.responseJSON.message)
                    alert("Error call api: " + data.responseJSON.message)
                else
                    alert("Error call api: " + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
            }


        });
    */
    }



    $(".send_tin").on('click', function () {
        console.log("Send tin ...");

        clsTableMngJs.saveOneDataTable(true, false);

        let event_id = $(this).parents('.action_event').data('id');

        let typeX = '';
        //only_list_user
        let select_content = '';
        if ($(this).hasClass("email")) {
            typeX = 'email';
            select_content = $('.select_mail_content[data-ev-id=' + event_id + ']').val();
        }
        if ($(this).hasClass("sms")) {
            typeX = 'sms';
            select_content = $('.select_sms_content[data-ev-id=' + event_id + ']').val();
        }

        let user_email_send_override = $("#user_email_send_override").val();

        sendTinJs(event_id, typeX, select_content, user_email_send_override);

    })

    $(".stop_send_tin").on('click', function () {
        console.log("stop Send tin ...");

        let event_id = $(this).parents('.action_event').data('id');
        let user_token = jctool.getCookie('_tglx863516839');

        let url = "/api/event-info/stopSendTinAll";

        $.ajax({
            url: url,
            type: 'POST',
            data: {event_id: event_id},
            async: false,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
            },
            success: function (data, status) {
                hideWaittingIcon();
                console.log("Data ret6: ", data, " \nStatus: ", status);

                if (data.payload) {
                    showToastInfoTop(data.payload);
                } else {
                    alert("Có lỗi: " + JSON.stringify(data))
                }
            },
            error: function (data) {
                hideWaittingIcon();
                console.log(" DATAx ", data);
                if (data.responseJSON && data.responseJSON.message)
                    alert("Error call api: " + data.responseJSON.message)
                else
                    alert("Error call api: " + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
            }
        });

    })
    })

</script>

<script>
    var  wsGlobal = null;
    let countReconnect = 0;

    function addLogGood(log) {
        if(!document.getElementById('status_app'))
            return;
        document.getElementById('status_app').innerHTML = log;
        document.getElementById('status_app').style.color = " Green ";

    }

    function addLogError(log) {
        if(!document.getElementById('status_app'))
            return;
        document.getElementById('status_app').innerHTML = "" + log;
        document.getElementById('status_app').style.color = " RED ";
        // document.getElementById('status_app').style.fontWeight = " bold ";
        // document.getElementById('status_app').style.fontSize = " 120% ";

    }


    //Khong dung socket
    function sendSmsAll(socket){

        return;
        // let user_token = jctool.getCookie('_tglx863516839');
        // const wsServerUrl = 'wss://events.dav.edu.vn:51111?tkx=' + user_token;
        // Kết nối tới WebSocket server
        // const socket = new WebSocket(wsServerUrl);
        // Lắng nghe sự kiện mở kết nối
        socket.onopen = () => {
            console.log('Connected to WebSocket server');
        };
        // Lắng nghe khi nhận tin nhắn từ server
        socket.onmessage = (event) => {
            console.log(`Server: ${event.data}`);
            // const messagesDiv = document.getElementById('messages');
            // const message = document.createElement('div');
            // message.textContent = `Server: ${event.data}`;
            // messagesDiv.appendChild(message);
        };

        // Lắng nghe khi kết nối bị đóng
        socket.onclose = () => {
            console.log('Disconnected from WebSocket server');
        };

        // Lắng nghe lỗi
        socket.onerror = (error) => {
            console.error('WebSocket error:', error);
        };

        // Gửi tin nhắn
        if (socket.readyState === WebSocket.OPEN) {
            // socket.send("send_all_sms_events_in_back_ground");
            // alert ('Lệnh đã gửi xuống App SMS');
            console.log(`Message sent`);
        } else {
            alert ('Socket is not open ?');
            console.log('Socket is not open?');
        }

    }

    window.addEventListener('load', function (e) {
        console.log("onload2");
    });


    // if(0) //Tam thoi disable websocket, de debug: 2.4.25
    window.addEventListener('load', function (e) {

        console.log("onload1 ...");
        let token = jctool.getCookie('_tglx863516839');
        const connectWebSocket = () => {
            countReconnect++;

            try {

            wsGlobal = new WebSocket('wss://<?php echo UrlHelper1::getDomainHostName() ?>:51111?tkx=' + token);

            }
            catch (e) {
                console.log(" *** Error: ", e);
                return;
            }

            wsGlobal.onopen = function () {
                console.log('Connected to WebSocket server');
                addLogError('Đang kiểm tra kết nối App SMS Gateway... ');
                // statusIcon.classList.remove('blinking-red');
                // statusIcon.classList.add('blinking-green');
            };

            wsGlobal.onmessage = function (event) {
                console.log('Message from server: ', event.data);


                if (event.data.includes('ping_check_alive_mobile:not_alive')) {
                    addLogError("Chưa gửi được SMS. Cần liên hệ Admin để bật APP gửi SMS!");
                    return;
                }

                //nếu event.data có chứa chuỗi: 'ping_check_alive_mobile:' + '<?php echo getCurrentUserId() ?>'
                //thì là có kết nối
                if (event.data.includes('ping_check_alive_mobile:ok_alive:')) {
                    //ping_check_alive_mobile:ok_alive lấy
                    let uidInstring = event.data.replace('ping_check_alive_mobile:ok_alive:', '');
                    //addLogGood('App Gửi SMS đang kết nối với Tài khoản của bạn: <b> "<?php echo getCurrentUserEmail() ?>"');
                    addLogGood(`App SMS sẵn sàng (Logged in: <b> ${uidInstring}</b>)`);
                    return;
                }

            };

            wsGlobal.onclose = function () {
                setTimeout(connectWebSocket, 2000); // Attempt to reconnect after 2 seconds
            };

            wsGlobal.onerror = function (error) {
                console.log('WebSocket error: ');
                addLogError('Chưa gửi được SMS. Cần liên hệ Admin để bật APP gửi SMS (Status Server chưa sẵn sàng)')
                wsGlobal.close();
            };
        };


        connectWebSocket();

        //Vòng lặp timeout 1 giây, ping đến server gửi 1 tin nhắn check một client khác có tồn tại không

        setInterval(function () {
            if (wsGlobal.readyState === WebSocket.OPEN) {
                console.log(" CUID = " + '<?php echo getCurrentUserId() ?>');
                wsGlobal.send('ping_check_alive_mobile:' + '<?php echo getCurrentUserId() ?>');
            }
        }, 3000);

    });

    window.addEventListener('load', function (e) {

        console.log("onload0 ...");

        // checkStatusWebSocketWithThisUserAccount()

        // $("#status_app").html(" ...  ");

        // $("textarea[name^='sms_content']").each(function () {
        //     var text = this.value;
        //     var byteCount = new Blob([text]).size;
        //     console.log('Byte count:', byteCount);
        //     let field = this.name;
        //     $("div[data-namex2='" + field + "']").html("Number char: " + byteCount + " / " + text.length);
        // })
        //
        // $("textarea[name^='sms_content']").on('click change keyup', function (e) {
        //     var text = e.target.value;
        //     var byteCount = new Blob([text]).size;
        //     console.log('Byte count:', byteCount, text.size);
        //     let field = e.target.name;
        //     $("div[data-namex2='" + field + "']").html("Number char: " + byteCount + " / " + text.length);
        // })
    });


    document.addEventListener('DOMContentLoaded', function () {
        $("#send_mail_test").on("click", function () {

            console.log(" send_mail_test ...");

            let testId = '<?php echo request('id') ?>';
            let user_token = jctool.getCookie('_tglx863516839');

            showWaittingIcon();
            let url = "/api/event-info/sendMailTest";
            url = "/api/event-info/sendMailTest";
            $.ajax({
                url: url,
                type: 'POST',
                data: {testId: testId},
                async: false,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                },
                success: function (data, status) {
                    hideWaittingIcon();
                    console.log("Data ret11: ", data, " \nStatus: ", status);

                    if (data.payload) {
                        showToastInfoTop(data.payload, '', 10000);
                    } else {
                        alert("Có lỗi: " + JSON.stringify(data))
                    }
                },
                error: function (data) {
                    hideWaittingIcon();
                    console.log(" DATAx ", data);
                    if (data.responseJSON && data.responseJSON.message)
                        alert('Error call api: ' + data.responseJSON.message)
                    else
                        alert('Error call api: ' + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                }
            });


        })
    })

// Handle sendTin2 button click to show preview modal
    document.addEventListener('DOMContentLoaded', function () {
    $('#sendTin2').on('click', function() {

        //Kiem tra message_content_send đã bấm select chưa
        //Nếu chưa thì yeeu cầu bấm select trước
        let select_content = $('#message_field_send').val();

        console.log(" message_field_send ...",select_content);
        if (!select_content || select_content == '0') {
            alert('Vui lòng chọn nội dung Tin nhắn trước khi gửi');
            return;
        }

        //Gọi API lấy nội dung tin nhắn xuống
        let user_token = jctool.getCookie('_tglx863516839');

        //get id from current url, number after /edit/ ...: event-info/edit/<id>?....
        let curl = window.location.pathname;
        let id = curl.split('/').pop();
        //If have ? then split again
        if (id.includes('?')) {
            id = id.split('?')[0];
        }
        console.log(" ID = ", id);
//Lay text cua .select_lang1 dau tien, có nhiều .select_lang1
        let lang1 = $('.user-row:visible:first .select_lang1').text();
        console.log(" lang111 = ", lang1);

        let url = "/api/event-info/get/"+id + "?cmd_ev=get_content_preview&lang1=" + lang1;
        showWaittingIcon();
        //fetch lấy nội dung
        $.ajax({
            url: url,
            type: 'GET',
            // data: {content_id: select_content},
            async: false,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
            },
            success: function (data, status) {
                hideWaittingIcon();
                console.log("Data ret12: ", data, " \nStatus: ", status);


                if (data.payload) {
                    //Hiển thị nội dung tin nhắn lên modal

                    let contentx = data.payload[select_content];
                    if(!contentx || contentx.length == 0){
                        alert("Nội dung tiếng Việt chưa có? Bạn hãy tạo nội dung trước khi gửi tin: " + select_content)
                        return;
                    }

                    let contentx2 = data.payload[select_content + '_en'];
                    if(select_content !='content4' && select_content !='content5')
                    if(!contentx2 || contentx2.length == 0){
                        alert("Nội dung tiếng Anh chưa có? Bạn hãy tạo nội dung trước khi gửi tin: " + select_content)
                        return;
                    }


                    if(lang1 == 'en'){
                        contentx = contentx2;
                    }

                    contentx = contentx.trim()
                    if(select_content !='content4' && select_content !='content5')
                    if(!contentx || contentx.length == 0){
                        alert("Nội dung chưa có? Bạn hãy tạo nội dung trước khi gửi tin: " + select_content)
                        return;
                    }


                    //if select_content start with: sms_content
                    if (select_content.startsWith('sms_content')){
                        //Thay the xuong dong (\n) bang Br
                        contentx = contentx.replace(/\n/g, "<br>");
                    }

                    let tmp = $('.user-row:visible:first .user_full_name').text();
                    //Lay text cua .user_full_name dau tien, có nhiều .user_full_name
                    contentx = contentx.replaceAll("[TENKHACH]", tmp);

                    //Lâấy ra input có data-field=name
                    let nameEvent = $('input[data-field=name]').val();
                    console.log(" nameEvent = ", nameEvent);
                    contentx = contentx.replaceAll("[EVENT_NAME]", nameEvent);

                    contentx = contentx.replaceAll("[LINKTHAMDU]", "<a href='https://events.dav.edu.vn/' target='_blank'> https://events.dav.edu.vn/... </a>");


                    $('.content_send_preview').html(contentx);

                    //Hiển thị modal
                    $('#previewMessage').modal('show');
                } else {
                    alert("Có lỗi: " + JSON.stringify(data))
                }
            },
            error: function (data) {
                hideWaittingIcon();
                console.log(" DATAx ", data);
                if (data.responseJSON && data.responseJSON.message)
                    alert('Error call api: ' + data.responseJSON.message)
                else
                    alert('Error call api: ' + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
            }
        });


        // // Get selected content and users here
        // let selectedContent = $('.modal-footer select').val();
        // let userCount = $('#numberOfMemberToSend').text();
        //
        // // Populate preview content - you can customize this based on your needs
        // $('.content_send_preview').html(`
        //     <div class="">
        //         <p><strong>Nội dung:</strong> ${selectedContent}</p>
        //         <p><strong>Số người nhận:</strong> ${userCount}</p>
        //         <p>Xác nhận gửi tin đến các đại biểu đã chọn?</p>
        //     </div>
        // `);

        // Hide current modal and show preview modal
        // $('#exampleModalCenter').modal('hide');
        // $('#previewMessage').modal('show');
    });

    // Make sure preview modal shows on top when needed
    $('#previewMessage').on('shown.bs.modal', function() {
        $(this).css('z-index', 1060); // Higher z-index than the first modal
    });
});
</script>
