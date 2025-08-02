<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadiz West 2 Elementary School</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <style>
    /* Custom styles */
    * { 
      margin: 0;
      padding: 0;
      box-sizing: border-box; 
    }

    .header {
      text-align: center; 
    } 

    .banner {
      position: relative; 
      overflow-x: hidden; /* Hide horizontal overflow to prevent white space */
    }

    .banner img {
      width: 100vw; /* Make the image slightly wider to cover the viewport */
      margin-left: 50%;
      transform: translateX(-50%);
    }

    .banner-content { 
      color: white; 
      position: absolute; 
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 100%; 
    } 

    #loginBtn { 
      background-color: rgba(0, 123, 255, 0.8); 
      border: none;
      padding: 10px 20px;
      font-size: 18px;
      border-radius: 5px; 
      cursor: pointer; 
      position: absolute;  
      left: 50%;
      transform: translateX(-50%);
    }

    /* Media query for desktop */
    @media (min-width: 768px) { 
      #loginBtn {
        top: 100px;  
      }

      .banner-content {
        top: 45%;  
      }
    }

    /* Media query for mobile */
    @media (max-width: 767px) { 
      #loginBtn {
        top: 80px;  
      }

      .banner-content {
        top: 40%;  
      }
    }

    /* Modal Styles */
    .modal-content {
      background-color: white;
      border-radius: 10px; 
      padding: 20px;
    }

    .modal-header {
      border-bottom: none; 
    }

    .modal-title {
      color: black; 
      text-align: center; 
      margin-bottom: 20px;
    }

    .modal-body .btn {
      display: block; 
      width: 100%;
      margin-bottom: 10px;
      background-color: white; 
      color: black; 
      border: none;
      padding: 15px;
      font-size: 16px;
      border-radius: 5px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
      transition: transform 0.2s; 
    }

    .modal-body .btn:hover {
      transform: translateY(-2px); 
    }

    /* Form Styles */
    .form-label {
      color: black;
      font-weight: bold;
    }

    .form-control {
      border: none;
      border-radius: 5px;
      padding: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Registration Form Specific Styles */
    #registerModal .modal-body {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    #registerModal .form-control {
      margin-bottom: 15px;
    }

    #registerModal .col-md-6 {
      margin-bottom: 15px;
    }
  </style>
</head>

<body>

  <div class="header banner">
    <img src="west2.png" alt="School Image"> 
    <div class="banner-content"> 
      <button id="loginBtn" class="btn btn-success">Login</button>
    </div>
  </div>

  <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="myModalLabel">Welcome to West 2 Elementary School Portal</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adminLoginModal">Admin Login</button>
          <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#facultyLoginModal">Faculty Login</button>
          <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="adminLoginModal" tabindex="-1" aria-labelledby="adminLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="adminLoginModalLabel">Admin Login</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3">
              <label for="idNo" class="form-label">ID No.</label>
              <input type="text" class="form-control" id="idNo">
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password">
            </div>
            <button type="submit" class="btn">Log In</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="facultyLoginModal" tabindex="-1" aria-labelledby="facultyLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="facultyLoginModalLabel">Faculty Login</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3">
              <label for="facultyId" class="form-label">Faculty ID</label>
              <input type="text" class="form-control" id="facultyId">
            </div>
            <div class="mb-3">
              <label for="facultyEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="facultyEmail">
            </div>
            <div class="mb-3">
              <label for="facultyPassword" class="form-label">Password</label>
              <input type="password" class="form-control" id="facultyPassword">
            </div>
            <button type="submit" class="btn">Log In</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="registerModalLabel">Register Account</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="row">
              <div class="col-md-6">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName">
              </div>
              <div class="col-md-6">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstName">
              </div>
            </div>
            <div class="mb-3">
              <label for="registerIdNo" class="form-label">ID No.</label>
              <input type="text" class="form-control" id="registerIdNo">
            </div>
            <div class="mb-3">
              <label for="registerEmail" class="form-label">DepEd Email Account</label>
              <input type="email" class="form-control" id="registerEmail">
            </div>
            <div class="mb-3">
              <label for="registerPassword" class="form-label">Password</label>
              <input type="password" class="form-control" id="registerPassword">
            </div>
            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Confirm Password</label>
              <input type="password" class="form-control" id="confirmPassword">
            </div>
            <button type="submit" class="btn">Create</button>
          </form>
        </div>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
    crossorigin="anonymous"></script>
  <script>
    // Get the modals
    var myModal = new bootstrap.Modal(document.getElementById('myModal'));
    var adminLoginModal = new bootstrap.Modal(document.getElementById('adminLoginModal'));
    var facultyLoginModal = new bootstrap.Modal(document.getElementById('facultyLoginModal'));
    var registerModal = new bootstrap.Modal(document.getElementById('registerModal'));

    // Get the button that opens the main modal
    var btn = document.getElementById("loginBtn");

    // When the user clicks the button, open the main modal 
    btn.onclick = function () {
      myModal.show();
    }
  </script>

</body>
</html>