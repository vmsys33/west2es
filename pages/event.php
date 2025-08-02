<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}



// Get the current file name
$currentPage = basename($_SERVER['PHP_SELF']);

require_once '../functions/pageTitle.php';
$pageTitle = getPageTitle($currentPage);
?>


<?php include '../includes/header.php'; ?>
<?php include '../includes/top-navbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>


  

<div class="col-md-9 main-content">
    <h3 class="mb-3">Event Management</h3>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addEventModal">Add Event</button>

    
  

    <h4 class="mb-3">List of Events</h4>
    <div class="table-responsive">
        <table id="eventsTable" class="table table-bordered">
            <colgroup>
        <col width="5%"> <!-- No. -->
        <col width="15%"> <!-- Filename -->
        <col width="15%"> <!-- Date & Time -->
        <col width="10%"> <!-- Uploader -->
        <col width="10%"> <!-- Actions -->
        <col width="15%"> <!-- Actions -->
            </colgroup>
            <thead>
                <tr>
                    <th>Id No</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Date & Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be dynamically populated here -->
            </tbody>
        </table>
    </div>
</div>




<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">Add Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEventForm" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="eventName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="eventName" name="name" required pattern="^[a-zA-Z0-9 ]{3,50}$">
                        <div class="invalid-feedback">Event name should be between 3-50 characters long and can only contain letters, numbers, and spaces.</div>
                    </div>
                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" name="description" rows="3" required></textarea>
                        <div class="invalid-feedback">Please provide a description for the event.</div>
                    </div>
                    <div class="mb-3">
                        <label for="eventLocation" class="form-label">Location</label>
                        <input type="text" class="form-control" id="eventLocation" name="location" required>
                        <div class="invalid-feedback">Please provide a location for the event.</div>
                    </div>
                    <div class="mb-3">
                        <label for="eventDateTime" class="form-label">Date & Time</label>
                        <input type="datetime-local" class="form-control" id="eventDateTime" name="datetime" required>
                        <div class="invalid-feedback">Please provide a valid date and time for the event.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editEventForm" method="POST" novalidate>
                    <!-- Hidden input to store event ID -->
                    <input type="hidden" id="editEventId" name="id">

                    <div class="mb-3">
                        <label for="editEventName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editEventName" name="name" required pattern="^[a-zA-Z0-9 ]{3,50}$">
                        <div class="invalid-feedback">Event name should be between 3-50 characters long and can only contain letters, numbers, and spaces.</div>
                    </div>
                    <div class="mb-3">
                        <label for="editEventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editEventDescription" name="description" rows="3" required></textarea>
                        <div class="invalid-feedback">Please provide a description for the event.</div>
                    </div>
                    <div class="mb-3">
                        <label for="editEventLocation" class="form-label">Location</label>
                        <input type="text" class="form-control" id="editEventLocation" name="location" required>
                        <div class="invalid-feedback">Please provide a location for the event.</div>
                    </div>
                    <div class="mb-3">
                        <label for="editEventDateTime" class="form-label">Date & Time</label>
                        <input type="datetime-local" class="form-control" id="editEventDateTime" name="datetime" required>
                        <div class="invalid-feedback">Please provide a valid date and time for the event.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>



  <?php include '../includes/footer.php'; ?>


  


<script>
    document.addEventListener('DOMContentLoaded', function () {
    const eventsTable = document.querySelector('#eventsTable tbody');


     // Retrieve the user role (set this dynamically for each context)
    const userRole = document.body.dataset.userRole; // Assume the role is passed as a `data-user-role` attribute in <body>


    // Initialize DataTable instance with responsive option
    let eventsTableInstance = $('#eventsTable').DataTable({
  responsive: true  // Enable responsiveness
    });
    




    const loadEvents = () => {
        fetch('../functions/fetch_events.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Clear existing rows in DataTable
                    eventsTableInstance.clear();

                     // Initialize a counter for numbering
                    let counter = 1;

                    // Add rows dynamically
                    data.data.forEach(event => {
                        let actionButtons = `
                            <button class="btn btn-warning btn-sm edit-event" data-id="${event.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                        `;

                        // Add delete button only if userRole is admin
                        if (userRole === 'admin') {
                            actionButtons += `
                                <button class="btn btn-danger btn-sm delete-event" data-id="${event.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;
                        }

                        eventsTableInstance.row.add([
                            counter, 
                            event.name,
                            event.description,
                            event.location,
                            formatDateTime(event.datetime),
                            actionButtons
                        ]);

                         // Increment the counter
                        counter++;
                    });

                    // Redraw DataTable to reflect changes
                    eventsTableInstance.draw();
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => console.error('Error fetching events:', error));
    };

    // Load events when the page is loaded
    loadEvents();

    
    


    
    
    // Format DateTime function
    const formatDateTime = (datetime) => {
        const date = new Date(datetime);
        return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
    };




    // Event delegation for both Edit and Delete buttons
    eventsTable.addEventListener('click', function (e) {
        // Handle Edit Button Click
        if (e.target.closest('.edit-event')) {
            const id = e.target.closest('.edit-event').dataset.id;
            fetchEventDetails(id); // Call fetchEventDetails for the selected event
        }

        // Handle Delete Button Click
        if (e.target.closest('.delete-event')) {
            const id = e.target.closest('.delete-event').dataset.id;

            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('../functions/delete_event.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Deleted!', data.message, 'success').then(() => loadEvents());
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(error => console.error('Error deleting event:', error));
                }
            });
        }
    });

    // Fetch event details for editing
    const fetchEventDetails = (id) => {
        fetch(`../functions/fetch_event.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('editEventId').value = data.data.id;
                    document.getElementById('editEventName').value = data.data.name;
                    document.getElementById('editEventDescription').value = data.data.description;
                    document.getElementById('editEventLocation').value = data.data.location;
                    document.getElementById('editEventDateTime').value = data.data.datetime;

                    // Show the modal
                    const editEventModal = new bootstrap.Modal(document.getElementById('editEventModal'));
                    editEventModal.show();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => console.error('Error fetching event details:', error));
    };

    // Initial load
    loadEvents(); // Initial load
});

</script>





<!-- Add event -->
<script>
 document.addEventListener('DOMContentLoaded', function () {
    const addEventForm = document.getElementById('addEventForm');
    const saveButton = addEventForm.querySelector('button[type="submit"]'); // Select the Save Event button

    if (addEventForm) {
        addEventForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            // Check form validity
            if (!addEventForm.checkValidity()) {
                addEventForm.classList.add('was-validated'); // Add Bootstrap validation feedback
                return; // Stop further execution if the form is invalid
            }

            // Disable the save button to prevent double submissions
            saveButton.disabled = true;
            saveButton.textContent = 'Saving...'; // Optional: Update button text to indicate saving in progress

            // If the form is valid, proceed with AJAX submission
            const formData = new FormData(addEventForm);

            fetch('../functions/add_event.php', {
                method: 'POST',
                body: formData, // Use FormData for AJAX submission
            })
                .then(response => response.json()) // Parse the JSON response
                .then(data => {
                    if (data.status === 'success') {
                        // Show SweetAlert success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Event Added Successfully!',
                            text: data.message,
                        }).then(() => {
                            // Refresh the page or dynamically update event list
                            const modal = bootstrap.Modal.getInstance(document.getElementById('addEventModal'));
                            modal.hide(); // Close modal
                            location.reload(); // Reload the page
                        });
                    } else {
                        // Show SweetAlert error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error adding event:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'An Error Occurred!',
                        text: 'Something went wrong. Please try again.',
                        showConfirmButton: true,
                    });
                })
                .finally(() => {
                    // Re-enable the button after the process is complete
                    saveButton.disabled = false;
                    saveButton.textContent = 'Save Event'; // Restore the original button text
                });
        });
    }
});


</script>



<!-- Edit event --> 
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const editEventForm = document.getElementById('editEventForm');
    const saveChangesButton = editEventForm.querySelector('button[type="submit"]'); // Save Changes button

    if (editEventForm) {
        editEventForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            // Check form validity
            if (!editEventForm.checkValidity()) {
                editEventForm.classList.add('was-validated'); // Add Bootstrap validation feedback
                return; // Stop further execution if the form is invalid
            }

            // Disable the save button to prevent double submissions
            saveChangesButton.disabled = true;
            saveChangesButton.textContent = 'Saving...'; // Optional: Indicate saving process

            // Create FormData object for AJAX submission
            const formData = new FormData(editEventForm);

            fetch('../functions/edit_event.php', {
                method: 'POST',
                body: formData, // Send FormData
            })
                .then(response => response.json()) // Parse JSON response
                .then(data => {
                    if (data.status === 'success') {
                        // Show SweetAlert success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Event Updated Successfully!',
                            text: data.message,
                        }).then(() => {
                            // Close the modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editEventModal'));
                            modal.hide(); // Close modal

                            // Reload the page to reflect changes
                            location.reload();
                        });
                    } else {
                        // Show SweetAlert error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error updating event:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'An Error Occurred!',
                        text: 'Something went wrong. Please try again.',
                    });
                })
                .finally(() => {
                    // Re-enable the button after submission
                    saveChangesButton.disabled = false;
                    saveChangesButton.textContent = 'Save Changes'; // Reset button text
                });
        });
    }

    // Function to populate the edit modal with event data
    window.populateEditModal = function (event) {
        document.getElementById('editEventId').value = event.id;
        document.getElementById('editEventName').value = event.name;
        document.getElementById('editEventDescription').value = event.description;
        document.getElementById('editEventLocation').value = event.location;
        document.getElementById('editEventDateTime').value = event.datetime;
        const modal = new bootstrap.Modal(document.getElementById('editEventModal'));
        modal.show(); // Open the modal
    };
});

</script>
<!-- edit event end


