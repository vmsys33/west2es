<?php
$user_role = $_SESSION['user_role']; // Default to 'guest' if not set

// Get user photo if user is logged in
$userPhoto = null;
if (isset($_SESSION['id_no'])) {
    require_once '../functions/upload_user_photo.php';
    $userPhoto = getUserPhoto($_SESSION['id_no']);
}
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
        <div class="d-flex align-items-center me-3">
          <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              <?php if ($userPhoto): ?>
                <img src="<?= htmlspecialchars($userPhoto) ?>" alt="User Photo" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #fff;">
              <?php else: ?>
                <i class="fas fa-user-circle me-2" style="font-size: 40px; color: #fff;"></i>
              <?php endif; ?>
              <span class="text-white">
                <?php 
                    echo isset($_SESSION['first_name']) ? "Welcome, " . htmlspecialchars($_SESSION['first_name']) . "!" : "Welcome, Guest!"; 
                ?>
              </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="pages/profile_photo.php"><i class="fas fa-camera me-2"></i>Manage Photo</a></li>
              <li><a class="dropdown-item" href="pages/profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="search-box">
        <span class="search-icon"><i class="fas fa-search"></i></span> <!-- Magnifying Glass Icon -->
        <input type="text" class="form-control" id="searchInput2" placeholder="Search" autocomplete="off">
        <button class="btn btn-light ms-1" id="clearButton" style="display: none;">
          <i class="fas fa-times"></i>
        </button>
        <div id="searchSuggestions" class="list-group" style="display: none; position: absolute; z-index: 999; background-color: white; width: 100%; "></div>
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




