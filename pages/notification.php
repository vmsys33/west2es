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

<!-- <div class="col-md-9 main-content">
    <h3 class="mb-3">Notifications</h3>
    <div class="table-responsive">
        <table class="table table-bordered" id="notificationsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th>DateTime</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div> -->


<div class="col-md-9 main-content">
    <h3 class="mb-3">Notifications</h3>

    <div class="table-responsive">
        <table class="table table-bordered" id="notificationsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th>DateTime</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- Notifications will be dynamically loaded here -->
            </tbody>
        </table>
    </div>
</div>


<?php include '../includes/footer.php'; ?>

<!-- DataTable DateTime Sorting Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.6/sorting/datetime-moment.js"></script>

<script>
  function formatDateTime(datetime) {
    const date = new Date(datetime); // Convert string to a Date object

    // Format the date to "Month Day, Year Hour:Minute AM/PM"
    const options = { 
      month: 'long', 
      day: 'numeric', 
      year: 'numeric', 
      hour: 'numeric', 
      minute: 'numeric', 
      hour12: true // Use 12-hour clock
    };
    
    return date.toLocaleString('en-US', options);
  }
</script>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Load notifications directly (no auto-mark since we only show seen notifications)
        
    const loadNotifications = () => {
        console.log('Loading notifications...'); // Debug log
        
        fetch('../functions/fetch_notifications.php')
            .then(response => {
                console.log('Response status:', response.status); // Debug log
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Raw response data:', data); // Debug log
                
                if (data.status === 'success') {
                    const tbody = document.querySelector('#notificationsTable tbody');
                    let counter = 1;
                    tbody.innerHTML = ''; // Clear the table body
                    
                    console.log('Fetched notifications:', data.data); // Debug log
                    console.log('Number of notifications:', data.data.length); // Debug log
                    
                                         if (data.data.length === 0) {
                         tbody.innerHTML = '<tr><td colspan="4" class="text-center">No notifications found</td></tr>';
                         return;
                     }
                    
                                                              data.data.forEach(notification => {
                         // Handle both seen and unseen notifications
                         const isSeen = notification.seen == 1;
                         const rowClass = isSeen ? 'table-secondary' : 'table-warning';
                         const statusBadge = isSeen ? 
                             '<span class="badge bg-secondary">Seen</span>' : 
                             '<span class="badge bg-warning text-dark">New</span>';
                         
                         const row = `
                             <tr class="${rowClass}">
                                 <td>${counter++}</td>
                                 <td>${notification.description}</td>
                                 <td>${formatDateTime(notification.created_at)}</td>
                                 <td>${statusBadge}</td>
                             </tr>
                         `;
                         tbody.insertAdjacentHTML('beforeend', row);
                     });
                    
                    // Destroy existing DataTable if it exists
                    if ($.fn.DataTable.isDataTable('#notificationsTable')) {
                        $('#notificationsTable').DataTable().destroy();
                    }
                    
                    // Initialize DataTable after the data is loaded
                    try {
                        // Configure moment.js for datetime sorting
                        $.fn.dataTable.moment('YYYY-MM-DD HH:mm:ss');
                        
                        $('#notificationsTable').DataTable({
                            responsive: true,
                            pageLength: 25, // Show 25 entries per page
                            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // Page length options
                            order: [[2, 'desc']], // Sort by date column (index 2) in descending order
                            columnDefs: [
                                {
                                    targets: 0, // Counter column
                                    orderable: false, // Disable sorting for counter column
                                    searchable: false
                                },
                                {
                                    targets: 1, // Description column
                                    orderable: true,
                                    searchable: true
                                },
                                {
                                    targets: 2, // DateTime column
                                    orderable: true,
                                    searchable: false,
                                    type: 'datetime', // Specify datetime type for proper sorting
                                    render: function(data, type, row) {
                                        if (type === 'display') {
                                            return formatDateTime(data);
                                        }
                                        // For sorting, return the raw datetime value
                                        return data;
                                    }
                                },
                                {
                                    targets: 3, // Status column
                                    orderable: false, // Disable sorting for status column
                                    searchable: false
                                }
                            ],
                            language: {
                                search: "Search notifications:",
                                lengthMenu: "Show _MENU_ notifications per page",
                                info: "Showing _START_ to _END_ of _TOTAL_ notifications",
                                emptyTable: "No notifications found"
                            },
                            // Ensure proper sorting on initialization
                            initComplete: function() {
                                console.log('DataTable initialized with sorting applied');
                                // Force re-sort to ensure proper order
                                this.api().order([2, 'desc']).draw();
                            }
                        });
                        console.log('DataTable initialized successfully'); // Debug log
                    } catch (error) {
                        console.error('DataTable initialization error:', error); // Debug log
                        // Show a simple message that DataTable failed but data is loaded
                        const tableContainer = document.querySelector('.table-responsive');
                        tableContainer.innerHTML += '<div class="alert alert-info mt-2">DataTable failed to initialize, but notifications are displayed above.</div>';
                    }
                                 } else {
                     console.error('Error:', data.message);
                     const tbody = document.querySelector('#notificationsTable tbody');
                     tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading notifications: ' + data.message + '</td></tr>';
                 }
            })
                         .catch(error => {
                 console.error('Error fetching notifications:', error);
                 const tbody = document.querySelector('#notificationsTable tbody');
                 tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading notifications. Please try again.</td></tr>';
             });
    };



    // Function to update notification badges
    const updateNotificationBadges = () => {
        fetch('../functions/get_notification_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update notification badge
                const notificationBadge = document.querySelector('a[href="notification.php"] .badge');
                if (notificationBadge) {
                    if (data.notification_count > 0) {
                        notificationBadge.textContent = data.notification_count;
                        notificationBadge.style.display = 'inline';
                    } else {
                        notificationBadge.style.display = 'none';
                    }
                }
                
                                 // Update pending users badge (admin only)
                 const pendingUsersBadge = document.querySelector('.pending-users-badge');
                 if (pendingUsersBadge) {
                     if (data.pending_users_count > 0) {
                         pendingUsersBadge.textContent = data.pending_users_count;
                         pendingUsersBadge.style.display = 'inline';
                     } else {
                         pendingUsersBadge.style.display = 'none';
                     }
                 }
                
                                 // Update pending files badge (admin only)
                 const pendingFilesBadge = document.querySelector('.pending-files-badge');
                 if (pendingFilesBadge) {
                     if (data.pending_files_count > 0) {
                         pendingFilesBadge.textContent = data.pending_files_count;
                         pendingFilesBadge.style.display = 'inline';
                     } else {
                         pendingFilesBadge.style.display = 'none';
                     }
                 }
            }
        })
        .catch(error => console.error('Error updating badges:', error));
    };

         // Load notifications when the page loads
     loadNotifications();
});

</script>


