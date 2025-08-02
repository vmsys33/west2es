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
  <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

  <!-- Add DataTables Responsive JavaScript -->
<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.11/dist/sweetalert2.all.min.js"></script>
  

  <script>
    // Sidebar toggle for mobile view
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebar = document.querySelector(".sidebar");

  sidebarToggle.addEventListener("click", () => {
    sidebar.classList.toggle("show");
  });
  </script>   

  
  <script>
    $(document).ready(function () {
    const userId = sessionStorage.getItem('user_id');
    const userRole = sessionStorage.getItem('user_role');
   
});

  </script>    
  
  
  <script>
    // Expose session variables to JavaScript
    sessionStorage.setItem('user_role', <?php echo json_encode($_SESSION['user_role'] ?? ''); ?>);
    sessionStorage.setItem('user_id', <?php echo json_encode($_SESSION['user_id'] ?? ''); ?>);
  </script>


  <script>
    function formatDateTime(datetime) {
    const date = new Date(datetime); // Convert string to a Date object

    // Format the date to "Month Day, Year Hour:Minute AM/PM"
    const options = { 
      month: 'long', 
      day: 'numeric', 
      year: 'numeric', 
      // hour: 'numeric', 
      // minute: 'numeric', 
      // hour12: true // Use 12-hour clock
    };
    
    return date.toLocaleString('en-US', options);
  }



<script>
$(document).on('click', '.download-file2', function () {
    const fileUrl = $(this).data('url'); // Retrieve the file URL
    const fileName = fileUrl.split('/').pop(); // Extract file name

    Swal.fire({
        title: 'Download Confirmation',
        text: `Do you want to download ${fileName}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, download it!',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const downloadUrl = `../functions/download_file.php?file=${encodeURIComponent(fileUrl)}`;
            window.location.href = downloadUrl; // Trigger download
        }
    });
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

        // Collapse other sibling menus only if not part of the active path
        const parentMenu = menu.parentElement; // Find the parent menu
        const siblingMenus = parentMenu.querySelectorAll(".has-submenu");
        siblingMenus.forEach((sibling) => {
          if (sibling !== menu && !sibling.querySelector(".nav-link.active")) {
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

  // Automatically highlight active submenu on page load
  const currentPage = window.location.pathname.split('/').pop(); // Get current page
  const submenuLinks = document.querySelectorAll('.submenu a.nav-link');
  submenuLinks.forEach(link => {
    const linkHref = link.getAttribute('href').split('/').pop(); // Get href of the submenu link
    if (linkHref === currentPage) {
      link.classList.add('active'); // Add active class to the matching link
      
      // Expand the parent menus
      let parentMenu = link.closest('.has-submenu');
      while (parentMenu) {
        parentMenu.classList.add('show');
        const submenu = parentMenu.querySelector('.submenu');
        if (submenu) submenu.classList.remove('collapse'); // Show submenu
        const icon = parentMenu.querySelector('.fa-chevron-right');
        if (icon) icon.style.transform = 'rotate(90deg)'; // Rotate the chevron down
        parentMenu = parentMenu.parentElement.closest('.has-submenu');
      }
    }
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
        const toggleUserStatus = (id_no) => {
            // Show confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to activate this user?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, activate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with activation
                    fetch('../functions/update_user_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id_no, status: 'active' }) // Always activate
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
                } else {
                    // Optional: Show a message if the user cancels
                    Swal.fire({
                        icon: 'info',
                        title: 'Cancelled',
                        text: 'The action has been cancelled.'
                    });
                }
            });
        };


        

});

</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Hide the loader after the page has fully loaded
        const loader = document.getElementById("loader");
        loader.style.transition = "opacity 0.5s ease";
        loader.style.opacity = "0";
        setTimeout(() => {
            loader.style.display = "none";
        }, 500); // Matches the transition duration
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("click", function (event) {
        // Check if the clicked element is a .download-file button or inside it
        let button = event.target.closest(".download-file");
        if (!button) return;

        

        // Remove return if you want the actual functionality to execute
        // return;

        // Show loading spinner while downloading
        let loader = button.querySelector(".loader");
        if (loader) {
            loader.style.display = "inline-block";
        }

        // Get the file path from the button's data-url attribute
        let fileUrl = button.getAttribute("data-url");
        if (!fileUrl) {
            alert("File URL not found!");
            return;
        }

        // Redirect to download.php with the file path
        window.location.href = fileUrl;

        // Hide the loader after initiating the download (optional)
        setTimeout(function () {
            if (loader) {
                loader.style.display = "none";
            }
        }, 2000);
    });
});

</script>




</body>
</html>




