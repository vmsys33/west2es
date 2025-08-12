<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);

require_once '../functions/pageTitle.php';
$pageTitle = getPageTitle($currentPage);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/top-navbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-md-9 main-content">
    <h3 class="mb-3">Notifications (Simple Version)</h3>
    
    <div class="mb-2">
        <button class="btn btn-success" id="deleteSelected">Mark Selected as Seen</button>
        <button class="btn btn-primary" onclick="location.reload()">Refresh</button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered" id="notificationsTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>#</th>
                    <th>Description</th>
                    <th>DateTime</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="notificationsTableBody">
                <tr>
                    <td colspan="6" class="text-center">Loading notifications...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
function formatDateTime(datetime) {
    const date = new Date(datetime);
    const options = { 
        month: 'long', 
        day: 'numeric', 
        year: 'numeric', 
        hour: 'numeric', 
        minute: 'numeric', 
        hour12: true
    };
    return date.toLocaleString('en-US', options);
}

document.addEventListener('DOMContentLoaded', function () {
    console.log('Page loaded, starting to fetch notifications...');
    
    // Load notifications immediately
    loadNotifications();
    
    function loadNotifications() {
        console.log('Fetching notifications from:', '../functions/fetch_notifications.php');
        
        fetch('../functions/fetch_notifications.php')
            .then(response => {
                console.log('Response received, status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                
                const tbody = document.getElementById('notificationsTableBody');
                
                if (data.status === 'success') {
                    if (data.data && data.data.length > 0) {
                        console.log('Found', data.data.length, 'notifications');
                        
                        let html = '';
                        data.data.forEach((notification, index) => {
                            const isSeen = notification.seen == 1;
                            const rowClass = isSeen ? 'table-secondary' : 'table-warning';
                            const buttonText = isSeen ? 'Already Seen' : 'Mark as Seen';
                            const buttonClass = isSeen ? 'btn-secondary btn-sm' : 'btn-success btn-sm';
                            const buttonDisabled = isSeen ? 'disabled' : '';
                            
                            const statusBadge = isSeen ? 
                                '<span class="badge bg-secondary">Seen</span>' : 
                                '<span class="badge bg-warning text-dark">Unseen</span>';
                            
                            html += `
                                <tr class="${rowClass}">
                                    <td><input type="checkbox" class="selectNotification" data-id="${notification.id}" ${isSeen ? 'disabled' : ''}></td>
                                    <td>${index + 1}</td>
                                    <td>${notification.description}</td>
                                    <td>${formatDateTime(notification.created_at)}</td>
                                    <td>${statusBadge}</td>
                                    <td>
                                        <button class="btn ${buttonClass} mark-seen" data-id="${notification.id}" ${buttonDisabled}>
                                            ${buttonText}
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        tbody.innerHTML = html;
                        console.log('Notifications loaded successfully');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No notifications found</td></tr>';
                        console.log('No notifications found');
                    }
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error: ' + (data.message || 'Unknown error') + '</td></tr>';
                    console.error('Error in response:', data.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                const tbody = document.getElementById('notificationsTableBody');
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading notifications: ' + error.message + '</td></tr>';
            });
    }
    
    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.selectNotification:not(:disabled)');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});
</script>
