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
  <title>Welcome to Basketball Roster</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
  ?>

<div class="header">
  Welcome, <?php echo $_SESSION["username"]?>!
</div>

<?php
  echo "<div class=\"container\">";

  get_all_buttons();

  echo "</div>";
?>

</body>
</html>
