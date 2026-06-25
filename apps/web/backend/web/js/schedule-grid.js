/**
 * Interactive Booking Block Schedule Grid (Date-Aware)
 * Shows actual dates (Mon 23 Jun, Tue 24 Jun...) with week navigation.
 * Supports ownership: only user's own blocks are editable.
 * Options (window.__scheduleGridOptions):
 *   readOnly: boolean — disables all editing
 *   deferSave: boolean — Konfirmasi pattern (default: true)
 *   hideLabSelect: boolean — hide room dropdown
 *   currentUserId: string — the logged-in user's ID (for ownership checks)
 *   adminMode: boolean — admin can view details / deny but not edit
 */
(function () {
    'use strict';

    // ─── Configuration ───
    const CONFIG = {
        pixelsPerMinute: 1.5,
        defaultSnapInterval: 10,
        operatingStart: 7 * 60,
        operatingEnd: 21 * 60,
        defaultDuration: 60,
        headerHeight: 50, // taller to show date
        colors: ['#FF6B00', '#3b82f6', '#22c55e', '#8b5cf6', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899'],
        dayNames: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
        dayNamesShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
    };

    // ─── State ───
    let bookings = [];
    let pendingChanges = [];
    let snapEnabled = true;
    let snapInterval = CONFIG.defaultSnapInterval;
    let dragState = null;
    let resizeState = null;
    let selectedBooking = null;
    let currentLabId = null;
    let readOnly = false;
    let deferSave = true;
    let hideLabSelect = false;
    let currentUserId = null;
    let adminMode = false;
    let weekStart = null; // Monday of current displayed week
    let weekDates = []; // array of 7 Date objects (Mon-Sun)

    // ─── Date Utilities ───
    function getMonday(d) {
        var date = new Date(d);
        var day = date.getDay();
        var diff = date.getDate() - day + (day === 0 ? -6 : 1);
        date.setDate(diff);
        date.setHours(0, 0, 0, 0);
        return date;
    }

    function computeWeekDates(monday) {
        var dates = [];
        for (var i = 0; i < 7; i++) {
            var d = new Date(monday);
            d.setDate(monday.getDate() + i);
            dates.push(d);
        }
        return dates;
    }

    function formatDateShort(d) {
        return CONFIG.dayNamesShort[d.getDay()] + ', ' + d.getDate() + ' ' + CONFIG.monthNames[d.getMonth()];
    }

    function formatDateISO(d) {
        var yyyy = d.getFullYear();
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var dd = String(d.getDate()).padStart(2, '0');
        return yyyy + '-' + mm + '-' + dd;
    }

    function isSameDate(dateStr, dateObj) {
        return dateStr === formatDateISO(dateObj);
    }

    // ─── Time Utilities ───
    function minutesToTime(minutes) {
        var h = Math.floor(minutes / 60);
        var m = minutes % 60;
        return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
    }

    function timeToMinutes(timeStr) {
        var parts = timeStr.split(':').map(Number);
        return parts[0] * 60 + parts[1];
    }

    function snapTime(minutes) {
        if (!snapEnabled) return Math.max(CONFIG.operatingStart, Math.min(CONFIG.operatingEnd, minutes));
        var snapped = Math.round(minutes / snapInterval) * snapInterval;
        return Math.max(CONFIG.operatingStart, Math.min(CONFIG.operatingEnd, snapped));
    }

    function minutesToPx(minutes) {
        return (minutes - CONFIG.operatingStart) * CONFIG.pixelsPerMinute + CONFIG.headerHeight;
    }

    function pxToMinutes(px) {
        return Math.round((px - CONFIG.headerHeight) / CONFIG.pixelsPerMinute) + CONFIG.operatingStart;
    }

    function generateId() {
        return 'bk_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5);
    }

    function isOwnBooking(booking) {
        if (!currentUserId) return !booking.ownerId; // new bookings without owner
        return booking.ownerId == currentUserId || !booking.ownerId;
    }

    function isBookingEditable(booking) {
        if (readOnly) return false;
        if (adminMode) return false;
        return isOwnBooking(booking);
    }

    function hasOverlap(booking, excludeId) {
        return bookings.some(function (b) {
            if (b.id === excludeId) return false;
            if (b.date !== booking.date || b.labId !== booking.labId) return false;
            var s1 = timeToMinutes(booking.startTime);
            var e1 = timeToMinutes(booking.endTime);
            var s2 = timeToMinutes(b.startTime);
            var e2 = timeToMinutes(b.endTime);
            return s1 < e2 && e1 > s2;
        });
    }

    function showToast(message, type) {
        var toast = document.createElement('div');
        toast.className = 'sg-toast sg-toast-' + type;
        toast.textContent = message;
        document.body.appendChild(toast);
        requestAnimationFrame(function () { toast.classList.add('sg-toast-show'); });
        setTimeout(function () {
            toast.classList.remove('sg-toast-show');
            setTimeout(function () { toast.remove(); }, 300);
        }, 3000);
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function dispatchBookingEvent(eventName, booking) {
        document.dispatchEvent(new CustomEvent(eventName, { detail: booking }));
    }

    // ─── Pending Changes ───
    function recordChange(type, data) {
        if (!deferSave) return;
        pendingChanges.push({ type: type, data: data, ts: Date.now() });
        updateConfirmBar();
    }

    function updateConfirmBar() {
        var bar = document.getElementById('sg-confirm-bar');
        if (!bar) return;
        var count = pendingChanges.length;
        if (count === 0) { bar.classList.add('sg-hidden'); }
        else {
            bar.classList.remove('sg-hidden');
            var badge = bar.querySelector('.sg-confirm-badge-count');
            var text = bar.querySelector('.sg-confirm-text');
            if (badge) badge.textContent = count;
            if (text) text.textContent = count + ' perubahan belum disimpan';
        }
    }

    function confirmChanges() {
        if (pendingChanges.length === 0) return;
        syncToServer(function (success) {
            if (success) {
                pendingChanges = [];
                updateConfirmBar();
                showToast('Semua perubahan berhasil disimpan.', 'success');
                document.querySelectorAll('.sg-block-pending').forEach(function (el) { el.classList.remove('sg-block-pending'); });
            } else {
                showToast('Gagal menyimpan perubahan. Coba lagi.', 'error');
            }
        });
    }

    function discardChanges() {
        if (pendingChanges.length === 0) return;
        if (!confirm('Buang semua perubahan yang belum disimpan?')) return;
        pendingChanges = [];
        loadBookings();
        renderBlocks();
        updateConfirmBar();
        showToast('Perubahan dibatalkan.', 'success');
    }

    // ─── Rendering ───
    function renderGrid() {
        var container = document.getElementById('schedule-grid-container');
        if (!container) return;
        container.innerHTML = '';

        var totalMinutes = CONFIG.operatingEnd - CONFIG.operatingStart;
        var gridHeight = totalMinutes * CONFIG.pixelsPerMinute + CONFIG.headerHeight;

        if (readOnly || adminMode) container.classList.add('sg-readonly');
        else container.classList.remove('sg-readonly');

        // Confirm bar
        if (!readOnly && !adminMode && deferSave) {
            container.appendChild(buildConfirmBar());
        }

        // Week navigation + toolbar
        container.appendChild(buildWeekNav());
        if (!readOnly && !adminMode) {
            container.appendChild(buildToolbar());
        }

        // Grid wrapper
        var gridWrapper = document.createElement('div');
        gridWrapper.className = 'sg-grid-wrapper';

        // Time axis
        var timeAxis = document.createElement('div');
        timeAxis.className = 'sg-time-axis';
        timeAxis.style.height = gridHeight + 'px';
        for (var m = CONFIG.operatingStart; m <= CONFIG.operatingEnd; m += 60) {
            var label = document.createElement('div');
            label.className = 'sg-time-label';
            label.style.top = minutesToPx(m) + 'px';
            label.textContent = minutesToTime(m);
            timeAxis.appendChild(label);
        }
        gridWrapper.appendChild(timeAxis);

        // Day columns (one per date in current week)
        var columnsWrap = document.createElement('div');
        columnsWrap.className = 'sg-columns-wrap';

        weekDates.forEach(function (dateObj, idx) {
            var dateStr = formatDateISO(dateObj);
            var col = document.createElement('div');
            col.className = 'sg-day-column';
            col.dataset.date = dateStr;
            col.style.height = gridHeight + 'px';

            // Header with date
            var header = document.createElement('div');
            header.className = 'sg-day-header';
            var isToday = formatDateISO(new Date()) === dateStr;
            if (isToday) header.classList.add('sg-day-today');
            header.innerHTML = '<div class="sg-day-name">' + CONFIG.dayNamesShort[dateObj.getDay()] + '</div>' +
                '<div class="sg-day-date">' + dateObj.getDate() + ' ' + CONFIG.monthNames[dateObj.getMonth()] + '</div>';
            col.appendChild(header);

            // Grid lines
            for (var m2 = CONFIG.operatingStart; m2 <= CONFIG.operatingEnd; m2 += 60) {
                var line = document.createElement('div');
                line.className = m2 % 60 === 0 ? 'sg-grid-line sg-grid-line-hour' : 'sg-grid-line';
                line.style.top = minutesToPx(m2) + 'px';
                col.appendChild(line);
            }

            // Click to add (only if editable, not admin)
            if (!readOnly && !adminMode) {
                col.addEventListener('click', function (e) {
                    if (e.target.closest('.sg-block')) return;
                    var rect = col.getBoundingClientRect();
                    var y = e.clientY - rect.top;
                    var clickedMin = pxToMinutes(y);
                    var snappedStart = snapTime(clickedMin);
                    var snappedEnd = Math.min(CONFIG.operatingEnd, snappedStart + CONFIG.defaultDuration);
                    openEditPanel({
                        id: null, labId: currentLabId, date: dateStr,
                        startTime: minutesToTime(snappedStart),
                        endTime: minutesToTime(snappedEnd),
                        courseName: '', color: '#FF6B00', ownerId: currentUserId,
                    });
                });
            }

            columnsWrap.appendChild(col);
        });

        gridWrapper.appendChild(columnsWrap);
        container.appendChild(gridWrapper);
        renderBlocks();
    }

    function buildWeekNav() {
        var nav = document.createElement('div');
        nav.className = 'sg-week-nav';
        var startStr = weekDates[0].getDate() + ' ' + CONFIG.monthNames[weekDates[0].getMonth()];
        var endStr = weekDates[6].getDate() + ' ' + CONFIG.monthNames[weekDates[6].getMonth()] + ' ' + weekDates[6].getFullYear();
        nav.innerHTML =
            '<button class="sg-week-btn" id="sg-week-prev"><i class="fas fa-chevron-left"></i></button>' +
            '<span class="sg-week-label">' + startStr + ' — ' + endStr + '</span>' +
            '<button class="sg-week-btn" id="sg-week-next"><i class="fas fa-chevron-right"></i></button>' +
            '<button class="sg-week-btn sg-week-today" id="sg-week-today">Hari Ini</button>';
        if (!hideLabSelect) {
            nav.appendChild(buildLabSelect());
        }
        return nav;
    }

    function buildConfirmBar() {
        var bar = document.createElement('div');
        bar.id = 'sg-confirm-bar';
        bar.className = 'sg-confirm-bar sg-hidden';
        bar.innerHTML =
            '<span class="sg-confirm-badge"><i class="fas fa-exclamation-circle"></i> <span class="sg-confirm-badge-count">0</span></span>' +
            '<span class="sg-confirm-text">0 perubahan belum disimpan</span>' +
            '<button class="sg-btn-discard" id="sg-btn-discard">Buang</button>' +
            '<button class="sg-btn-confirm" id="sg-btn-confirm"><i class="fas fa-check"></i> Konfirmasi</button>';
        return bar;
    }

    function buildToolbar() {
        var toolbar = document.createElement('div');
        toolbar.className = 'sg-toolbar';
        var snapToggle = document.createElement('label');
        snapToggle.className = 'sg-snap-toggle';
        snapToggle.innerHTML =
            '<input type="checkbox" id="sg-snap-checkbox" ' + (snapEnabled ? 'checked' : '') + '>' +
            '<span>Snap:</span>' +
            '<select id="sg-snap-interval">' +
            '<option value="5"' + (snapInterval === 5 ? ' selected' : '') + '>5m</option>' +
            '<option value="10"' + (snapInterval === 10 ? ' selected' : '') + '>10m</option>' +
            '<option value="15"' + (snapInterval === 15 ? ' selected' : '') + '>15m</option>' +
            '<option value="30"' + (snapInterval === 30 ? ' selected' : '') + '>30m</option>' +
            '</select>';
        toolbar.appendChild(snapToggle);
        return toolbar;
    }

    function buildLabSelect() {
        var labSelect = document.createElement('select');
        labSelect.id = 'sg-lab-select';
        labSelect.className = 'sg-lab-select';
        if (window.__scheduleGridRooms) {
            window.__scheduleGridRooms.forEach(function (room) {
                var opt = document.createElement('option');
                opt.value = room.id;
                opt.textContent = room.name;
                if (room.id == currentLabId) opt.selected = true;
                labSelect.appendChild(opt);
            });
        }
        return labSelect;
    }

    function renderBlocks() {
        document.querySelectorAll('.sg-block').forEach(function (el) { el.remove(); });

        // Filter bookings: only those in current week and current room
        var weekStartStr = formatDateISO(weekDates[0]);
        var weekEndStr = formatDateISO(weekDates[6]);

        var filtered = bookings.filter(function (b) {
            if (b.labId != currentLabId) return false;
            if (!b.date) return false;
            return b.date >= weekStartStr && b.date <= weekEndStr;
        });

        filtered.forEach(function (booking) {
            var col = document.querySelector('.sg-day-column[data-date="' + booking.date + '"]');
            if (!col) return;

            var startMin = timeToMinutes(booking.startTime);
            var endMin = timeToMinutes(booking.endTime);
            var top = minutesToPx(startMin);
            var height = (endMin - startMin) * CONFIG.pixelsPerMinute;

            var block = document.createElement('div');
            block.className = 'sg-block';
            block.dataset.id = booking.id;
            block.style.top = top + 'px';
            block.style.height = height + 'px';
            block.style.backgroundColor = booking.color || CONFIG.colors[0];

            if (booking._pending) block.classList.add('sg-block-pending');

            var editable = isBookingEditable(booking);
            if (!editable) block.classList.add('sg-block-readonly');

            block.innerHTML =
                '<div class="sg-block-content">' +
                '<div class="sg-block-title">' + escapeHtml(booking.courseName || 'Untitled') + '</div>' +
                '<div class="sg-block-time">' + booking.startTime + '–' + booking.endTime + '</div>' +
                '</div>' +
                (editable ? '<div class="sg-block-resize-handle"></div>' : '');

            // Click behavior depends on ownership/admin
            block.addEventListener('click', function (e) {
                if (e.target.closest('.sg-block-resize-handle')) return;
                e.stopPropagation();
                if (editable) {
                    openEditPanel(booking);
                } else if (adminMode) {
                    openAdminDetailPanel(booking);
                }
                // read-only non-admin: no click action
            });

            if (editable) {
                setupDrag(block, booking);
                var handle = block.querySelector('.sg-block-resize-handle');
                if (handle) setupResize(handle, booking);
            }

            col.appendChild(block);
        });
    }

    // ─── Admin Detail Panel (view-only + deny) ───
    function openAdminDetailPanel(booking) {
        closeEditPanel();
        var panel = document.createElement('div');
        panel.id = 'sg-edit-panel';
        panel.className = 'sg-edit-panel';
        panel.innerHTML =
            '<div class="sg-edit-overlay"></div>' +
            '<div class="sg-edit-modal">' +
            '<div class="sg-edit-header"><h3>Detail Jadwal</h3>' +
            '<button class="sg-edit-close" aria-label="Close">&times;</button></div>' +
            '<div class="sg-edit-body">' +
            '<div class="sg-field"><label>Kegiatan</label><p style="margin:0;font-size:15px;font-weight:600">' + escapeHtml(booking.courseName || '-') + '</p></div>' +
            '<div class="sg-field"><label>Tanggal</label><p style="margin:0">' + escapeHtml(booking.date || '-') + '</p></div>' +
            '<div class="sg-field-row">' +
            '<div class="sg-field"><label>Mulai</label><p style="margin:0">' + booking.startTime + '</p></div>' +
            '<div class="sg-field"><label>Selesai</label><p style="margin:0">' + booking.endTime + '</p></div>' +
            '</div>' +
            (booking.ownerName ? '<div class="sg-field"><label>Peminjam</label><p style="margin:0">' + escapeHtml(booking.ownerName) + '</p></div>' : '') +
            '</div>' +
            '<div class="sg-edit-footer">' +
            (booking.id && booking.id.toString().startsWith('srv_') ? '<button class="sg-btn sg-btn-danger" id="sg-btn-deny">Tolak</button>' : '') +
            '<button class="sg-btn sg-btn-secondary" id="sg-btn-cancel">Tutup</button>' +
            '</div></div>';

        document.body.appendChild(panel);

        panel.querySelector('.sg-edit-close').addEventListener('click', closeEditPanel);
        panel.querySelector('.sg-edit-overlay').addEventListener('click', closeEditPanel);
        panel.querySelector('#sg-btn-cancel').addEventListener('click', closeEditPanel);

        var denyBtn = panel.querySelector('#sg-btn-deny');
        if (denyBtn) {
            denyBtn.addEventListener('click', function () {
                if (!confirm('Tolak/batalkan jadwal ini?')) return;
                dispatchBookingEvent('sg-booking-denied', booking);
                bookings = bookings.filter(function (b) { return b.id !== booking.id; });
                closeEditPanel();
                renderBlocks();
                showToast('Jadwal ditolak.', 'success');
            });
        }
    }

    // ─── Drag and Drop ───
    function setupDrag(block, booking) {
        block.addEventListener('mousedown', function (e) {
            if (e.target.closest('.sg-block-resize-handle')) return;
            if (e.button !== 0) return;
            e.preventDefault();
            e.stopPropagation();

            var origTop = parseInt(block.style.top);
            dragState = {
                booking: booking, block: block,
                startY: e.clientY, startX: e.clientX,
                origTop: origTop, origDate: booking.date,
                origStartMin: timeToMinutes(booking.startTime),
                duration: timeToMinutes(booking.endTime) - timeToMinutes(booking.startTime),
            };
            block.classList.add('sg-block-dragging');

            var ghostEl = block.cloneNode(true);
            ghostEl.className = 'sg-block sg-block-ghost';
            ghostEl.style.top = block.style.top;
            ghostEl.style.height = block.style.height;
            ghostEl.style.backgroundColor = booking.color;
            block.parentElement.appendChild(ghostEl);
            dragState.ghost = ghostEl;

            document.addEventListener('mousemove', onDragMove);
            document.addEventListener('mouseup', onDragEnd);
        });
    }

    function onDragMove(e) {
        if (!dragState) return;
        var dy = e.clientY - dragState.startY;
        var newTop = dragState.origTop + dy;
        var newStartMin = pxToMinutes(newTop);
        var snappedStart = snapEnabled && !e.altKey
            ? snapTime(newStartMin)
            : Math.max(CONFIG.operatingStart, Math.min(CONFIG.operatingEnd - dragState.duration, newStartMin));
        var snappedEnd = snappedStart + dragState.duration;
        if (snappedEnd > CONFIG.operatingEnd) return;

        // Find target column by x position
        var columns = document.querySelectorAll('.sg-day-column');
        var targetDate = dragState.origDate;
        columns.forEach(function (col) {
            var rect = col.getBoundingClientRect();
            if (e.clientX >= rect.left && e.clientX <= rect.right) {
                targetDate = col.dataset.date;
            }
        });

        var targetCol = document.querySelector('.sg-day-column[data-date="' + targetDate + '"]');
        if (targetCol && dragState.ghost.parentElement !== targetCol) {
            dragState.ghost.remove();
            targetCol.appendChild(dragState.ghost);
        }

        dragState.ghost.style.top = minutesToPx(snappedStart) + 'px';
        dragState.targetDate = targetDate;
        dragState.targetStartMin = snappedStart;

        var testBooking = Object.assign({}, dragState.booking, {
            date: targetDate, startTime: minutesToTime(snappedStart), endTime: minutesToTime(snappedEnd),
        });
        var conflict = hasOverlap(testBooking, dragState.booking.id);
        dragState.ghost.classList.toggle('sg-block-invalid', conflict);
        dragState.ghost.classList.toggle('sg-block-valid', !conflict);
    }

    function onDragEnd(e) {
        if (!dragState) return;
        document.removeEventListener('mousemove', onDragMove);
        document.removeEventListener('mouseup', onDragEnd);

        var booking = dragState.booking;
        var targetDate = dragState.targetDate || dragState.origDate;
        var targetStartMin = dragState.targetStartMin != null ? dragState.targetStartMin : dragState.origStartMin;
        var targetEndMin = targetStartMin + dragState.duration;

        if (dragState.ghost) dragState.ghost.remove();
        dragState.block.classList.remove('sg-block-dragging');

        var testBooking = Object.assign({}, booking, {
            date: targetDate, startTime: minutesToTime(targetStartMin), endTime: minutesToTime(targetEndMin),
        });
        if (hasOverlap(testBooking, booking.id)) {
            showToast('Konflik jadwal!', 'error');
            dragState = null; renderBlocks(); return;
        }

        booking.date = targetDate;
        booking.startTime = minutesToTime(targetStartMin);
        booking.endTime = minutesToTime(targetEndMin);
        booking._pending = true;
        dragState = null;

        if (deferSave) recordChange('move', { id: booking.id, date: booking.date, startTime: booking.startTime, endTime: booking.endTime });
        else saveBookings();
        renderBlocks();
        showToast('Jadwal dipindahkan.' + (deferSave ? ' Klik Konfirmasi.' : ''), 'success');
        dispatchBookingEvent('sg-booking-changed', booking);
    }

    // ─── Resize ───
    function setupResize(handle, booking) {
        handle.addEventListener('mousedown', function (e) {
            e.preventDefault(); e.stopPropagation();
            var block = handle.closest('.sg-block');
            resizeState = { booking: booking, block: block, startY: e.clientY, origHeight: parseInt(block.style.height) };
            block.classList.add('sg-block-resizing');
            document.addEventListener('mousemove', onResizeMove);
            document.addEventListener('mouseup', onResizeEnd);
        });
    }

    function onResizeMove(e) {
        if (!resizeState) return;
        var dy = e.clientY - resizeState.startY;
        var newHeight = Math.max(15, resizeState.origHeight + dy);
        var startMin = timeToMinutes(resizeState.booking.startTime);
        var newEndMin = pxToMinutes(minutesToPx(startMin) + newHeight);
        var snappedEnd = snapEnabled && !e.altKey ? snapTime(newEndMin) : Math.min(CONFIG.operatingEnd, newEndMin);
        if (snappedEnd <= startMin + 10 || snappedEnd > CONFIG.operatingEnd) return;
        resizeState.block.style.height = (snappedEnd - startMin) * CONFIG.pixelsPerMinute + 'px';
        var timeEl = resizeState.block.querySelector('.sg-block-time');
        if (timeEl) timeEl.textContent = resizeState.booking.startTime + '–' + minutesToTime(snappedEnd);
        resizeState.targetEndMin = snappedEnd;
    }

    function onResizeEnd(e) {
        if (!resizeState) return;
        document.removeEventListener('mousemove', onResizeMove);
        document.removeEventListener('mouseup', onResizeEnd);
        var booking = resizeState.booking;
        var targetEndMin = resizeState.targetEndMin || timeToMinutes(booking.endTime);
        resizeState.block.classList.remove('sg-block-resizing');

        var testBooking = Object.assign({}, booking, { endTime: minutesToTime(targetEndMin) });
        if (hasOverlap(testBooking, booking.id)) {
            showToast('Konflik!', 'error'); resizeState = null; renderBlocks(); return;
        }
        booking.endTime = minutesToTime(targetEndMin);
        booking._pending = true;
        resizeState = null;

        if (deferSave) recordChange('resize', { id: booking.id, endTime: booking.endTime });
        else saveBookings();
        renderBlocks();
        showToast('Durasi diubah.' + (deferSave ? ' Klik Konfirmasi.' : ''), 'success');
        dispatchBookingEvent('sg-booking-changed', booking);
    }

    // ─── Edit Panel (for own bookings) ───
    function openEditPanel(booking) {
        selectedBooking = booking;
        var isNew = !booking.id;
        closeEditPanel();

        var panel = document.createElement('div');
        panel.id = 'sg-edit-panel';
        panel.className = 'sg-edit-panel';
        panel.innerHTML =
            '<div class="sg-edit-overlay"></div>' +
            '<div class="sg-edit-modal">' +
            '<div class="sg-edit-header"><h3>' + (isNew ? 'Tambah Jadwal' : 'Edit Jadwal') + '</h3>' +
            '<button class="sg-edit-close" aria-label="Close">&times;</button></div>' +
            '<div class="sg-edit-body">' +
            '<div class="sg-field"><label>Nama Kegiatan</label>' +
            '<input type="text" id="sg-course-name" value="' + escapeHtml(booking.courseName || '') + '" placeholder="Rapat Tim IT"></div>' +
            '<div class="sg-field"><label>Tanggal</label>' +
            '<input type="date" id="sg-date" value="' + (booking.date || '') + '"></div>' +
            '<div class="sg-field-row">' +
            '<div class="sg-field"><label>Mulai</label><input type="time" id="sg-start-time" value="' + booking.startTime + '"></div>' +
            '<div class="sg-field"><label>Selesai</label><input type="time" id="sg-end-time" value="' + booking.endTime + '"></div></div>' +
            '<div class="sg-field"><label>Warna</label><div class="sg-color-swatches">' +
            CONFIG.colors.map(function (c) { return '<button class="sg-swatch ' + (c === booking.color ? 'sg-swatch-active' : '') + '" data-color="' + c + '" style="background:' + c + '"></button>'; }).join('') +
            '</div></div>' +
            '<div class="sg-edit-error" id="sg-edit-error"></div></div>' +
            '<div class="sg-edit-footer">' +
            (!isNew ? '<button class="sg-btn sg-btn-danger" id="sg-btn-delete">Hapus</button>' : '') +
            '<button class="sg-btn sg-btn-secondary" id="sg-btn-cancel">Batal</button>' +
            '<button class="sg-btn sg-btn-primary" id="sg-btn-save">Simpan</button></div></div>';

        document.body.appendChild(panel);
        setupEditPanelEvents(panel, booking, isNew);
    }

    function setupEditPanelEvents(panel, booking, isNew) {
        var closeBtn = panel.querySelector('.sg-edit-close');
        var overlay = panel.querySelector('.sg-edit-overlay');
        var cancelBtn = panel.querySelector('#sg-btn-cancel');
        var saveBtn = panel.querySelector('#sg-btn-save');
        var deleteBtn = panel.querySelector('#sg-btn-delete');
        var errorEl = panel.querySelector('#sg-edit-error');
        var currentColor = booking.color;

        closeBtn.addEventListener('click', closeEditPanel);
        overlay.addEventListener('click', closeEditPanel);
        cancelBtn.addEventListener('click', closeEditPanel);

        panel.querySelectorAll('.sg-swatch').forEach(function (sw) {
            sw.addEventListener('click', function () {
                panel.querySelectorAll('.sg-swatch').forEach(function (s) { s.classList.remove('sg-swatch-active'); });
                this.classList.add('sg-swatch-active');
                currentColor = this.dataset.color;
            });
        });

        saveBtn.addEventListener('click', function () {
            var courseName = panel.querySelector('#sg-course-name').value.trim();
            var date = panel.querySelector('#sg-date').value;
            var startTime = panel.querySelector('#sg-start-time').value;
            var endTime = panel.querySelector('#sg-end-time').value;

            if (!courseName) { errorEl.textContent = 'Nama kegiatan wajib diisi.'; return; }
            if (!date) { errorEl.textContent = 'Tanggal wajib diisi.'; return; }
            if (!startTime || !endTime) { errorEl.textContent = 'Waktu wajib diisi.'; return; }
            var startMin = timeToMinutes(startTime), endMin = timeToMinutes(endTime);
            if (endMin <= startMin) { errorEl.textContent = 'Waktu selesai harus setelah mulai.'; return; }
            if (startMin < CONFIG.operatingStart || endMin > CONFIG.operatingEnd) {
                errorEl.textContent = 'Harus dalam jam operasional.'; return;
            }

            var newBooking = {
                id: isNew ? generateId() : booking.id,
                labId: currentLabId, date: date,
                startTime: startTime, endTime: endTime,
                courseName: courseName, color: currentColor,
                ownerId: currentUserId, _pending: true,
            };
            if (hasOverlap(newBooking, isNew ? null : booking.id)) {
                errorEl.textContent = 'Konflik dengan jadwal lain!'; return;
            }

            if (isNew) {
                bookings.push(newBooking);
                if (deferSave) recordChange('add', newBooking); else saveBookings();
                showToast('Jadwal ditambahkan.', 'success');
                dispatchBookingEvent('sg-booking-added', newBooking);
            } else {
                Object.assign(booking, newBooking);
                if (deferSave) recordChange('edit', newBooking); else saveBookings();
                showToast('Jadwal diperbarui.', 'success');
                dispatchBookingEvent('sg-booking-changed', newBooking);
            }
            closeEditPanel(); renderBlocks();
        });

        if (deleteBtn) {
            deleteBtn.addEventListener('click', function () {
                if (!confirm('Hapus jadwal ini?')) return;
                bookings = bookings.filter(function (b) { return b.id !== booking.id; });
                if (deferSave) recordChange('delete', { id: booking.id }); else saveBookings();
                closeEditPanel(); renderBlocks();
                showToast('Jadwal dihapus.', 'success');
            });
        }
    }

    function closeEditPanel() {
        var panel = document.getElementById('sg-edit-panel');
        if (panel) panel.remove();
        selectedBooking = null;
        renderBlocks();
    }

    // ─── Persistence ───
    function loadBookings() {
        if (window.__scheduleGridBookings) {
            bookings = JSON.parse(JSON.stringify(window.__scheduleGridBookings));
            return;
        }
        try { bookings = JSON.parse(localStorage.getItem('sg_bookings_' + currentLabId)) || []; }
        catch (e) { bookings = []; }
    }

    function saveBookings() {
        localStorage.setItem('sg_bookings_' + currentLabId, JSON.stringify(bookings));
        if (window.__scheduleGridSaveUrl && !deferSave) syncToServer();
    }

    function syncToServer(callback) {
        var url = window.__scheduleGridSaveUrl;
        if (!url) { if (callback) callback(false); return; }
        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        var headers = { 'Content-Type': 'application/json' };
        if (csrfToken) headers['X-CSRF-Token'] = csrfToken.getAttribute('content');
        fetch(url, {
            method: 'POST', headers: headers,
            body: JSON.stringify({ labId: currentLabId, bookings: bookings.filter(function (b) { return b.labId == currentLabId; }), changes: pendingChanges }),
        })
        .then(function (r) { return r.json(); })
        .then(function (d) { if (callback) callback(d && d.success); })
        .catch(function () { if (callback) callback(false); });
    }

    // ─── Preferences ───
    function loadSnapPreferences() {
        try { var p = JSON.parse(localStorage.getItem('sg_snap_prefs')); if (p) { snapEnabled = p.enabled !== false; snapInterval = p.interval || CONFIG.defaultSnapInterval; } } catch (e) {}
    }
    function saveSnapPreferences() {
        localStorage.setItem('sg_snap_prefs', JSON.stringify({ enabled: snapEnabled, interval: snapInterval }));
    }

    // ─── Events ───
    function setupEvents() {
        document.addEventListener('change', function (e) {
            if (e.target.id === 'sg-snap-checkbox') { snapEnabled = e.target.checked; saveSnapPreferences(); }
            if (e.target.id === 'sg-snap-interval') { snapInterval = parseInt(e.target.value); saveSnapPreferences(); renderGrid(); }
            if (e.target.id === 'sg-lab-select') { currentLabId = e.target.value; loadBookings(); renderBlocks(); }
        });
        document.addEventListener('click', function (e) {
            if (e.target.id === 'sg-btn-confirm' || e.target.closest('#sg-btn-confirm')) confirmChanges();
            if (e.target.id === 'sg-btn-discard' || e.target.closest('#sg-btn-discard')) discardChanges();
            if (e.target.id === 'sg-week-prev' || e.target.closest('#sg-week-prev')) { changeWeek(-1); }
            if (e.target.id === 'sg-week-next' || e.target.closest('#sg-week-next')) { changeWeek(1); }
            if (e.target.id === 'sg-week-today' || e.target.closest('#sg-week-today')) { weekStart = getMonday(new Date()); weekDates = computeWeekDates(weekStart); renderGrid(); }
        });
    }

    function changeWeek(dir) {
        var newStart = new Date(weekStart);
        newStart.setDate(newStart.getDate() + dir * 7);
        weekStart = newStart;
        weekDates = computeWeekDates(weekStart);
        renderGrid();
    }

    // ─── Init ───
    function init() {
        var opts = window.__scheduleGridOptions || {};
        readOnly = !!opts.readOnly;
        deferSave = opts.deferSave !== undefined ? !!opts.deferSave : true;
        hideLabSelect = !!opts.hideLabSelect;
        currentUserId = opts.currentUserId || null;
        adminMode = !!opts.adminMode;

        if (window.__scheduleGridRooms && window.__scheduleGridRooms.length > 0) {
            currentLabId = window.__scheduleGridCurrentRoom || window.__scheduleGridRooms[0].id;
        }

        // Set current week
        weekStart = getMonday(new Date());
        weekDates = computeWeekDates(weekStart);

        loadSnapPreferences();
        loadBookings();
        renderGrid();
        setupEvents();
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();

    window.ScheduleGrid = {
        refresh: function () { renderGrid(); },
        getBookings: function () { return bookings; },
        setBookings: function (data) { bookings = data; renderBlocks(); },
        setLabId: function (id) { currentLabId = id; loadBookings(); renderBlocks(); },
        getPendingCount: function () { return pendingChanges.length; },
        confirm: confirmChanges, discard: discardChanges,
        setWeek: function (date) { weekStart = getMonday(date); weekDates = computeWeekDates(weekStart); renderGrid(); },
    };
})();
