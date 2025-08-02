<div class="col-md-3 bg-primary text-white sidebar">
  <div class="py-3">
    <ul class="nav flex-column">
      <!-- Dashboard menu -->
      <li class="nav-item py-2 has-submenu show">
        <a href="javascript:void(0)" class="nav-link text-white">
          <i class="fas fa-home me-2"></i>Dashboard
          <i class="fas fa-chevron-right float-end"></i>
        </a>
        <ul class="submenu">
          <li class="submenu-item py-2">
            <a href="dashboard-overview.php" class="nav-link text-white <?php echo $currentPage == 'dashboard-overview.php' ? 'active' : ''; ?>">
              <i class="fas fa-file-alt me-2"></i>Overview
            </a>
          </li>

          <?php
          if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
              // Pending Users Count
              require_once '../functions/db_connection.php';
              $stmt = $pdo->prepare("SELECT COUNT(*) AS pending_count FROM user_data WHERE status = 'pending'");
              $stmt->execute();
              $pendingUsersCount = $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];

              echo '<li class="submenu-item py-2">
                      <a href="pending-users.php" class="nav-link text-white ' . ($currentPage == 'pending-users.php' ? 'active' : '') . '">
                          <i class="fas fa-users me-2"></i>Pending Users';
              if ($pendingUsersCount > 0) {
                  echo '<span class="badge bg-danger ms-2">' . $pendingUsersCount . '</span>';
              }
              echo '</a></li>';
            }
          ?>

          <li class="submenu-item py-2">
    <a href="pending-files.php" class="nav-link text-white">
        <i class="fas fa-file-alt me-2"></i>Pending Files
        <?php
        // Fetch pending files count
        $stmt = $pdo->query("SELECT COUNT(*) AS pending_count FROM pending_files WHERE status = 'pending'");
        $pendingFilesCount = $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];

        if ($pendingFilesCount > 0) {
            echo '<span id="pendingFilesBadge" class="badge bg-danger ms-2">' . $pendingFilesCount . '</span>';
        }
        ?>
    </a>
</li>
    





          <!-- Notification Menu -->
          <li class="submenu-item py-2">
            <a href="notification.php" class="nav-link text-white <?php echo $currentPage == 'notification.php' ? 'active' : ''; ?>">
              <i class="fas fa-file-alt me-2"></i>Notifications
              <?php

              require_once '../functions/db_connection.php';// Fetch unseen notifications count
              $stmt = $pdo->prepare("SELECT COUNT(*) AS unseen_count FROM notifications WHERE seen = 0");
              $stmt->execute();
              $notificationCount = $stmt->fetch(PDO::FETCH_ASSOC)['unseen_count'];

              // Display badge only if there are unseen notifications
              if ($notificationCount > 0) {
                  echo '<span class="badge bg-danger ms-2">' . $notificationCount . '</span>';
              }
              ?>
            </a>
          </li>
            </ul>

          <li class="nav-item py-2">
            <a href="event.php" class="nav-link text-white <?php echo $currentPage == 'event.php' ? 'active' : ''; ?>">
              <i class="fas fa-calendar-alt me-2"></i>Event
            </a>
          </li>

          <!-- File menu -->
          <li class="nav-item py-2 has-submenu show">
            <a href="javascript:void(0)" class="nav-link text-white">
              <i class="fas fa-folder-open me-2"></i>File
              <i class="fas fa-chevron-right float-end"></i>
            </a>
            <ul class="submenu collapse" id="fileMenu">
              <li class="submenu-item py-2">

                <a href="content.php?current_page=admin_files" class="nav-link text-white <?php echo $currentPage == 'admin_files' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>Administrative Files
                </a>

              </li>
              <li class="submenu-item py-2">
                <a href="content.php?current_page=cild_files" class="nav-link text-white <?php echo $currentPage == 'cild_files' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>Curriculum Implementation and Learning Delivery
                </a>
              </li>
              <li class="submenu-item py-2">
                    <a href="content.php?current_page=lulr_files" class="nav-link text-white <?php echo $currentPage == 'lulr_files' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>Localization and Utilization of Learning Resources
                </a>
              </li>
              <li class="submenu-item py-2">
                <a href="content.php?current_page=aeld_files" class="nav-link text-white <?php echo $currentPage == 'aeld_files' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>Assessment/Evaluation of Learner's Development
                </a>
              </li>
            </ul>
          </li>

          <!-- Innovation Files -->
          <li class="nav-item py-2 has-submenu show">
            <a href="javascript:void(0)" class="nav-link text-white">
              <i class="fas fa-lightbulb me-2"></i>Innovation Files
              <i class="fas fa-chevron-right float-end"></i>
            </a>
            <ul class="submenu collapse" id="innovationMenu">
              <li class="submenu-item py-2">

                <a href="content.php?current_page=if_proposals_files" class="nav-link text-white <?php echo $currentPage == 'if_proposals_files' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>Proposals
                </a>

              </li>
              <li class="submenu-item py-2">

              <a href="content.php?current_page=if_completed_files" class="nav-link text-white <?php echo $currentPage == 'if_completed_files' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>Completed
                </a>
              </li>
            </ul>
          </li>
  <li class="nav-item py-2 has-submenu">
  <a href="javascript:void(0)" class="nav-link text-white">
    <i class="fas fa-book me-2"></i>Research Papers
    <i class="fas fa-chevron-right float-end"></i>
  </a>
  <ul class="submenu collapse" id="researchMenu">
    <li class="submenu-item py-2 has-submenu">
      <a href="javascript:void(0)" class="nav-link text-white">
        <i class="fas fa-file-alt me-2"></i>Proposals
        <i class="fas fa-chevron-right float-end"></i>
      </a>
      <ul class="submenu collapse">
        <li class="submenu-item py-2">
          <a href="content.php?current_page=rp_proposal_berf_files" class="nav-link text-white <?php echo $currentPage == 'rp_proposal_berf_files' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>BERF
                </a>

        </li>
        <li class="submenu-item py-2">
          <a href="content.php?current_page=rp_proposal_nonberf_files" class="nav-link text-white <?php echo $currentPage == 'rp_proposal_nonberf_files' ? 'active' : ''; ?>">
            <i class="fas fa-file-alt me-2"></i>Non-BERF
        </a>
        </li>
      </ul>
    </li>
    <li class="submenu-item py-2 has-submenu">
      <a href="javascript:void(0)" class="nav-link text-white">
        <i class="fas fa-file-alt me-2"></i>Completed
        <i class="fas fa-chevron-right float-end"></i>
      </a>
      <ul class="submenu collapse">
        <li class="submenu-item py-2">
          <a href="content.php?current_page=rp_completed_berf_files" class="nav-link text-white <?php echo $currentPage == 'rp_completed_berf_files' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>BERF
                </a>
        </li>
        <li class="submenu-item py-2">
          <a href="content.php?current_page=rp_completed_nonberf_files" class="nav-link text-white <?php echo $currentPage == 'rp_completed_nonberf_files' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>Non-BERF
                </a>

        </li>
      </ul>
    </li>
  </ul>
</li>


          <!-- End Research Papers -->

          <!-- Transparency -->
          <li class="nav-item py-2 has-submenu show">
            <a href="javascript:void(0)" class="nav-link text-white">
              <i class="fas fa-chart-bar me-2"></i>Transparency
              <i class="fas fa-chevron-right float-end"></i>
            </a>
            <ul class="submenu collapse" id="transparencyMenu">
              <li class="submenu-item py-2">

              <a href="content.php?current_page=t_lr_files" class="nav-link text-white <?php echo $currentPage == 't_lr_files' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>Liquidation Reports
                </a>
              </li>
              <li class="submenu-item py-2">

              <a href="content.php?current_page=t_pp_files" class="nav-link text-white <?php echo $currentPage == 't_pp_files' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>Project Proposals
                </a>
              </li>
              <li class="submenu-item py-2">
              <a href="content.php?current_page=t_rs_files" class="nav-link text-white <?php echo $currentPage == 't_rs_files' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>Realignment and Supplementals
                </a>
              </li>
            </ul>
          </li>
          <!-- End Transparency -->

          <?php
          if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
          ?>
            <li class="nav-item py-2">
              <a href="teachers_profile.php" class="nav-link text-white <?php echo $currentPage == 'teachers_profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-tie me-2"></i>Teacher's Profile
              </a>
            </li>
          
          <li class="nav-item py-2">
            <a href="reports.php" class="nav-link text-white <?php echo $currentPage == 'reports.php' ? 'active' : ''; ?>">
              <i class="fas fa-user-tie me-2"></i>Report Management
            </a>
          </li>

          <?php
          }
          ?>


          <?php
          if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'faculty') {
          ?>
            <li class="nav-item py-2">
              <a href="profile.php" class="nav-link text-white <?php echo $currentPage == 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-tie me-2"></i>My Profile
              </a>
            </li>
          <?php
          }
          ?>

          <li class="nav-item py-2">
            <a href="mission_vision.php" class="nav-link text-white <?php echo $currentPage == 'mission_vision.php' ? 'active' : ''; ?>">
              <i class="fas fa-bullseye me-2"></i>Mission & Vision
            </a>
          </li>

          <?php
          if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
          ?>
            <li class="nav-item py-2">
              <a href="settings.php" class="nav-link text-white <?php echo $currentPage == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cogs me-2"></i>Settings
              </a>
            </li>
          <?php } ?>

          <li class="nav-item py-2">
            <a href="../pages/logout.php" id="logoutButton" class="nav-link text-white">
              <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
          </li>
        </ul>
      </div>
    </div>
