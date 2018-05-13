<?php
  session_start();
  if (empty($_SESSION)) {
    echo "<script>window.location = history.back();</script>"; // go back to the previous page
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Welcome to Basketball Roster</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <?php
    session_destroy();

    include_once "default_navbar.php";
  ?>

  <div class="header">
    Logged out
  </div>
  <div class="container">
    <h2>You are now successfully logged out of Basketball Roster.</h2>
    Didn't mean to logout? <a href="index.php">Login again</a>
  </div>


</body>
</html>
