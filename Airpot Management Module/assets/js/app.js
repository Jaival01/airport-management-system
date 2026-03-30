// Airport Management System - Main JavaScript

// ---- Modal Functions ----

function openModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close modal when clicking the backdrop
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        closeModal(e.target.id);
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var modals = document.querySelectorAll('.modal.active');
        modals.forEach(function(modal) {
            closeModal(modal.id);
        });
    }
});

// ---- Notification ----

function showNotification(title, message, duration) {
    if (!duration) { duration = 4000; }

    var notification = document.createElement('div');
    notification.className = 'notification';
    notification.innerHTML =
        '<div class="notification-header">' +
            '<span class="notification-title">' + title + '</span>' +
            '<button class="notification-close" onclick="this.parentElement.parentElement.remove()">x</button>' +
        '</div>' +
        '<div class="notification-message">' + message + '</div>';

    document.body.appendChild(notification);

    setTimeout(function() {
        if (notification.parentElement) {
            notification.remove();
        }
    }, duration);
}

// ---- Table Search ----

function searchTable(inputId, tableId) {
    var input = document.getElementById(inputId);
    var table = document.getElementById(tableId);

    if (!input || !table) { return; }

    var filter = input.value.toLowerCase();
    var rows = table.getElementsByTagName('tr');

    for (var i = 1; i < rows.length; i++) {
        var text = rows[i].textContent.toLowerCase();
        rows[i].style.display = (text.indexOf(filter) > -1) ? '' : 'none';
    }
}

// ---- Print ----

function printTicket() {
    window.print();
}

// ---- Date / Time formatting ----

function formatDate(dateString) {
    var date = new Date(dateString);
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
}

function formatTime(timeString) {
    return timeString.substring(0, 5);
}

function formatDateTime(dateTimeString) {
    var date = new Date(dateTimeString);
    return formatDate(dateTimeString) + ' ' + date.getHours() + ':' + String(date.getMinutes()).padStart(2, '0');
}

// ---- Currency ----

function formatCurrency(amount) {
    return 'Rs. ' + parseFloat(amount).toFixed(2);
}

// ---- Export Table to CSV ----

function exportTableToCSV(tableId, filename) {
    if (!filename) { filename = 'export.csv'; }

    var table = document.getElementById(tableId);
    if (!table) { return; }

    var csv = [];
    var rows = table.querySelectorAll('tr');

    for (var i = 0; i < rows.length; i++) {
        var cols = rows[i].querySelectorAll('td, th');
        var rowData = [];
        for (var j = 0; j < cols.length; j++) {
            rowData.push('"' + cols[j].textContent.replace(/"/g, '""') + '"');
        }
        csv.push(rowData.join(','));
    }

    var blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    var url = window.URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

// ---- Status Helper Functions ----

function getStatusClass(status) {
    var map = {
        'On-Time'   : 'badge-success',
        'Boarding'  : 'badge-info',
        'Delayed'   : 'badge-warning',
        'Cancelled' : 'badge-danger'
    };
    return map[status] || 'badge-info';
}

function getStatusDot(status) {
    var map = {
        'On-Time'   : 'status-ontime',
        'Boarding'  : 'status-boarding',
        'Delayed'   : 'status-delayed',
        'Cancelled' : 'status-cancelled'
    };
    return map[status] || 'status-ontime';
}

function getBaggageStatusClass(status) {
    var map = {
        'Checked-in' : 'badge-info',
        'Loaded'     : 'badge-success',
        'In Transit' : 'badge-warning',
        'At Belt'    : 'badge-success'
    };
    return map[status] || 'badge-info';
}

// ---- Form Validation ----

function validateForm(formId) {
    var form = document.getElementById(formId);
    if (!form) { return false; }

    var isValid = true;
    var inputs = form.querySelectorAll('[required]');

    for (var i = 0; i < inputs.length; i++) {
        if (!inputs[i].value.trim()) {
            inputs[i].style.borderColor = '#cc0000';
            isValid = false;
        } else {
            inputs[i].style.borderColor = '';
        }
    }
    return isValid;
}

// ---- Theme Toggle ----

function initTheme() {
    var saved = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', saved);
}

function toggleTheme() {
    var current = document.documentElement.getAttribute('data-theme') || 'dark';
    var next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
}

initTheme();

// ---- Smooth Scroll (simplified) ----

function smoothScroll(targetId) {
    var el = document.getElementById(targetId);
    if (el) { el.scrollIntoView(); }
}

// ---- Logout Confirmation ----

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}

// ---- Global AirportApp Object ----

window.AirportApp = {
    openModal             : openModal,
    closeModal            : closeModal,
    showNotification      : showNotification,
    searchTable           : searchTable,
    printTicket           : printTicket,
    formatDate            : formatDate,
    formatTime            : formatTime,
    formatDateTime        : formatDateTime,
    formatCurrency        : formatCurrency,
    exportTableToCSV      : exportTableToCSV,
    getStatusClass        : getStatusClass,
    getStatusDot          : getStatusDot,
    getBaggageStatusClass : getBaggageStatusClass,
    validateForm          : validateForm,
    smoothScroll          : smoothScroll,
    logout                : logout
};
