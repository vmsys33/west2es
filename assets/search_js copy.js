
  
  $(document).ready(function () {
    
    
    $('#searchInput').on('input', function () {
        const query = $(this).val().trim();

        if (query.length > 0) {
            // Determine which endpoint to use based on user role
            const endpoint = userRole === 'admin'
                ? `../functions/search_suggestions.php?query=${encodeURIComponent(query)}`
                : `../functions/search_suggestions_faculty.php?query=${encodeURIComponent(query)}&user_id=${encodeURIComponent(userId)}`;

            fetch(endpoint)
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
    $('#searchInput2').on('input', function () {
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
                                    <th>Version</th>
                                    <th>Datetime</th>
                                    <th>File Size</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    revisions.forEach(revision => {
                        
                        revisionsHtml += `
                            <tr>
                                <td>${revision.version_no}</td>
                                <td>${formatDateTime(revision.datetime)}</td>
                                <td>${revision.file_size}</td>
                                 <td>
                                    <a href="${revision.file_path}" class="btn btn-sm btn-primary" download-file2><i class="fas fa-download"></i></a>
                                    <a href="../pages/preview_file.php?file=${encodeURIComponent(revision.file_path)}" class="btn btn-secondary btn-sm" target="_blank">
                                    <i class="fas fa-eye"></i> 
                                    </a>
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

  



<!-- search form close button -->
<script>
    $(document).ready(function() {
      const searchInput = $('#searchInput');
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

  