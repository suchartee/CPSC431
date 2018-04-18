<?php
  session_start();
  if (empty($_SESSION)) {
    echo "<script>window.location = history.back();</script>"; // go back to the previous page
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Welcome to Basketball Roster Project</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <?php
    session_destroy();
  ?>
  <!-- Navigation menu on top -->
  <div class="navmenu">
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
  </div>

  <div class="header">
    Logged out
  </div>
  <div class="container">
    <h2>You are now successfully logged out of Basketball Roster Project.</h2>
    Didn't mean to logout? <a href="index.php">Login again</a>
  </div>


</body>
</html>