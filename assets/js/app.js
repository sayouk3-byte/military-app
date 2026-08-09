/**
 * កម្មវិធីគ្រប់គ្រងទិន្នន័យនាយទាហាន (Military Personnel Management System)
 * JavaScript Interactive Application Engine & Mobile Image Compressor
 */

document.addEventListener('DOMContentLoaded', () => {
    // App State Variables
    let personnelData = [];
    let selectedPersonnelId = null;

    // DOM Form Controls
    const formFields = [
        'id', 'manual_id', 'rank', 'surname', 'given_name', 'gender', 'id_card',
        'position', 'unit_group', 'unit', 'rank_date', 'position_date',
        'dob', 'enlistment_date', 'education_level', 'study_local', 'study_abroad',
        'children_count', 'black_card_expiry', 'blue_card_expiry',
        'pob_village', 'pob_commune', 'pob_district', 'pob_province',
        'addr_house', 'addr_group', 'addr_village', 'addr_commune', 'addr_district', 'addr_province',
        'notes', 'phone', 'family_name'
    ];

    // Element References
    const tableBody = document.getElementById('personnelTableBody');
    const dobInput = document.getElementById('dob');
    const ageDisplay = document.getElementById('ageDisplay');
    const statusMessage = document.getElementById('statusMessage');

    // Photo Elements
    const photoImg = document.getElementById('photoImg');
    const photoDashedLabel = document.getElementById('photoDashedLabel');
    const familyPhotoImg = document.getElementById('familyPhotoImg');
    const familyDashedLabel = document.getElementById('familyDashedLabel');

    const DEFAULT_AVATAR = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='105' height='130' viewBox='0 0 105 130'><rect width='105' height='130' fill='%231e293b'/><rect x='4' y='4' width='97' height='122' fill='%230f2537' stroke='%23d4af37' stroke-width='1.5'/><circle cx='52.5' cy='46' r='22' fill='%23d4af37'/><path d='M22,108 C22,78 83,78 83,108 Z' fill='%23d4af37'/><text x='52.5' y='120' font-family='sans-serif' font-size='10' font-weight='bold' fill='%23ffffff' text-anchor='middle'>4x6</text></svg>";

    // Buttons
    const btnAdd = document.getElementById('btnAdd');
    const btnUpdate = document.getElementById('btnUpdate');
    const btnDelete = document.getElementById('btnDelete');
    const btnClear = document.getElementById('btnClear');
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const btnSearchId = document.getElementById('btnSearchId');
    const btnSearchName = document.getElementById('btnSearchName');
    const searchIdInput = document.getElementById('searchIdInput');
    const searchNameInput = document.getElementById('searchNameInput');

    // Mobile FAB Buttons
    const fabPrev = document.getElementById('fabPrev');
    const fabSave = document.getElementById('fabSave');
    const fabNext = document.getElementById('fabNext');
    const fabScroll = document.getElementById('fabScroll');

    // Register Service Worker for PWA Mobile Support
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('./sw.js')
            .then(reg => console.log('[PWA] ServiceWorker Registered:', reg.scope))
            .catch(err => console.log('[PWA] ServiceWorker Failed:', err));
    }

    const blackCardInput = document.getElementById('black_card_expiry');
    const blueCardInput = document.getElementById('blue_card_expiry');

    // Register Service Worker for PWA Mobile Support
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('./sw.js')
            .then(reg => console.log('[PWA] ServiceWorker Registered:', reg.scope))
            .catch(err => console.log('[PWA] ServiceWorker Failed:', err));
    }

    // Event Listeners
    if (dobInput) {
        dobInput.addEventListener('input', updateAgeDisplay);
        dobInput.addEventListener('change', updateAgeDisplay);
    }

    [blackCardInput, blueCardInput].forEach(inp => {
        if (inp) {
            inp.addEventListener('input', checkExpiryDates);
            inp.addEventListener('change', checkExpiryDates);
            inp.addEventListener('keyup', checkExpiryDates);
        }
    });

    if (btnAdd) btnAdd.addEventListener('click', handleAdd);
    if (btnUpdate) btnUpdate.addEventListener('click', handleUpdate);
    if (btnDelete) btnDelete.addEventListener('click', handleDelete);
    if (btnClear) btnClear.addEventListener('click', clearForm);

    function navigateRecord(direction) {
        if (!personnelData || personnelData.length === 0) return;
        if (!selectedPersonnelId) {
            const firstTr = tableBody.querySelector('tr');
            selectRow(personnelData[0], firstTr);
            return;
        }

        let currentIndex = personnelData.findIndex(p => String(p.id) === String(selectedPersonnelId));
        if (currentIndex === -1) currentIndex = 0;

        let targetIndex = currentIndex + direction;
        if (targetIndex < 0) {
            targetIndex = 0;
            setStatus('បានដល់ទិន្នន័យដំបូងគេហើយ!');
        } else if (targetIndex >= personnelData.length) {
            targetIndex = personnelData.length - 1;
            setStatus('បានដល់ទិន្នន័យចុងក្រោយគេហើយ!');
        }

        const targetPersonnel = personnelData[targetIndex];
        const targetTr = tableBody.querySelector(`tr[data-id="${targetPersonnel.id}"]`);
        selectRow(targetPersonnel, targetTr);
    }

    if (btnPrev) btnPrev.addEventListener('click', () => navigateRecord(-1));
    if (btnNext) btnNext.addEventListener('click', () => navigateRecord(1));
    if (fabPrev) fabPrev.addEventListener('click', () => navigateRecord(-1));
    if (fabNext) fabNext.addEventListener('click', () => navigateRecord(1));
    if (fabSave) fabSave.addEventListener('click', handleUpdate);

    let isScrolledToForm = true;
    if (fabScroll) {
        fabScroll.addEventListener('click', () => {
            if (isScrolledToForm) {
                const tableCard = document.querySelector('.grid-table-card');
                if (tableCard) tableCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                isScrolledToForm = false;
            } else {
                const formCard = document.querySelector('.input-form-card');
                if (formCard) formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                isScrolledToForm = true;
            }
        });
    }

    if (btnSearchId) btnSearchId.addEventListener('click', () => loadPersonnel({ search_id: searchIdInput.value.trim() }));
    if (btnSearchName) btnSearchName.addEventListener('click', () => loadPersonnel({ search_name: searchNameInput.value.trim() }));

    if (searchIdInput) {
        searchIdInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') loadPersonnel({ search_id: searchIdInput.value.trim() });
        });
    }

    if (searchNameInput) {
        searchNameInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') loadPersonnel({ search_name: searchNameInput.value.trim() });
        });
    }

    // ----------------------------------------------------
    // 1. Data Fetch & Render
    // ----------------------------------------------------
    async function loadPersonnel(filters = {}) {
        setStatus('កំពុងផ្ទុកទិន្នន័យ...');
        
        const params = new URLSearchParams({ action: 'fetch_all', ...filters });

        try {
            const res = await fetch(`api.php?${params.toString()}`);
            const result = await res.json();

            if (result.success) {
                personnelData = result.data || [];
                try {
                    localStorage.setItem('military_personnel_cache', JSON.stringify(personnelData));
                } catch (e) {}
                renderTable(personnelData);
                setStatus(`ទិន្នន័យត្រូវបានផ្ទុកជោគជ័យ សរុប ${personnelData.length} នាក់`);
            } else {
                setStatus(`មានបញ្ហា: ${result.message}`);
            }
        } catch (err) {
            console.log('Offline/Local fallback triggered');
            const cached = localStorage.getItem('military_personnel_cache');
            if (cached) {
                try {
                    personnelData = JSON.parse(cached);
                } catch(e) {}
            }
            renderTable(personnelData);
            setStatus(`ដំណើរការលើទូរស័ព្ទ (Mobile Offline Mode) - សរុប ${personnelData.length} នាក់`);
        }
    }

    function renderTable(data) {
        tableBody.innerHTML = '';

        if (!data || data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="14" class="text-center" style="padding: 20px; color: #64748b;">
                        ពុំមានទិន្នន័យនាយទាហានឡើយ
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach((p, idx) => {
            const tr = document.createElement('tr');
            tr.dataset.id = p.id;

            if (selectedPersonnelId && String(p.id) === String(selectedPersonnelId)) {
                tr.classList.add('selected-row');
            }

            tr.innerHTML = `
                <td>${p.id || (idx + 1)}</td>
                <td>${p.manual_id || ''}</td>
                <td>${p.rank || ''}</td>
                <td>${p.surname || ''}</td>
                <td>${p.given_name || p.name_khmer || ''}</td>
                <td>${p.gender || ''}</td>
                <td>${p.id_card || ''}</td>
                <td>${p.position || ''}</td>
                <td>${p.unit_group || ''}</td>
                <td>${p.unit || ''}</td>
                <td>${formatDisplayDate(p.dob)}</td>
                <td>${formatDisplayDate(p.enlistment_date)}</td>
                <td>${formatDisplayDate(p.rank_date)}</td>
                <td>${formatDisplayDate(p.position_date)}</td>
            `;

            tr.addEventListener('click', () => selectRow(p, tr));
            tableBody.appendChild(tr);
        });

        // Auto-select first row if none selected
        if (!selectedPersonnelId && data.length > 0) {
            const firstRow = tableBody.querySelector('tr');
            if (firstRow) selectRow(data[0], firstRow);
        }
    }

    function selectRow(personnel, trElement) {
        selectedPersonnelId = personnel.id;

        // Update highlight
        const allRows = tableBody.querySelectorAll('tr');
        allRows.forEach(r => r.classList.remove('selected-row', 'selected'));
        if (trElement) trElement.classList.add('selected-row');

        // Populate Form
        formFields.forEach(field => {
            const el = document.getElementById(field);
            if (!el) return;

            if (el.type === 'checkbox') {
                el.checked = Boolean(personnel[field]);
            } else {
                let val = personnel[field] || '';
                if (['dob', 'enlistment_date', 'rank_date', 'position_date', 'black_card_expiry', 'blue_card_expiry'].includes(field)) {
                    val = formatDisplayDate(val);
                }
                el.value = val;
            }
        });

        // Personal Photo
        if (personnel.photo) {
            photoImg.src = personnel.photo;
            photoImg.classList.remove('d-none');
            if (photoDashedLabel) photoDashedLabel.classList.add('d-none');
        } else {
            photoImg.src = DEFAULT_AVATAR;
            photoImg.classList.remove('d-none');
            if (photoDashedLabel) photoDashedLabel.classList.add('d-none');
        }

        // Family Photo
        if (personnel.family_photo) {
            familyPhotoImg.src = personnel.family_photo;
            familyPhotoImg.classList.remove('d-none');
            if (familyDashedLabel) familyDashedLabel.classList.add('d-none');
        } else {
            familyPhotoImg.src = '';
            familyPhotoImg.classList.add('d-none');
            if (familyDashedLabel) familyDashedLabel.classList.remove('d-none');
        }

        updateAgeDisplay();
        checkExpiryDates();

        const curIdx = personnelData.findIndex(p => String(p.id) === String(selectedPersonnelId));
        const idxInfo = curIdx !== -1 ? ` [ទី ${curIdx + 1}/${personnelData.length}]` : '';
        setStatus(`បានជ្រើសរើស${idxInfo}៖ ${personnel.surname || ''} ${personnel.given_name || personnel.name_khmer || ''} (អត្តលេខ: ${personnel.id_card || '-'})`);

        // Smooth scroll to form on mobile devices when selecting a row
        if (window.innerWidth <= 992) {
            const formCard = document.querySelector('.input-form-card');
            if (formCard) {
                formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

    // ----------------------------------------------------
    // 2. Form CRUD Actions
    // ----------------------------------------------------
    async function handleAdd() {
        const payload = collectFormData();
        if (!payload.surname && !payload.given_name && !payload.name_khmer) {
            alert('សូមបញ្ចូល "គោត្តនាម" ឬ "នាម" របស់នាយទាហាន!');
            return;
        }

        setStatus('កំពុងបន្ថែមទិន្នន័យថ្មី...');
        try {
            const res = await fetch('api.php?action=add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.success) {
                alert(result.message);
                if (result.id) selectedPersonnelId = result.id;
                loadPersonnel();
            } else {
                alert('បរាជ័យ៖ ' + result.message);
            }
        } catch (err) {
            alert('មានបញ្ហាក្នុងការភ្ជាប់ទៅកាន់ API');
        }
    }

    async function handleUpdate() {
        if (!selectedPersonnelId) {
            alert('សូមជ្រើសរើសជួរទិន្នន័យនាយទាហានក្នុងតារាងជាមុនសិន!');
            return;
        }

        const payload = collectFormData();
        payload.id = selectedPersonnelId;

        setStatus('កំពុងកែប្រែទិន្នន័យ...');
        try {
            const res = await fetch('api.php?action=edit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.success) {
                alert(result.message);
                loadPersonnel();
            } else {
                alert('បរាជ័យ៖ ' + result.message);
            }
        } catch (err) {
            alert('មានបញ្ហាក្នុងការភ្ជាប់ទៅកាន់ API');
        }
    }

    async function handleDelete() {
        if (!selectedPersonnelId) {
            alert('សូមជ្រើសរើសជួរទិន្នន័យនាយទាហានដែលត្រូវលុបជាមុនសិន!');
            return;
        }

        if (!confirm('តើអ្នកប្រាកដជាចង់លុបទិន្នន័យនាយទាហាននេះមែនទេ?')) return;

        setStatus('កំពុងលុបទិន្នន័យ...');
        try {
            const res = await fetch('api.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: selectedPersonnelId })
            });
            const result = await res.json();
            if (result.success) {
                alert(result.message);
                selectedPersonnelId = null;
                clearForm();
                loadPersonnel();
            } else {
                alert('បរាជ័យ៖ ' + result.message);
            }
        } catch (err) {
            alert('មានបញ្ហាក្នុងការភ្ជាប់ទៅកាន់ API');
        }
    }

    function clearForm() {
        selectedPersonnelId = null;
        formFields.forEach(field => {
            const el = document.getElementById(field);
            if (!el) return;
            if (el.type === 'checkbox') {
                el.checked = false;
            } else if (field === 'children_count') {
                el.value = '0';
            } else if (field === 'gender') {
                el.value = 'ស';
            } else if (field === 'rank') {
                el.value = 'ព្រិន្ទបាលឯក';
            } else {
                el.value = '';
            }
        });

        if (photoImg) {
            photoImg.src = DEFAULT_AVATAR;
            photoImg.classList.remove('d-none');
        }
        if (photoDashedLabel) photoDashedLabel.classList.add('d-none');

        if (familyPhotoImg) {
            familyPhotoImg.src = '';
            familyPhotoImg.classList.add('d-none');
        }
        if (familyDashedLabel) familyDashedLabel.classList.remove('d-none');

        const allRows = tableBody.querySelectorAll('tr');
        allRows.forEach(r => r.classList.remove('selected-row', 'selected'));

        if (ageDisplay) ageDisplay.innerText = '-- ឆ្នាំ';
        checkExpiryDates();
        setStatus('បានសម្អាតទម្រង់បញ្ចូល');
    }

    function collectFormData() {
        const payload = {};
        formFields.forEach(field => {
            const el = document.getElementById(field);
            if (!el) return;
            if (el.type === 'checkbox') {
                payload[field] = el.checked ? 1 : 0;
            } else {
                payload[field] = el.value.trim();
            }
        });

        payload.name_khmer = `${payload.surname || ''} ${payload.given_name || ''}`.trim();
        
        const pobParts = [payload.pob_village, payload.pob_commune, payload.pob_district, payload.pob_province].filter(Boolean);
        payload.place_of_birth = pobParts.join(' - ');

        const addrParts = [payload.addr_house, payload.addr_group, payload.addr_village, payload.addr_commune, payload.addr_district, payload.addr_province].filter(Boolean);
        payload.current_address = addrParts.join(' - ');

        // Personal Photo URL / Data String
        if (photoImg && photoImg.src && !photoImg.src.includes('data:image/svg+xml')) {
            payload.photo = photoImg.getAttribute('src') || photoImg.src;
        } else {
            payload.photo = '';
        }

        // Family Photo URL / Data String
        if (familyPhotoImg && !familyPhotoImg.classList.contains('d-none') && familyPhotoImg.src) {
            payload.family_photo = familyPhotoImg.getAttribute('src') || familyPhotoImg.src;
        } else {
            payload.family_photo = '';
        }

        return payload;
    }

    // ----------------------------------------------------
    // 3. Helper Functions & Image Compressor
    // ----------------------------------------------------
    function updateAgeDisplay() {
        if (!dobInput || !ageDisplay) return;
        const val = dobInput.value.trim();
        if (!val) {
            ageDisplay.innerText = '-- ឆ្នាំ';
            return;
        }

        let birthYear = null;
        if (val.includes('/')) {
            const parts = val.split('/');
            if (parts.length === 3) birthYear = parseInt(parts[2], 10);
        } else if (val.includes('-')) {
            const parts = val.split('-');
            if (parts.length === 3) birthYear = parseInt(parts[0], 10);
        }

        if (birthYear && !isNaN(birthYear)) {
            const currentYear = new Date().getFullYear();
            const age = currentYear - birthYear;
            ageDisplay.innerText = `${age} ឆ្នាំ`;
        } else {
            ageDisplay.innerText = '-- ឆ្នាំ';
        }
    }

    function formatDisplayDate(dateStr) {
        if (!dateStr) return '';
        if (dateStr.includes('/')) return dateStr;
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return `${parts[2].padStart(2, '0')}/${parts[1].padStart(2, '0')}/${parts[0]}`;
        }
        return dateStr;
    }

    function parseIsoDate(displayDate) {
        if (!displayDate) return null;
        if (displayDate.includes('-')) return displayDate;
        const parts = displayDate.split('/');
        if (parts.length === 3) {
            return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
        }
        return displayDate;
    }

    function setStatus(msg) {
        if (statusMessage) statusMessage.innerText = msg;
    }

    function checkExpiryDates() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const blackInp = document.getElementById('black_card_expiry');
        const blueInp = document.getElementById('blue_card_expiry');

        [blackInp, blueInp].forEach(input => {
            if (!input) return;
            const val = input.value.trim();
            if (!val || val.includes('អចិន្ត្រៃយ៍')) {
                input.classList.remove('input-expired');
                input.style.backgroundColor = '';
                input.style.color = '';
                input.style.borderColor = '';
                input.style.fontWeight = '';
                return;
            }

            const parsedDate = parseDateToObj(val);
            if (parsedDate && parsedDate < today) {
                input.classList.add('input-expired');
                input.style.setProperty('background-color', '#fef2f2', 'important');
                input.style.setProperty('color', '#dc2626', 'important');
                input.style.setProperty('border-color', '#ef4444', 'important');
                input.style.setProperty('font-weight', '700', 'important');
            } else {
                input.classList.remove('input-expired');
                input.style.backgroundColor = '';
                input.style.color = '';
                input.style.borderColor = '';
                input.style.fontWeight = '';
            }
        });
    }

    function parseDateToObj(dateStr) {
        if (!dateStr) return null;
        if (dateStr.includes('/')) {
            const parts = dateStr.split('/');
            if (parts.length === 3) {
                const d = parseInt(parts[0], 10);
                const m = parseInt(parts[1], 10) - 1;
                const y = parseInt(parts[2], 10);
                return new Date(y, m, d);
            }
        } else if (dateStr.includes('-')) {
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                if (parts[0].length === 4) {
                    return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                } else {
                    return new Date(parseInt(parts[2], 10), parseInt(parts[1], 10) - 1, parseInt(parts[0], 10));
                }
            }
        }
        return null;
    }

    // Client-side Image Compressor
    function compressImage(file, maxWidth, maxHeight, quality, callback) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                let width = img.width;
                let height = img.height;
                if (width > height) {
                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }
                } else {
                    if (height > maxHeight) {
                        width = Math.round((width * maxHeight) / height);
                        height = maxHeight;
                    }
                }
                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                const dataUrl = canvas.toDataURL('image/jpeg', quality);
                callback(dataUrl);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // Image Upload Window Handlers (Global Scope)
    window.previewPersonalImage = function(input) {
        if (input.files && input.files[0]) {
            setStatus('កំពុងដំណើរការបង្រួមរូបថតសាមីខ្លួន...');
            compressImage(input.files[0], 600, 800, 0.85, function(dataUrl) {
                if (photoImg) {
                    photoImg.src = dataUrl;
                    photoImg.classList.remove('d-none');
                }
                if (photoDashedLabel) photoDashedLabel.classList.add('d-none');
                setStatus('បានជ្រើសរើស និងបង្រួមរូបថតសាមីខ្លួនរួចរាល់!');
            });
        }
    };

    window.previewFamilyImage = function(input) {
        if (input.files && input.files[0]) {
            setStatus('កំពុងដំណើរការបង្រួមរូបថតគ្រួសារ...');
            compressImage(input.files[0], 800, 600, 0.85, function(dataUrl) {
                if (familyPhotoImg) {
                    familyPhotoImg.src = dataUrl;
                    familyPhotoImg.classList.remove('d-none');
                }
                if (familyDashedLabel) familyDashedLabel.classList.add('d-none');
                setStatus('បានជ្រើសរើស និងបង្រួមរូបថតគ្រួសាររួចរាល់!');
            });
        }
    };

    window.previewImage = function(input, targetImgId) {
        if (targetImgId === 'familyPhotoImg') {
            window.previewFamilyImage(input);
        } else {
            window.previewPersonalImage(input);
        }
    };

    window.removePhoto = function(imgId, fileInputId, labelId) {
        const img = document.getElementById(imgId);
        const input = document.getElementById(fileInputId);
        if (input) input.value = '';

        if (imgId === 'photoImg') {
            if (img) img.src = DEFAULT_AVATAR;
            if (photoDashedLabel) photoDashedLabel.classList.add('d-none');
        } else {
            if (img) {
                img.src = '';
                img.classList.add('d-none');
            }
            if (labelId) {
                const label = document.getElementById(labelId);
                if (label) label.classList.remove('d-none');
            }
        }
        setStatus('បានលុបរូបថតចេញ!');
    };
});
