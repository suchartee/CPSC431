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
  <title>Modify Coach Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    // prepare for the <select><option></option></select>
    $db = configDB($_SESSION["role"]);
    $query = "SELECT ID, FirstName, LastName FROM Coach";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($coachidDB, $coachfirstnameDB, $coachlastnameDB);
    }

    // retrieve info from previous page (user's choice)
    if (isset($_GET["coachid"]) && !empty($_GET["coachid"]) && isset($_GET["coachfirstname"]) && !empty($_GET["coachfirstname"]) &&
    isset($_GET["coachlastname"]) && !empty($_GET["coachlastname"]) && isset($_GET["teamid"]) && !empty($_GET["teamid"]) &&
    isset($_GET["teamname"]) && !empty($_GET["teamname"])) {
      $_SESSION["coachid"] = $_GET["coachid"];
      $firstname = $_GET["coachfirstname"];
      $lastname = $_GET["coachlastname"];
      $_SESSION["teamid"] = $_GET["teamid"];
      $teamname = $_GET["teamname"];
    } else {
      $coachid = 0;
      $firstname = "";
      $lastname = "";
      $teamid = 0;
      $teamname = "";
    }
    ?>

  <div class="header">
    Modify Coach
  </div>
  <div class="container">
  <div class="box">
  <form action="changecoachinfo.php" method="post">
  <label>Coach's Name</label><br/>
  <select class="select" name="coachid" required>
    <option value="<?php echo $_SESSION["coachid"]?>"><?php echo $firstname." ".$lastname ?></option>
          <?php
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$coachidDB."\">".$coachfirstnameDB." ".$coachlastnameDB."</option>";
					}
          ?>
  </select><br/>
  <select class="select" name="teamID" required>
    <option value="<?php echo $_SESSION["teamid"]?>"><?php echo $teamname ?></option>
          <?php
          $query = "SELECT ID, TeamName FROM Team";
          if ($stmt = $db->prepare($query)) {
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($teamidDB, $teamnameDB);
          }
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$teamidDB."\">".$teamnameDB."</option>";
					}
          ?>
  </select><br/>
  <input type="submit" class="btn_reg" value="Modify Coach" name="submit"/>
  </form>
  </div>
  </div>


  <?php
  if (isset($_POST["submit"])) {
    // check SQL injection
    if (isset($_POST["coachid"]) && !empty($_POST["coachid"]) && isset($_POST["teamID"]) && !empty($_POST["teamID"])) {
      $coachidDB = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["coachid"]))));
      $teamidDB = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["teamID"]))));

      unset($_SESSION["coachid"]);
      unset($_SESSION["teamid"]);

      $query = "UPDATE Coach SET TeamID = ? WHERE ID = ?";
      if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("ii", $teamidDB, $coachidDB);
        $stmt->execute();
        echo '<script type="text/javascript"> alert("You have successfully changed coach\'s information!")</script>';
        echo "<script>window.location = 'modifycoach.php';</script>";
      } else {
        echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
        echo "<script>window.location = 'dashboard.php';</script>";
      }
    }
  }
  ?>

</body>
</html>
