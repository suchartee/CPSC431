<?php

// Connecting database based on the role
function configDB($role) {
  switch($role) {
    case 1:
      $usernameSQL = "observer";
      $passwordSQL = "observerPassword";
      break;
    case 2:
      $usernameSQL = "operator";
      $passwordSQL = "operatorPassword";
      break;
    case 3:
      $usernameSQL = "manager";
      $passwordSQL = "managerPassword";
      break;
    case 4:
      $usernameSQL = "admin";
      $passwordSQL = "adminPassword";
      break;
    default:
      $usernameSQL = "accountauth";
      $passwordSQL = "accountauthPassword";
      break;
  }
  $host = "localhost";
  $database = "db_project";

  $db = new mysqli($host, $usernameSQL, $passwordSQL, $database);
  if (mysqli_connect_errno()){
    echo '<p>Error: Could not connect to database.<br/>
    Please try again later.</p>';
    die();
  }
  return $db;
}







 ?>
