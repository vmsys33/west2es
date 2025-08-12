   <!-- Footer Section -->
  <footer class="bg-primary text-white text-center py-3 mt-auto">
    <div class="container">
      <p class="mb-0">© 2024 Cadiz West 2 Elementary School Portal. All rights reserved.</p>
      <!-- <p class="mb-0">Developed by Gweneth | Powered by Bootstrap</p> -->
    
    </div>
  </footer>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap JS -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js" defer></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js" defer ></script>
 
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.11/dist/sweetalert2.all.min.js"></script>






  
<script>
     function formatDateTime(datetime) {
    const date = new Date(datetime); // Convert string to a Date object

    // Format the date to "Month Day, Year Hour:Minute AM/PM"
    const options = { 
      month: 'long', 
      day: 'numeric', 
      year: 'numeric', 
    };
    
     return date.toLocaleDateString('en-US', options);
  }

</script>   



  <script>
    document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById('searchInput2'); // The input field for the search
  const clearButton = document.getElementById('clearButton'); // Clear button for search input
  const searchSuggestions = document.getElementById('searchSuggestions'); // Suggestions dropdown
  let selectedIndex = -1; // Keeps track of the selected suggestion

  // Show the "X" button when there's input
  searchInput.addEventListener("input", function() {
    const query = searchInput.value.trim();
    
    if (query.length > 0) {
      clearButton.style.display = "block";
      fetchSearchSuggestions(query);
    } else {
      clearButton.style.display = "none";
      searchSuggestions.style.display = "none";
    }
  });

  // Clear input when "X" button is clicked
  clearButton.addEventListener("click", function() {
    searchInput.value = '';
    clearButton.style.display = "none";
    searchSuggestions.style.display = "none";
    selectedIndex = -1; // Reset the selected index
  });

  // Fetch search suggestions from the PHP backend
  function fetchSearchSuggestions(query) {
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

          searchSuggestions.innerHTML = resultsHtml;
          searchSuggestions.style.display = "block"; // Show the suggestions
        } else if (data.status === 'no_results') {
          searchSuggestions.innerHTML = '<li class="list-group-item text-muted">No results found</li>';
          searchSuggestions.style.display = "block"; // Show the empty message
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
  }



  // Hide suggestions when clicked outside
  document.addEventListener("click", function(event) {
    if (!searchInput.contains(event.target) && !searchSuggestions.contains(event.target)) {
      searchSuggestions.style.display = "none";
    }
  });

  // When a suggestion is clicked, populate the input with the suggestion
  searchSuggestions.addEventListener("click", function(event) {
    if (event.target && event.target.matches(".list-group-item")) {
      searchInput.value = event.target.textContent;
      searchSuggestions.style.display = "none";
      clearButton.style.display = "block";  // Ensure the X button is shown
    }
  });

  // Keyboard navigation (down arrow, up arrow, enter)
  searchInput.addEventListener("keydown", function(e) {
    const items = searchSuggestions.getElementsByClassName("list-group-item");

    if (e.key === "ArrowDown") {
      // Navigate down the suggestions
      if (selectedIndex < items.length - 1) {
        selectedIndex++;
        highlightSuggestion(items[selectedIndex]);
      }
    } else if (e.key === "ArrowUp") {
      // Navigate up the suggestions
      if (selectedIndex > 0) {
        selectedIndex--;
        highlightSuggestion(items[selectedIndex]);
      }
    } else if (e.key === "Enter" && selectedIndex >= 0) {
      // Select the current suggestion on Enter
      searchInput.value = items[selectedIndex].textContent;
      searchSuggestions.style.display = "none";
      clearButton.style.display = "block";
      selectedIndex = -1; // Reset the selected index
    }
  });

  // Highlight the currently selected suggestion
  function highlightSuggestion(item) {
    const items = searchSuggestions.getElementsByClassName("list-group-item");
    // Reset previously selected items
    Array.from(items).forEach(item => item.classList.remove("active"));
    item.classList.add("active");
  }
});


  
    $(document).on('click', '.search-result', function (e) {
    e.preventDefault();

    const table = $(this).data('table');
    const id = $(this).data('id');

    fetch(`../functions/fetch_file_details.php?table=${table}&id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Network response was not ok (status: ${response.status})`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success' && data.data && data.data.file && data.data.revisions) {
                const file = data.data.file;
                const revisions = data.data.revisions;

                let revisionsHtml = '';

                 // Clear the search input and hide the search results
                $('#searchInput').val(''); // Clear the input field
                $('#searchResults').addClass('d-none').html(''); // Hide and clear the list


                if (revisions && revisions.length > 0) {
                    revisionsHtml += `
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 7%;">Version</th>
                                    <th style="width: 10%;">Datetime</th>
                                    <th style="width: 7%;">File Size</th>
                                    <th style="width: 15%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    

                    revisions.forEach(revision => {

                      // Extract file extension from filename
                        // const fileExtension = revision.filename.split('.').pop().toLowerCase();

                        // let viewerButton = ''; // Initialize the viewer button HTML
                        // if (fileExtension === 'pdf') {
                        //   viewerButton = `
                        //     <a href="../view_pdf.php?file_path=${revision.file_path}" class="btn btn-secondary btn-sm" target="_blank"> 
                        //         <i class="fas fa-eye"></i>   
                        //     </a>`;

                        // } else if (fileExtension === 'doc' || fileExtension === 'docx') {
                        //     // Word file viewer
                        //     viewerButton = `
                        //         <a href="../view_word.php?file_path=${revision.file_path}" class="btn btn-secondary btn-sm" target="_blank"> 
                        //             <i class="fas fa-eye"></i>   
                        //         </a>`;
                        // } else {
                        //     // Unsupported file type message
                        //     viewerButton = `
                        //         <button class="btn btn-danger btn-sm" disabled>
                        //             <i class="fas fa-eye-slash"></i> Unsupported
                        //         </button>`;
                        // }

                        // Extract file extension from filename
                    const fileExtension = revision.filename.split('.').pop().toLowerCase();

                    let viewerButton = ''; // Initialize the viewer button HTML

                    if (fileExtension === 'pdf') {
                        viewerButton = `
                            <a href="../view_pdf.php?file_path=${revision.file_path}" class="btn btn-secondary btn-sm" target="_blank"> 
                                <i class="fas fa-eye"></i>   
                            </a>`;
                    } else if (fileExtension === 'doc' || fileExtension === 'docx') {
                        // Word file viewer
                        viewerButton = `
                            <a href="../view_word.php?file_path=${revision.file_path}" class="btn btn-secondary btn-sm" target="_blank"> 
                                <i class="fas fa-eye"></i>   
                            </a>`;
                    } else if (fileExtension === 'xls' || fileExtension === 'xlsx') {
                        // Excel file viewer
                        viewerButton = `
                            <a href="../view_excel.php?file_path=${revision.file_path}" class="btn btn-secondary btn-sm" target="_blank"> 
                                <i class="fas fa-eye"></i>   
                            </a>`;
                    } else if (fileExtension === 'ppt' || fileExtension === 'pptx') {
                      // PowerPoint file viewer
                      viewerButton = `
                          <a href="../view_ppt.php?file_path=${revision.file_path}" class="btn btn-secondary btn-sm" target="_blank"> 
                              <i class="fas fa-eye"></i>   
                          </a>`;        
                    } else {
                        // Unsupported file type message
                        viewerButton = `
                            <button class="btn btn-danger btn-sm" disabled>
                                <i class="fas fa-eye-slash"></i> Unsupported
                            </button>`;
                    }

                        
                        revisionsHtml += `
                            <tr>
                                <td>${revision.version_no}</td>
                                <td>${formatDateTime(revision.datetime)}</td>
                                <td>${revision.file_size}</td>
                                 <td>
                                    <a href="#" class="btn btn-sm btn-primary download-file2" data-url="${revision.file_path}"><i class="fas fa-download"></i></a>
                                    ${viewerButton} <!-- Dynamically generated viewer button -->
                                </td>
                            </tr>
                        `;
                    });

                    revisionsHtml += `
                            </tbody>
                        </table>
                    `;
                } else {
                    revisionsHtml = "<p>No revisions found for this file.</p>";
                }


                    let SearchTable = table;

                    switch(SearchTable) {
                            case 'admin_files':
                             fileCategory = "Files/Administrative Files";
                                break;
                            case 'cild_files':
                                fileCategory = "Files/Curriculum Implementation and Learning Delivery";
                                break;

                            case 'lulr_files':
                                fileCategory = "Localization and Utilization of Learning Resources";
                                break;    
                            
                            case 'aeld_files':
                                fileCategory = "Assessment/Evaluation of Learner's Development ";
                                break;        
                            
                            case 'if_proposals_files':
                                fileCategory = "Innovation Files/Proposals";
                                break;            

                            case 'if_completed_completed':
                                fileCategory = "Innovation Files/Completed";
                                break;   
                                
                            case "rp_proposal_berf_files":
                                fileCategory = "Research Papers/Proposals/Berf";       
                                break;       

                            case "rp_proposal_nonberf_files":
                                fileCategory = "Research Papers/Proposals/Non Berf";  
                                break;           

                            case "rp_completed_berf_files":
                                fileCategory = "Research Papers/Proposals/Non Berf";  
                                break;
                                
                            case "rp_completed_nonberf_files":
                                fileCategory = "Research Papers/Proposals/Non Berf";  
                                break;    
                                
                                case "t_lr_files":
                                fileCategory = "Transparency/Liquidation Report";  
                                break;           

                            case "t_pp_files":
                                fileCategory = "Transparency/Project Proposal"; 
                                break;
                                
                            case "t_rs_files":
                                fileCategory = "Transparency/Realignment and Supplementals";       
                                break;           
                                  
                        
                    }

                    

                $('#fileDetailsModal .modal-body').html(`
                    <p><strong>Name:</strong> ${file.filename}</p>
                    <p><strong>File Folder:</strong> ${fileCategory}</p>
                    ${revisionsHtml}
                `);

                $('#fileDetailsModal').modal('show');
            } else {
                console.error('Unexpected response format:', data);
                alert('Error fetching file details: Unexpected response format.');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('An error occurred while fetching file details.');
        });
});    


  </script>




  
  <script>
    // Expose session variables to JavaScript
    sessionStorage.setItem('user_role', <?php echo json_encode($_SESSION['user_role'] ?? ''); ?>);
    sessionStorage.setItem('user_id', <?php echo json_encode($_SESSION['user_id'] ?? ''); ?>);
  </script>



<script>
$(document).on('click', '.download-file2', function () {
    const fileUrl = $(this).data('url'); // Retrieve the file URL
    const fileName = fileUrl.split('/').pop(); // Extract file name
    
    // Extract the relative path from uploads directory
    let relativePath = fileUrl;
    if (fileUrl.includes('uploads/')) {
        relativePath = fileUrl.split('uploads/')[1];
    } else if (fileUrl.includes('/west2es/uploads/')) {
        relativePath = fileUrl.split('/west2es/uploads/')[1];
    }

    Swal.fire({
        title: 'Download Confirmation',
        text: `Do you want to download ${fileName}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, download it!',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const downloadUrl = `../functions/file_functions/download_file.php?file=${encodeURIComponent(relativePath)}`;
            window.location.href = downloadUrl; // Trigger download
        }
    });
});
</script>



  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const sidebarMenus = document.querySelectorAll(".has-submenu");

      // Prevent multiple event listeners
      let isInitialized = false;
      if (isInitialized) return;
      isInitialized = true;

      // Collapse all menus and submenus by default
      function initializeMenus() {
        sidebarMenus.forEach((menu) => {
          const submenu = menu.querySelector(".submenu");
          const activeLink = menu.querySelector(".nav-link.active");

          // Collapse all menus unless an active link is present
          if (activeLink) {
            menu.classList.add("show");
            if (submenu) {
              submenu.classList.remove("collapse");
              submenu.style.display = "block";
            }
            const icon = menu.querySelector(".fa-chevron-right");
            if (icon) icon.style.transform = "rotate(90deg)";
          } else {
            menu.classList.remove("show");
            if (submenu) {
              submenu.classList.add("collapse");
              submenu.style.display = "none";
            }
            const icon = menu.querySelector(".fa-chevron-right");
            if (icon) icon.style.transform = "rotate(0deg)";
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
            if (submenu) {
              submenu.classList.remove("collapse");
              submenu.style.display = "block";
            }
            const icon = parentMenu.querySelector(".fa-chevron-right");
            if (icon) icon.style.transform = "rotate(90deg)";
            parentMenu = parentMenu.parentElement.closest(".has-submenu");
          }
        }
      }

      // Add toggle functionality for menus and submenus
      function addToggleFunctionality() {
        sidebarMenus.forEach((menu) => {
          const link = menu.querySelector("a.nav-link");
          const submenu = menu.querySelector(".submenu");

          // Remove existing event listeners
          const newLink = link.cloneNode(true);
          link.parentNode.replaceChild(newLink, link);

          newLink.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();

            const isOpen = menu.classList.contains("show");

            // Collapse other sibling menus
            const parentMenu = menu.parentElement;
            const siblingMenus = parentMenu.querySelectorAll(".has-submenu");
            siblingMenus.forEach((sibling) => {
              if (sibling !== menu) {
                sibling.classList.remove("show");
                const siblingSubmenu = sibling.querySelector(".submenu");
                if (siblingSubmenu) {
                  siblingSubmenu.classList.add("collapse");
                  siblingSubmenu.style.display = "none";
                }
                const siblingIcon = sibling.querySelector(".fa-chevron-right");
                if (siblingIcon) siblingIcon.style.transform = "rotate(0deg)";
              }
            });

            // Toggle the current menu with smooth transition
            if (!isOpen) {
              menu.classList.add("show");
              if (submenu) {
                submenu.classList.remove("collapse");
                submenu.style.display = "block";
              }
              const icon = newLink.querySelector(".fa-chevron-right");
              if (icon) icon.style.transform = "rotate(90deg)";
            } else {
              menu.classList.remove("show");
              if (submenu) {
                submenu.classList.add("collapse");
                submenu.style.display = "none";
              }
              const icon = newLink.querySelector(".fa-chevron-right");
              if (icon) icon.style.transform = "rotate(0deg)";
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

      if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", (e) => {
          e.preventDefault();
          sidebar.classList.toggle("show");
        });
      }
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

    // Only load pending users if the table exists (only on pending-users.php page)
    const pendingUsersTable = document.querySelector('#pendingUsersTable tbody');
    if (pendingUsersTable) {
        // Load the data when the page loads
        loadPendingUsers();

        // Event delegation for handling activate button click
        pendingUsersTable.addEventListener('click', function (e) {
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
    } // Close the if statement for pendingUsersTable
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






</body>
</html>




