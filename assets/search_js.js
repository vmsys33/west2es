document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById('searchInput2'); // The input field for the search
  const clearButton = document.getElementById('clearSearch'); // Clear button for search input
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
