<?php
  session_start();
  if (empty($_SESSION["role"]) && (empty($_SESSION["authenticated"]))) {
    echo "<script type=\"text/javascript\"> alert(\"You must log in to the system first\")</script>";
    echo "<script>window.location = 'index.php';</script>"; // redirect to index.php (login page)
  }

  require_once "config.php";
  require_once "functions.php";

  // only for any changexxxinfo.php or changeuserpassword.php
  $fullurl = basename($_SERVER['PHP_SELF']);
  $url = explode("?", $fullurl);

  if (!checkPermission($_SESSION["role"], $url[0])) {
    echo "<script type=\"text/javascript\"> alert(\"You cannot see this page\")</script>";
    echo "<script>window.location = 'dashboard.php';</script>"; // redirect to index.php (login page)
  }
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
    // prepare for the <select><option></option></select>
    $db = configDB($_SESSION["role"]);
    $query = "SELECT ID, TeamName FROM Team";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($teamidDB, $teamnameDB);
    }

    // retrieve info from previous page (user's choice)
    if (isset($_GET["playerid"]) && !empty($_GET["playerid"]) && isset($_GET["firstname"]) && !empty($_GET["firstname"]) &&
    isset($_GET["lastname"]) && !empty($_GET["lastname"]) && isset($_GET["teamid"]) && !empty($_GET["teamid"]) &&
    isset($_GET["teamname"]) && !empty($_GET["teamname"])) {
      $_SESSION["playerid"] = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["playerid"]))));
      $firstname = trim(strip_tags(htmlspecialchars(htmlentities($_GET["firstname"]))));
      $lastname = trim(strip_tags(htmlspecialchars(htmlentities($_GET["lastname"]))));
      $_SESSION["teamid"] = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["teamid"]))));
      $teamname = trim(strip_tags(htmlspecialchars(htmlentities($_GET["teamname"]))));
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
  <label>Player's First Name</label><br/>
  <input type="text" name="firstname" value="<?php echo $firstname; ?>" class="textbox" readonly="readonly" required/><br/>
  <label>Player's Last Name</label><br/>
  <input type="text" name="lastname" value="<?php echo $lastname; ?>" class="textbox" readonly="readonly" required/><br/>
  <label>Player's Team Name</label><br/>
  <select class="select" name="teamID" required>
    <option value="<?php echo $_SESSION["teamid"]; ?>"><?php echo $teamname; ?></option>
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
    if (isset($_POST["firstname"]) && !empty($_POST["firstname"]) && isset($_POST["lastname"]) && !empty($_POST["lastname"])
    && isset($_POST["teamID"]) && !empty($_POST["teamID"])) {
      $firstnameDB = ucwords(trim(strip_tags(htmlspecialchars(htmlentities($_POST["firstname"])))));
      $lastnameDB = ucwords(trim(strip_tags(htmlspecialchars(htmlentities($_POST["lastname"])))));
      $teamidDB = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["teamID"]))));
      $playerid = (int)trim(strip_tags(htmlspecialchars(htmlentities($_SESSION["playerid"]))));

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
