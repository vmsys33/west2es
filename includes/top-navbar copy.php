<?php
$user_role = $_SESSION['user_role']; // Default to 'guest' if not set
?>
<body data-user-role="<?= htmlspecialchars($user_role);
?>">

<div id="loader">
    <img src="../assets/images/logo1.png" alt="Website Logo" />
</div>

  <!-- container-fluid begin -->
  <div class="container-fluid p-0">
    <div class="bg-primary text-white py-2 d-flex align-items-center justify-content-center">
      <img src="../assets/images/logo1.png" alt="School Logo" class="me-2 rounded-circle" style="width: 40px; height: 40px;">
      <h5 class="m-0 blue-header-text">Cadiz West 2 Elementary School</h5>
    </div>

    <div class="bg-warning text-dark d-flex justify-content-between align-items-center py-2 px-3">
      <div class="d-flex align-items-center">
        <span class="me-2"><i class="fas fa-user-circle"></i>
        <?php 
            echo isset($_SESSION['first_name']) ? "Welcome, " . htmlspecialchars($_SESSION['first_name']) . "!" : "Welcome, Guest!"; 
        ?>
        </span>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <div class="d-md-flex search-box">
        <input type="text" class="form-control" id="searchInput2" placeholder="Document Search">
        <button class="btn btn-light ms-1">
            <i class="fas fa-search"></i>
        </button>
        <!-- Clear button -->
      <span id="clearSearch" class="position-absolute end-0 top-50 translate-middle-y me-5" style="cursor: pointer; display: none;">
          <i class="fas fa-times"></i>
      </span>
        <ul id="searchSuggestions" class="list-group d-none"></ul>
        </div>

            
        </div>

        <button class="btn btn-primary" id="sidebarToggle">
          <i class="fas fa-bars"></i>
        </button>
    




 </div>




<!-- File Details Modal -->
<div class="modal fade" id="fileDetailsModal" tabindex="-1" aria-labelledby="fileDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileDetailsModalLabel">File Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- File details will be loaded here dynamically -->
            </div>
        </div>
    </div>
</div>

<div class="row">




