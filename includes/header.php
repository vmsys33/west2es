<?php
include '../functions/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle; ?></title>

  <link rel="icon" type="image/png" href="../assets/images/logo1.png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

 <!-- Add responsive DataTables CSS -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css"> -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.4/css/responsive.dataTables.css">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/styles.css">

<!-- Notification Handler Script -->
<script src="<?php echo $baseUrl; ?>assets/js/notification-handler.js"></script>

<style>
    /* Completely disable browser default validation feedback */
    input:valid, input:invalid {
        box-shadow: none; /* Removes browser's checkmark and invalid icons */
        border-color: initial; /* Reset border styles */
    }

    /* Styling for valid and invalid states */
    .is-valid {
        border-color: green;
    }

    .is-invalid {
        border-color: red;
    }

    .bell-icon {
    color: #6c757d; /* Muted color for the icon */
    font-size: 1.5rem; /* Adjust size */
    display: flex;
    align-items: center; /* Vertically align icon */
    cursor: pointer; /* Optional: make it interactive */
    transition: color 0.3s ease;
}

.bell-icon:hover {
    color: #007bff; /* Change color on hover */
}

#loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: #ffffff; /* Background color for the loader */
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999; /* Ensure it appears above all other elements */
}

#loader img {
    max-width: 150px; /* Adjust the size of your logo */
    animation: fadeinout 2s ease-in-out infinite;
}

/* Optional animation for the logo */
@keyframes fadeinout {
    0%, 100% {
        opacity: 0.5;
    }
    50% {
        opacity: 1;
    }
}

a.search-result {
    text-decoration: none !important;
    color:black;
}



</style>
<!-- <link rel="stylesheet" href="../assets/styles.css"> -->
 
</head>