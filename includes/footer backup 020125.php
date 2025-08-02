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

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.11/dist/sweetalert2.all.min.js"></script>


   <script>
    $(document).ready(function () {
      const endpoint = '../functions/search_suggestions.php';

      $('#searchInput2').on('input', function () {
        const query = $(this).val().trim();

        if (query.length > 0) {
          fetch(`${endpoint}?query=${encodeURIComponent(query)}`)
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

                $('#searchSuggestions').html(resultsHtml).removeClass('d-none');
              } else if (data.status === 'no_results') {
                $('#searchSuggestions').html('<li class="list-group-item text-muted">No results found</li>').removeClass('d-none');
              }
            })
            .catch(error => {
              console.error('Error fetching search suggestions:', error);
            });
        } else {
          $('#searchSuggestions').addClass('d-none').html('');
        }
      });

      $(document).on('click', function (event) {
        if (!$(event.target).closest('#searchInput, #searchSuggestions').length) {
          $('#searchSuggestions').addClass('d-none').html('');
        }
      });
    });
  </script>


  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput2');
    const clearSearch = document.getElementById('clearSearch');
    const searchSuggestions = document.getElementById('searchSuggestions');

    // Show or hide the "X" button based on input value
    searchInput.addEventListener('input', () => {
      if (searchInput.value.trim() !== '') {
        clearSearch.style.display = 'block'; // Show "X" button
      } else {
        clearSearch.style.display = 'none'; // Hide "X" button
        searchSuggestions.classList.add('d-none'); // Hide suggestions
      }
    });

    // Clear input and hide suggestions when "X" is clicked
    clearSearch.addEventListener('click', () => {
      searchInput.value = ''; // Clear the input field
      clearSearch.style.display = 'none'; // Hide "X" button
      searchSuggestions.classList.add('d-none'); // Hide suggestions
    });

    // Hide suggestions when clicking outside the search box
    document.addEventListener('click', (event) => {
      if (!searchInput.contains(event.target) && !searchSuggestions.contains(event.target)) {
        searchSuggestions.classList.add('d-none');
      }
    });
  });
</script>








<!--   
    <script>
    document.addEventListener("DOMContentLoaded", () => {
      const searchInput = document.getElementById("searchInput2");
      const searchSuggestions = document.getElementById("searchSuggestions");

      const dummyData = [
        "Annual Report 2025",
        "Student Attendance List",
        "School Events Calendar",
        "Parent-Teacher Meeting Notes",
        "Grade 5 Performance Review",
        "Library Book Inventory",
        "Teacher Schedules",
        "Monthly Expense Report",
        "Health and Safety Guidelines",
        "Holiday Schedules"
      ];

      searchInput.addEventListener("input", () => {
        const query = searchInput.value.toLowerCase();
        searchSuggestions.innerHTML = ""; // Clear previous suggestions

        if (query) {
          const filteredSuggestions = dummyData.filter(item =>
            item.toLowerCase().includes(query)
          );

          filteredSuggestions.forEach(suggestion => {
            const li = document.createElement("li");
            li.className = "list-group-item";
            li.textContent = suggestion;
            li.addEventListener("click", () => {
              searchInput.value = suggestion;
              searchSuggestions.innerHTML = ""; // Clear suggestions on selection
              searchSuggestions.classList.add("d-none");
            });
            searchSuggestions.appendChild(li);
          });

          searchSuggestions.classList.remove("d-none");
        } else {
          searchSuggestions.classList.add("d-none");
        }
      });

      document.addEventListener("click", (event) => {
        if (!searchInput.contains(event.target) && !searchSuggestions.contains(event.target)) {
          searchSuggestions.classList.add("d-none");
        }
      });
    });
  </script>
 
 -->



  




  
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


  



  
//   $(document).ready(function () {
//     // Retrieve user_role and user_id from the search-box element
//     const userRole = $('.search-box').data('role');
//     const userId = $('.search-box').data('user-id');

//     console.log('User Role:', userRole); // Debugging: Check the value in the console
//     console.log('User ID:', userId);     // Debugging: Check the value in the console

//     $('#searchInput').on('input', function () {
//         const query = $(this).val().trim();

//         if (query.length > 0) {
//             // Determine which endpoint to use based on user role
//             const endpoint = userRole === 'admin'
//                 ? `../functions/search_suggestions.php?query=${encodeURIComponent(query)}`
//                 : `../functions/search_suggestions_faculty.php?query=${encodeURIComponent(query)}&user_id=${encodeURIComponent(userId)}`;

//             fetch(endpoint)
//                 .then(response => response.json())
//                 .then(data => {
//                     if (data.status === 'success') {
//                         let resultsHtml = '';
//                         data.data.forEach(file => {
//                             resultsHtml += `
//                                 <li class="list-group-item">
//                                     <a href="#" class="search-result" data-table="${file.source_table}" data-id="${file.id}">
//                                         ${file.filename}
//                                     </a>
//                                 </li>
//                             `;
//                         });

//                         $('#searchResults').html(resultsHtml).removeClass('d-none'); // Show results
//                     } else if (data.status === 'no_results') {
//                         $('#searchResults').html('<li class="list-group-item text-muted">No results found</li>').removeClass('d-none');
//                     }
//                 })
//                 .catch(error => {
//                     console.error('Error fetching search suggestions:', error);
//                 });
//         } else {
//             $('#searchResults').addClass('d-none').html(''); // Hide results if query is empty
//         }
//     });
// });


$(document).ready(function () {
    // Set the default endpoint to search_suggestions.php
    const endpoint = '../functions/search_suggestions.php';

    $('#searchInput').on('input', function () {
        const query = $(this).val().trim();

        if (query.length > 0) {
            // Use the default endpoint for all searches
            fetch(`${endpoint}?query=${encodeURIComponent(query)}`)
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
                    console.error('Error fetching search suggestions:', error);
                });
        } else {
            $('#searchResults').addClass('d-none').html(''); // Hide results if query is empty
        }
    });
});



 
  $(document).ready(function () {
    $('#searchInput3').on('input', function () {
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


    // search-result begin 
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
                                         <th style="width: 7%;">Ver.</th> 
                                        <th style="width: 20%;">Date</th>
                                        <th style="width: 15%;">File Size</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                    
                          revisions.forEach(revision => {
                          // Extract file extension from the filename
                          const fileExtension = revision.filename.split('.').pop().toLowerCase();
                          
                          // Initialize viewer button HTML based on file type
                          let viewerButton = '';
                          if (fileExtension === 'pdf') {
                              viewerButton = `
                                  <a href="http://localhost/word_reader/pdf_viewer.php?file_id=${revision.file_id}&table_name=${revision.table_name}&version_no=${revision.version_no}" class="btn btn-secondary btn-sm" target="_blank"> 
                                    <i class="fas fa-eye"></i> 
                                </a>`;
                          } else if (fileExtension === 'doc' || fileExtension === 'docx') {
                              viewerButton = `
                                  <a href="http://localhost/word_reader/read_word.php?file_id=${revision.file_id}&table_name=${revision.table_name}&version_no=${revision.version_no}" class="btn btn-secondary btn-sm" target="_blank"> 
                                    <i class="fas fa-eye"></i> 
                                </a>`;
                          } else {
                              viewerButton = `
                                  <button class="btn btn-danger btn-sm" disabled>
                                      <i class="fas fa-eye-slash"></i> Unsupported
                                  </button>`;
                          }

                          // Append the viewer button to the table row
                          revisionsHtml += `
                              <tr>
                                  <td>${revision.version_no}</td>
                                  <td>${formatDateTime(revision.datetime)}</td>
                                  <td>${revision.file_size}</td>
                                  <td>
                                      <a href="${revision.download_path}" class="btn btn-sm btn-primary" download>
                                          <i class="fas fa-download"></i>
                                      </a>
                                      ${viewerButton}
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

    // search-result end    

});

  </script>

  

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

<!-- 

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
  document.addEventListener("DOMContentLoaded", () => {
  // Get the parent "Research Papers" menu and submenu
  const researchMenuLink = document.querySelector('.nav-item.has-submenu > a.nav-link');
  const researchMenuSubmenu = document.querySelector('#researchMenu');
  const researchMenuChevron = researchMenuLink.querySelector('.fa-chevron-right');

  // Function to toggle the visibility of the submenu
  const toggleResearchMenu = () => {
    if (researchMenuSubmenu.classList.contains('collapse')) {
      researchMenuSubmenu.classList.remove('collapse'); // Expand the submenu
      researchMenuChevron.style.transform = "rotate(90deg)"; // Rotate the chevron icon to point down
    } else {
      researchMenuSubmenu.classList.add('collapse'); // Collapse the submenu
      researchMenuChevron.style.transform = "rotate(0deg)"; // Reset the chevron icon to point right
    }
  };

  // Add click event listener to toggle Research Papers menu
  researchMenuLink.addEventListener('click', (e) => {
    e.preventDefault();
    toggleResearchMenu();
  });

  // Get all submenu links (BERF, Non-BERF links)
  const submenuLinks = document.querySelectorAll('#researchMenu .submenu a.nav-link');
  
  // For each link, check if it's active on click
  submenuLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      // Remove active class from all links
      submenuLinks.forEach(link => link.classList.remove('active'));
      
      // Add active class to clicked link
      link.classList.add('active');
    });
  });

  // Automatically highlight active submenu on page load
  const currentPage = window.location.pathname.split('/').pop(); // Get current page
  submenuLinks.forEach(link => {
    const linkHref = link.getAttribute('href').split('/').pop(); // Get href of the submenu link
    if (linkHref === currentPage) {
      link.classList.add('active'); // Add active class to the matching link
      
      // Expand the parent Research Papers menu if the link is active
      const parentMenu = link.closest('.has-submenu');
      if (parentMenu) {
        parentMenu.querySelector('.submenu').classList.remove('collapse'); // Expand the submenu
        parentMenu.querySelector('.fa-chevron-right').style.transform = "rotate(90deg)"; // Rotate the chevron to point down
      }
    }
  });

  // **Toggle visibility of submenus (Proposals, Completed) based on active state**
  const submenus = document.querySelectorAll('#researchMenu > .submenu-item > a.nav-link');
  
  submenus.forEach(submenu => {
    submenu.addEventListener('click', () => {
      // Find the submenu under the clicked item
      const submenuList = submenu.nextElementSibling;
      if (submenuList && submenuList.classList.contains('collapse')) {
        submenuList.classList.remove('collapse'); // Expand the submenu
        submenu.querySelector('.fa-chevron-right').style.transform = "rotate(90deg)"; // Rotate chevron to point down
      } else if (submenuList) {
        submenuList.classList.add('collapse'); // Collapse the submenu
        submenu.querySelector('.fa-chevron-right').style.transform = "rotate(0deg)"; // Reset chevron to point right
      }
    });
  });
});

</script>
   -->


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
    // const toggleUserStatus = (id_no, status) => {
    //     fetch('../functions/update_user_status.php', {
    //         method: 'POST',
    //         headers: { 'Content-Type': 'application/json' },
    //         body: JSON.stringify({ id_no, status })
    //     })
    //         .then(response => {
    //             if (!response.ok) {
    //                 throw new Error(`HTTP error! status: ${response.status}`);
    //             }
    //             return response.json();
    //         })
    //         .then(data => {
    //             if (data.status === 'success') {
    //                 Swal.fire({
    //                     icon: 'success',
    //                     title: 'Success',
    //                     text: data.message
    //                 }).then(() => {
    //                     loadPendingUsers(); // Refresh the table
    //                 });
    //             } else {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'Error',
    //                     text: data.message
    //                 });
    //             }
    //         })
    //         .catch(error => {
    //             console.error('Error updating user status:', error);
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: 'Error',
    //                 text: 'Something went wrong. Please try again later.'
    //             });
    //         });
    // };


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





<!-- search form close button -->
<script>
    $(document).ready(function() {
      const searchInput = $('#searchInput2');
      const clearButton = $('.clear-button');

      searchInput.on('input', function() {
        if (searchInput.val()) {
          clearButton.show();
        } else {
          clearButton.hide();
        }
      });

      clearButton.on('click', function() {
        searchInput.val('');
         // Clear the search input and hide the search results
       
       $('#searchResults').addClass('d-none').html(''); // Hide and clear the list

        clearButton.hide();
      });
    });
  </script>



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




