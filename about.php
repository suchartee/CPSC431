<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Welcome to Basketball Roster Project</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
  if(isset($_SESSION["role"])) {
    include_once "logged_navbar.php";
  } else {
    include_once "default_navbar.php";
  }
   ?>

  <div id="about-page">
    <h1>- About Us -</h1>
    <br/>
    <h2>Team Members:</h2>
      1. Don Vu
      <br/>
      2. Suchartee Kitisopakul
      <br/>
      3. Su Win Htet
  </div>

</body>
</html>
