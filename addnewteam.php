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
  <title>Add New Team Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    ?>

  <div class="header">
    Add New Team
  </div>
  <div class="container">
  <div class="box">
  <form action="addnewteam.php" method="post">
  <input type="text" name="teamname" placeholder="Team Name" class="textbox" required/><br/>
  <input type="submit" class="btn_reg" value="Add New Team" name="submit"/>
  </form>
  </div>
  </div>


  <?php
  if (isset($_POST["submit"])) {
    // check SQL injection
    if (isset($_POST["teamname"]) && !empty($_POST["teamname"])) {
      $teamname = ucwords(strip_tags(htmlspecialchars($_POST["teamname"])));

      $db = configDB($_SESSION["role"]);
      $query = "INSERT INTO Team(TeamName) VALUES (?)";
      if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("s", $teamname);
        $stmt->execute();
        echo '<script type="text/javascript"> alert("New Team is successfully added into the roster!")</script>';
      } else {
        echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
      }

    }
  }
  ?>

</body>
</html>
