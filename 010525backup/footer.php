   <!-- Footer Section -->
  <footer class="bg-primary text-white text-center py-3 mt-auto">
    <div class="container">
      <p class="mb-0">© 2024 Cadiz West 2 Elementary School Portal. All rights reserved.</p>
      <!-- <p class="mb-0">Developed by Gweneth | Powered by Bootstrap</p> -->
    
    </div>
  </footer>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <script>
    $(document).ready(function () {
    $('#searchInput').on('input', function () {
        const query = $(this).val().trim();
        if (query.length > 0) {
            fetch(`../functions/search_suggestions.php?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        let resultsHtml = '';
                        data.data.forEach(file => {
                            resultsHtml += `
                                <li class="list-group-item">
                                    <a href="#" class="search-result" data-table="${file.source_table}" data-id="${file.id}">
                                        ${file.filename}
                                    </a>
                                </li>
                            `;
                        });

                        $('#searchResults').html(resultsHtml).removeClass('d-none'); // Show results
                    } else if (data.status === 'no_results') {
                        $('#searchResults').html('<li class="list-group-item text-muted">No results found</li>').removeClass('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else {
            $('#searchResults').addClass('d-none').html(''); // Hide results if query is empty
        }
    });

    $(document).on('click', '.search-result', function (e) {
        e.preventDefault();

        const table = $(this).data('table');
        const id = $(this).data('id');

        // Fetch file details and show them in the modal
        fetch(`../functions/fetch_file_details.php?table=${table}&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    $('#fileDetailsModal .modal-body').html(`
                        <p><strong>Filename:</strong> ${data.file.filename}</p>
                        <p><strong>Table:</strong> ${data.file.source_table}</p>
                        <p><strong>ID:</strong> ${data.file.id}</p>
                    `);

                    $('#fileDetailsModal').modal('show');
                } else {
                    alert('Error fetching file details.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    // search-result end    

});

  </script>



  <script>
    document.addEventListener("DOMContentLoaded", () => {
  const sidebarMenus = document.querySelectorAll(".has-submenu");

  // Collapse all menus and submenus by default
  function initializeMenus() {
    sidebarMenus.forEach((menu) => {
      const submenu = menu.querySelector(".submenu");
      const activeLink = menu.querySelector(".nav-link.active");

      // Collapse all menus unless an active link is present
      if (activeLink) {
        menu.classList.add("show");
        if (submenu) submenu.classList.remove("collapse"); // Expand submenu
        const icon = menu.querySelector(".fa-chevron-right");
        if (icon) icon.style.transform = "rotate(90deg)"; // Point chevron down
      } else {
        menu.classList.remove("show");
        if (submenu) submenu.classList.add("collapse"); // Collapse submenu
        const icon = menu.querySelector(".fa-chevron-right");
        if (icon) icon.style.transform = "rotate(0deg)"; // Point chevron right
      }
    });
  }

  // Expand menus containing the active link
  function expandActiveMenu() {
    const activeLink = document.querySelector(".nav-link.active");
    if (activeLink) {
      let parentMenu = activeLink.closest(".has-submenu");
      while (parentMenu) {
        parentMenu.classList.add("show");
        const submenu = parentMenu.querySelector(".submenu");
        if (submenu) submenu.classList.remove("collapse"); // Show submenu
        const icon = parentMenu.querySelector(".fa-chevron-right");
        if (icon) icon.style.transform = "rotate(90deg)"; // Point chevron down
        parentMenu = parentMenu.parentElement.closest(".has-submenu");
      }
    }
  }

  // Add toggle functionality for menus and submenus
  function addToggleFunctionality() {
    sidebarMenus.forEach((menu) => {
      const link = menu.querySelector("a.nav-link");
      const submenu = menu.querySelector(".submenu");

      link.addEventListener("click", (e) => {
        e.preventDefault();

        const isOpen = menu.classList.contains("show");

        // Collapse other sibling menus
        const parentMenu = menu.parentElement; // Find the parent menu
        const siblingMenus = parentMenu.querySelectorAll(".has-submenu");
        siblingMenus.forEach((sibling) => {
          if (sibling !== menu) {
            sibling.classList.remove("show");
            const siblingSubmenu = sibling.querySelector(".submenu");
            if (siblingSubmenu) siblingSubmenu.classList.add("collapse"); // Hide sibling submenu
            const siblingIcon = sibling.querySelector(".fa-chevron-right");
            if (siblingIcon) siblingIcon.style.transform = "rotate(0deg)"; // Point chevron right
          }
        });

        // Toggle the current menu
        if (!isOpen) {
          menu.classList.add("show");
          if (submenu) submenu.classList.remove("collapse"); // Show submenu
          const icon = link.querySelector(".fa-chevron-right");
          if (icon) icon.style.transform = "rotate(90deg)"; // Point chevron down
        } else {
          menu.classList.remove("show");
          if (submenu) submenu.classList.add("collapse"); // Hide submenu
          const icon = link.querySelector(".fa-chevron-right");
          if (icon) icon.style.transform = "rotate(0deg)"; // Point chevron right
        }
      });
    });
  }

  // Initialize menus
  initializeMenus();

  // Expand active menu if an active link exists
  expandActiveMenu();

  // Add toggle functionality
  addToggleFunctionality();

  // Sidebar toggle for mobile view
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebar = document.querySelector(".sidebar");

  sidebarToggle.addEventListener("click", () => {
    sidebar.classList.toggle("show");
  });
});

  </script>

  


<script>
document.addEventListener('DOMContentLoaded', function () {
    const logoutButton = document.getElementById('logoutButton');

    if (logoutButton) {
        logoutButton.addEventListener('click', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Logout!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        });
    }
});
</script>



<script>
  document.addEventListener('DOMContentLoaded', function () {
    const loadPendingUsers = () => {
        fetch('../functions/fetch_pending_users.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    const tbody = document.querySelector('#pendingUsersTable tbody');
                    tbody.innerHTML = ''; // Clear the table body
                    data.data.forEach(user => {
                        const row = `
                            <tr>
                                <td>${user.deped_id_no}</td>
                                <td>${user.last_name}</td>
                                <td>${user.first_name}</td>
                                <td>${user.middle_name}</td>
                                <td>${user.email}</td>
                                <td>
                                    <button class="btn btn-success btn-sm activate-user" data-id="${user.id_no}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-user" data-id="${user.id_no}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => console.error('Error fetching pending users:', error));
    };

    // Load the data when the page loads
    loadPendingUsers();

    // Event delegation for handling activate button click
    document.querySelector('#pendingUsersTable tbody').addEventListener('click', function (e) {
        if (e.target.closest('.activate-user')) {
            const id_no = e.target.closest('.activate-user').getAttribute('data-id');
            toggleUserStatus(id_no, 'active');
        }
    });

    // Function to toggle user status
    const toggleUserStatus = (id_no, status) => {
        fetch('../functions/update_user_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_no, status })
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
                        loadPendingUsers(); // Refresh the table
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
                console.error('Error updating user status:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong. Please try again later.'
                });
            });
    };
});

</script>




<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




<script>

      




document.addEventListener('DOMContentLoaded', function () {
    const eventsTable = document.querySelector('#eventsTable tbody');

    // Load Events
    // const loadEvents = () => {
    //     fetch('../functions/fetch_events.php')
    //         .then(response => response.json())
    //         .then(data => {
    //             if (data.status === 'success') {
    //                 eventsTable.innerHTML = ''; // Clear the table body
    //                 data.data.forEach(event => {
    //                     const row = `
    //                         <tr>
    //                             <td>${event.id}</td>
    //                             <td>${event.name}</td>
    //                             <td>${event.description}</td>
    //                             <td>${event.location}</td>
    //                             <td>${event.datetime}</td>
    //                             <td>
    //                                 <button class="btn btn-warning btn-sm edit-event" data-id="${event.id}"><i class="fas fa-edit"></i></button>
    //                                 <button class="btn btn-danger btn-sm delete-event" data-id="${event.id}"><i class="fas fa-trash"></i></button>
    //                             </td>
    //                         </tr>
    //                     `;
    //                     eventsTable.insertAdjacentHTML('beforeend', row);
    //                 });

    //                 // Add event listener for the edit button
    //                 document.querySelectorAll('.edit-event').forEach(button => {
    //                     button.addEventListener('click', function () {
    //                         const id = this.getAttribute('data-id');
    //                         fetchEventDetails(id); // Call fetchEventDetails for the selected event
    //                     });
    //                 });
    //             } else {
    //                 console.error('Error:', data.message);
    //             }
    //         })
    //         .catch(error => console.error('Error fetching events:', error));
    // };


    // Initialize DataTable instance
    let eventsTableInstance = $('#eventsTable').DataTable();

    // Load Events
    const loadEvents = () => {
        fetch('../functions/fetch_events.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Clear existing rows in DataTable
                    eventsTableInstance.clear();

                    // Add rows dynamically
                    data.data.forEach(event => {
                        eventsTableInstance.row.add([
                            event.id,
                            event.name,
                            event.description,
                            event.location,
                            event.datetime,
                            `
                                <button class="btn btn-warning btn-sm edit-event" data-id="${event.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm delete-event" data-id="${event.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `
                        ]);
                    });

                    // Redraw DataTable to reflect changes
                    eventsTableInstance.draw();

                    // Rebind event listeners for dynamically added buttons
                    document.querySelectorAll('.edit-event').forEach(button => {
                        button.addEventListener('click', function () {
                            const id = this.getAttribute('data-id');
                            fetchEventDetails(id); // Call fetchEventDetails for the selected event
                        });
                    });
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => console.error('Error fetching events:', error));
    };

    // Load events when the page is loaded
    loadEvents();

    

   
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

    // Delete Event
    eventsTable.addEventListener('click', function (e) {
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





</body>
</html>




