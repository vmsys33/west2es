<div class="col-md-9 main-content">
        <div class="row g-3">
          <div class="col-md-4">
            <div class="info-box">
              <i class="fas fa-chart-line"></i>
              <span class="info-label">TOTAL</span>
              <span class="info-value">101</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-box">
              <i class="fas fa-calendar-alt"></i>
              <span class="info-label">EVENT</span>
              <span class="info-value">10</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-box">
              <i class="fas fa-user"></i>
              <span class="info-label">PENDING<br>USERS</span>
              <span class="info-value">25</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-box">
              <i class="fas fa-file-alt"></i>
              <span class="info-label">PENDING<br>FILES</span>
              <span class="info-value">10</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-box">
              <i class="fas fa-folder"></i>
              <span class="info-label">TOTAL<br>FILES</span>
              <span class="info-value">30</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-box">
              <i class="fas fa-bell"></i>
              <span class="info-label">NOTIFICATIONS</span>
              <span class="info-value">1</span>
            </div>
          </div>
          <div class="col-md-4 offset-md-2">
            <div class="info-box">
              <i class="fas fa-users"></i>
              <span class="info-label">TOTAL<br>USERS</span>
              <span class="info-value">25</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="searchModalLabel">Search</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Type to search...">
              <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // JavaScript to toggle the sidebar on mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');

    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });

    // Add this JavaScript to handle the submenu toggle
    const submenuItems = document.querySelectorAll('.has-submenu');

    submenuItems.forEach(item => {
      item.querySelector('a').addEventListener('click', (event) => {
        // Only prevent default for parent menu items (those with submenus)
        // Allow actual navigation for submenu items
        const link = event.target.closest('a');
        const hasSubmenu = link.parentElement.classList.contains('has-submenu');
        const isSubmenuItem = link.parentElement.classList.contains('submenu-item');
        
        if (hasSubmenu && !isSubmenuItem) {
          // This is a parent menu item with submenu - toggle it
          event.preventDefault();
          item.classList.toggle('show');
        }
        // For submenu items, let the default navigation happen
      });
    });
  </script>
</body>
</html>