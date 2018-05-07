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
  <title>Add New Player Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $db = configDB($_SESSION["role"]);
    $query = "SELECT ID, TeamName FROM Team";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($teamID, $teamname);
    ?>

  <div class="header">
    Add New Player
  </div>
  <div class="container">
  <div class="box">
  <form action="addnewplayer.php" method="post">
  <input type="text" name="firstname" placeholder="Player's First Name" class="textbox" required/><br/>
  <input type="text" name="lastname" placeholder="Player's Last Name" class="textbox" required/><br/>
  <select class="select" name="teamID" required>
    <option value="" disabled selected hidden>Team Name</option>
          <?php
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$teamID."\">".$teamname."</option>";
					}
          ?>
  </select><br/>
  <input type="submit" class="btn_reg" value="Add New Player" name="submit"/>
  </form>
  </div>
  </div>


  <?php
  // by default, all player is listed
  if (isset($_POST["submit"])) {
    // check SQL injection
    if (isset($_POST["firstname"]) && !empty($_POST["firstname"]) && isset($_POST["lastname"]) && !empty($_POST["lastname"]) && isset($_POST["teamID"]) && !empty($_POST["teamID"])) {
      $firstname = ucfirst(strip_tags(htmlspecialchars($_POST["firstname"])));
      $lastname = ucfirst(strip_tags(htmlspecialchars($_POST["lastname"])));
      $teamID = strip_tags(htmlspecialchars($_POST["teamID"]));

      $query = "INSERT INTO Player (FirstName, LastName, TeamID) VALUES (?, ?, ?)";
      $stmt = $db->prepare($query);
      $stmt->bind_param("ssi", $firstname, $lastname, $teamID);
      $stmt->execute();
      echo '<script type="text/javascript"> alert("New player is successfully added into the roster!")</script>';
    }
  }
  ?>

</body>
</html>
