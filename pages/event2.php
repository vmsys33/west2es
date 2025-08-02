<?php include '../includes/header.php'; ?>
<?php include '../includes/top-navbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>





<div class="col-md-9 main-content">
        <h3 class="mb-3">Event</h3>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addEventModal">Add Event</button>

        <h4 class="mb-3">List of Events</h4>
        <div class="table-responsive">
          <table id="eventsTable" class="table table-bordered">
            <thead>
              <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Location</th>
                <th>Date & Time</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>School Foundation Day</td>
                <td>Celebration of school founding anniversary</td>
                <td>Auditorium</td>
                <td>2024-12-28 08:00 AM</td>
                <td>
                  <button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                  <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
              <!-- Add more rows here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

   <!-- Add Event Modal -->
  <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addEventModalLabel">Add Event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3">
              <label for="eventName" class="form-label">Name</label>
              <input type="text" class="form-control" id="eventName" placeholder="Enter event name">
            </div>
            <div class="mb-3">
              <label for="eventDescription" class="form-label">Description</label>
              <textarea class="form-control" id="eventDescription" rows="3" placeholder="Enter event description"></textarea>
            </div>
            <div class="mb-3">
              <label for="eventLocation" class="form-label">Location</label>
              <input type="text" class="form-control" id="eventLocation" placeholder="Enter event location">
            </div>
            <div class="mb-3">
              <label for="eventDateTime" class="form-label">Date & Time</label>
              <input type="datetime-local" class="form-control" id="eventDateTime">
            </div>
            <button type="button" class="btn btn-primary">Save Event</button>
          </form>
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



  <?php include '../includes/footer.php'; ?>