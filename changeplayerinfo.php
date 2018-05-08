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
  <title>Modify Player Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $db = configDB($_SESSION["role"]);
    $query = "SELECT ID, TeamName FROM Team";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($teamidDB, $teamnameDB);
    }
    if (isset($_GET["firstname"]) && isset($_GET["lastname"]) && isset($_GET["teamid"]) && isset($_GET["teamname"])) {
      $_SESSION["playerid"] = $_GET["playerid"];
      $firstname = $_GET["firstname"];
      $lastname = $_GET["lastname"];
      $_SESSION["teamid"] = $_GET["teamid"];
      $teamname = $_GET["teamname"];
    } else {
      $playerid = 0;
      $firstname = "";
      $lastname = "";
      $teamid = 0;
      $teamname = "";
    }
    ?>

  <div class="header">
    Modify Player
  </div>
  <div class="container">
  <div class="box">
  <form action="changeplayerinfo.php" method="post">
  <input type="text" name="firstname" value="<?php echo $firstname ?>" class="textbox" required/><br/>
  <input type="text" name="lastname" value="<?php echo $lastname ?>" class="textbox" required/><br/>
  <select class="select" name="teamID" required>
    <option value="<?php echo $_SESSION["teamid"]?>"><?php echo $teamname ?></option>
          <?php
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$teamidDB."\">".$teamnameDB."</option>";
					}
          ?>
  </select><br/>
  <input type="submit" class="btn_reg" value="Modify Player" name="submit"/>
  </form>
  </div>
  </div>


  <?php
  if (isset($_POST["submit"])) {
    // check SQL injection
    if (!empty($_POST["firstname"]) && !empty($_POST["lastname"]) && !empty($_POST["teamID"])) {
      $firstnameDB = ucwords(strip_tags(htmlspecialchars($_POST["firstname"])));
      $lastnameDB = ucwords(strip_tags(htmlspecialchars($_POST["lastname"])));
      $teamidDB = strip_tags(htmlspecialchars($_POST["teamID"]));
      $playerid = $_SESSION["playerid"];
      if ($teamidDB == $_SESSION["teamid"]) {
        $teamidDB = $_SESSION["teamid"];
      }

      echo $firstnameDB;
      echo $lastnameDB;
      echo $teamidDB;
      echo $_SESSION["playerid"];

      unset($_SESSION["playerid"]);
      unset($_SESSION["teamid"]);

      $query = "UPDATE Player SET FirstName = ?, LastName = ?, TeamID = ? WHERE ID = ?";
      if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("ssii", $firstnameDB, $lastnameDB, $teamidDB, $playerid);
        $stmt->execute();
        echo '<script type="text/javascript"> alert("You have successfully changed player\'s information!")</script>';
        echo "<script>window.location = 'modifyplayer.php';</script>";
      } else {
        echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
        echo "<script>window.location = 'dashboard.php';</script>";
      }
    }
  }
  ?>

</body>
</html>