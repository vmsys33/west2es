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
    <div class="mb-2">
        <button class="btn btn-danger" id="deleteSelected">Delete Selected</button>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered" id="notificationsTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>#</th>
                    <th>Description</th>
                    <th>DateTime</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Notifications will be dynamically loaded here -->
            </tbody>
        </table>
    </div>
</div>


<?php include '../includes/footer.php'; ?>

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
    const loadNotifications = () => {
        fetch('../functions/fetch_notifications.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    const tbody = document.querySelector('#notificationsTable tbody');
                    let counter = 1;
                    tbody.innerHTML = ''; // Clear the table body
                    data.data.forEach(notification => {
                        const row = `
                            <tr>
                                <td><input type="checkbox" class="selectNotification" data-id="${notification.id}"></td>
                                <td>${counter++}</td>
                                <td>${notification.description}</td>
                                <td>${formatDateTime(new Date(notification.created_at).toLocaleString())}</td>
                                <td>
                                    <button class="btn btn-success btn-sm mark-seen" data-id="${notification.id}">
                                        Mark as Seen
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                    // Initialize DataTable after the data is loaded
                    $('#notificationsTable').DataTable({
                        responsive: true
                    });
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    };

    // Load notifications when the page loads
    loadNotifications();

    // Event delegation for handling mark-as-seen button click
    document.querySelector('#notificationsTable tbody').addEventListener('click', function (e) {
        if (e.target.closest('.mark-seen')) {
            const notificationId = e.target.closest('.mark-seen').getAttribute('data-id');
            markNotificationAsSeen(notificationId);
        }
    });

    // Function to mark notification as seen
    const markNotificationAsSeen = (id) => {
        fetch('../functions/toggle_notification_seen.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    }).then(() => {
                      
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error updating notification:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong. Please try again later.'
                });
            });
    };




    // Select All checkbox functionality
    document.getElementById('selectAll').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.selectNotification');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Mark selected notifications as seen
    document.getElementById('deleteSelected').addEventListener('click', function () {
        const selectedIds = Array.from(document.querySelectorAll('.selectNotification:checked')).map(
            checkbox => checkbox.getAttribute('data-id')
        );

        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Selection',
                text: 'Please select at least one notification to mark as seen.'
            });
            return;
        }

        // Confirm action
        Swal.fire({
            title: 'Are you sure?',
            text: 'Mark selected notifications as seen?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('../functions/mark_notifications_seen.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids: selectedIds })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message
                            }).then(() => {
                                // loadNotifications(); // Refresh the table
                                location.reload();


                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error updating notifications:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again later.'
                        });
                    });
            }
        });
    });
});

</script>


