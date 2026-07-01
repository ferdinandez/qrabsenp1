// script.js

document.addEventListener('DOMContentLoaded', function() {
    // Barcode form handler
    const barcodeForm = document.getElementById('barcodeForm');
    if (barcodeForm) {
        barcodeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const barcode = document.getElementById('barcodeInput').value;
            const checkInType = document.getElementById('checkInType').value;
            
            if (!barcode || !checkInType) {
                showStatus('danger', 'Harap isi barcode dan pilih tipe absensi');
                return;
            }
            
            // Send to backend
            fetch('api_absensi.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'process_barcode',
                    barcode: barcode,
                    type: checkInType,
                    timestamp: new Date().toISOString()
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus('success', data.message);
                    document.getElementById('barcodeInput').value = '';
                    document.getElementById('checkInType').value = '';
                    loadTodayStatus();
                } else {
                    showStatus('danger', data.message || 'Proses absensi gagal');
                }
            })
            .catch(error => {
                showStatus('danger', 'Error: ' + error.message);
            });
        });
    }

    const checkInBtn = document.getElementById('checkInBtn');
    const checkOutBtn = document.getElementById('checkOutBtn');
    const statusDiv = document.getElementById('status');
    
    if (checkInBtn) {
        checkInBtn.addEventListener('click', function() {
            const barcode = '1'; // Use user_id from session (default 1)
            processBarcodeAction(barcode, 'checkin');
        });
    }
    
    if (checkOutBtn) {
        checkOutBtn.addEventListener('click', function() {
            const barcode = '1'; // Use user_id from session (default 1)
            processBarcodeAction(barcode, 'checkout');
        });
    }

    // Load report
    const loadReportBtn = document.getElementById('loadReportBtn');
    if (loadReportBtn) {
        loadReportBtn.addEventListener('click', loadReport);
    }

    // Load dashboard history on page load
    if (document.getElementById('riwayatBody')) {
        loadHistory();
    }
    
    // Load today status on absensi page
    if (document.getElementById('checkInTime')) {
        loadTodayStatus();
    }

    // Update current time
    const currentTimeSpan = document.getElementById('currentTime');
    if (currentTimeSpan) {
        updateTime();
        setInterval(updateTime, 1000);
    }
});

function performAbsensi(type) {
    const apiUrl = 'http://localhost:8000/api/absensi/' + type; // Ganti dengan URL Laravel Anda
    const token = localStorage.getItem('token') || 'dummy-token'; // Get real token

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({
            user_id: 1, // Get from session or token
            timestamp: new Date().toISOString()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatus('success', type + ' berhasil pada ' + new Date().toLocaleString());
            loadTodayStatus(); // Reload status
        } else {
            showStatus('danger', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        showStatus('danger', 'Error: ' + error.message);
    });
}

function loadHistory() {
    const apiUrl = 'http://localhost:8000/api/absensi/history'; // Ganti dengan URL Laravel
    const token = localStorage.getItem('token') || 'dummy-token';

    fetch(apiUrl, {
        headers: {
            'Authorization': 'Bearer ' + token
        }
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('riwayatBody');
        tbody.innerHTML = '';
        if (data.success && data.data) {
            data.data.slice(0, 5).forEach(item => { // Show last 5
                const row = `<tr>
                    <td>${item.date}</td>
                    <td>${item.check_in || '-'}</td>
                    <td>${item.check_out || '-'}</td>
                    <td>${item.status}</td>
                </tr>`;
                tbody.innerHTML += row;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4">Tidak ada data</td></tr>';
        }
    })
    .catch(error => console.error('Error loading history:', error));
}

function loadReport() {
    const month = document.getElementById('monthSelect').value;
    const apiUrl = `http://localhost:8000/api/absensi/report?month=${month}`;
    const token = localStorage.getItem('token') || 'dummy-token';

    fetch(apiUrl, {
        headers: {
            'Authorization': 'Bearer ' + token
        }
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('reportBody');
        tbody.innerHTML = '';
        if (data.success && data.data) {
            data.data.forEach(item => {
                const row = `<tr>
                    <td>${item.date}</td>
                    <td>${item.check_in || '-'}</td>
                    <td>${item.check_out || '-'}</td>
                    <td>${item.duration || '-'}</td>
                    <td>${item.status}</td>
                </tr>`;
                tbody.innerHTML += row;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5">Tidak ada data</td></tr>';
        }
    })
    .catch(error => console.error('Error loading report:', error));
}

function loadTodayStatus() {
    const apiUrl = 'api_absensi.php';

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'get_today_status'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            const attendance = data.data;
            
            // Update Check In time
            const checkInElement = document.getElementById('checkInTime');
            if (checkInElement) {
                if (attendance.check_in) {
                    checkInElement.innerHTML = '<span class="badge bg-success">' + attendance.check_in + '</span>';
                } else {
                    checkInElement.innerHTML = '<span class="badge bg-warning">Belum Check In</span>';
                }
            }
            
            // Update Check Out time
            const checkOutElement = document.getElementById('checkOutTime');
            if (checkOutElement) {
                if (attendance.check_out) {
                    checkOutElement.innerHTML = '<span class="badge bg-success">' + attendance.check_out + '</span>';
                } else {
                    checkOutElement.innerHTML = '<span class="badge bg-secondary">Belum Check Out</span>';
                }
            }
            
            // Update Duration
            const durationElement = document.getElementById('workDuration');
            if (durationElement) {
                if (attendance.duration) {
                    durationElement.innerHTML = '<span class="badge bg-info">' + attendance.duration + '</span>';
                } else {
                    durationElement.innerHTML = '<span class="badge bg-secondary">-</span>';
                }
            }
            
            // Update Status
            const statusElement = document.getElementById('absensiStatus');
            if (statusElement) {
                if (attendance.status === 'present') {
                    statusElement.innerHTML = '<span class="badge bg-success">Lengkap</span>';
                } else if (attendance.status === 'partial') {
                    statusElement.innerHTML = '<span class="badge bg-warning">Sebagian</span>';
                } else {
                    statusElement.innerHTML = '<span class="badge bg-secondary">Belum Ada</span>';
                }
            }
        }
    })
    .catch(error => console.error('Error loading today status:', error));
}



function updateTime() {
    const now = new Date();
    document.getElementById('currentTime').textContent = now.toLocaleString();
}

function processBarcodeAction(barcode, type) {
    fetch('api_absensi.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'process_barcode',
            barcode: barcode,
            type: type,
            timestamp: new Date().toISOString()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatus('success', data.message);
            loadTodayStatus();
        } else {
            showStatus('danger', data.message || 'Proses absensi gagal');
        }
    })
    .catch(error => {
        showStatus('danger', 'Error: ' + error.message);
    });
}

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.classList.remove('d-none');
    }
}

function showStatus(type, message) {
    const statusDiv = document.getElementById('status');
    if (statusDiv) {
        statusDiv.className = `alert alert-${type}`;
        statusDiv.textContent = message;
        statusDiv.classList.remove('d-none');
    }
}