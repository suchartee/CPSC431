<?php
  session_start();
  if (empty($_SESSION["role"]) && (empty($_SESSION["authenticated"]))) {
    echo "<script type=\"text/javascript\"> alert(\"You must log in to the system first\")</script>";
    echo "<script>window.location = 'index.php';</script>"; // redirect to index.php (login page)
  }

  require_once "config.php";
  require_once "functions.php";
?>

<!DOCTYPE html>
<html>
<head>
  <title>Player Page</title>
  <link rel="stylesheet" href="style.css">
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script type="text/javascript">
      $(function () {
          $("#search").change(function () {
              if ($(this).val() == "searchByFirstName" || $(this).val() == "searchByLastName" || $(this).val() == "searchByTeam") {
                  $("#criteria").show();
                  $("#searchButton").show();
              } else {
                  $("#criteria").hide();
              }
          });
      });
  </script>
</head>

<body>
  <?php
    include 'logged_navbar.php';
  ?>

<div class="header">
  <select>
    <option value="view">View All Player</option>
    <option value="searchByFirstName">Search By First Name</option>
    <option value="searchByLastName">Search By Last Name</option>
    <option value="searchByTeam">Search By Team Name</option>
  </select>
  <input type="text" id="criteria" style="display: none;" />
  <input type="button" id="searchButton" style="display: none;"/>
</div>

</body>
</html>
