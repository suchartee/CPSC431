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
  <title>Modify Team Page</title>
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
    if (isset($_GET["teamid"]) && isset($_GET["teamname"]) && isset($_GET["coachid"]) && isset($_GET["coachfirstname"]) && isset($_GET["coachlastname"])) {
      $_SESSION["teamid"] = $_GET["teamid"];
      $teamname = $_GET["teamname"];
      $_SESSION["coachid"] = $_GET["coachid"];
      $coachfirstname = $_GET["coachfirstname"];
      $coachlastname = $_GET["coachlastname"];
      $wincount = $_GET["wincount"];
      $lostcount = $_GET["lostcount"];
    } else {
      $teamid = 0;
      $teamname = "";
      $coachid = 0;
      $coachfirstname = "";
      $coachlastname = "";
      $wincount = 0;
      $lostcount = 0;
    }
    ?>

  <div class="header">
    Modify Team
  </div>
  <div class="container">
  <div class="box">
  <form action="changeteaminfo.php" method="post">
  <label>Team Name</label><br/>
  <input type="text" name="teamname" value="<?php echo $teamname ?>" class="textbox" required/><br/>
  <label>Team's Coach Name</label><br/>
  <select class="select" name="coachID" required>
    <option value="<?php echo $_SESSION["coachid"]; ?>"><?php echo $coachfirstname . " " . $coachlastname; ?></option>
          <?php
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$coachidDB."\">".$coachfirstnameDB." ".$coachlastnameDB."</option>";
					}
          ?>
  </select><br/>
  <label>Win Game Count</label><br/>
  <input type="number" name="wincount" value="<?php echo $wincount ?>" class="textbox" required/><br/>
  <label>Lost Game Count</label><br/>
  <input type="number" name="lostcount" value="<?php echo $lostcount ?>" class="textbox" required/><br/>
  <input type="submit" class="btn_reg" value="Modify Team" name="submit"/>
  </form>
  </div>
  </div>


  <?php
  if (isset($_POST["submit"])) {
    // check SQL injection
    if (!empty($_POST["teamname"]) && !empty($_POST["coachID"])) {
      // sql injection
      // prepare info for update the table
      $teamnameDB = ucwords(strip_tags(htmlspecialchars($_POST["teamname"])));
      $coachidDB = strip_tags(htmlspecialchars($_POST["coachID"]));
      $coachid = $_SESSION["coachid"];
      $teamidDB = $_SESSION["teamid"];
      if ($coachidDB == $coachid) { // in case the coach is the same
        $coachidDB = $_SESSION["coachid"];
      }
      $wincountDB = strip_tags(htmlspecialchars($_POST["wincount"]));
      $lostcountDB = strip_tags(htmlspecialchars($_POST["lostcount"]));

      unset($_SESSION["coachid"]);
      unset($_SESSION["teamid"]);

      $query = "UPDATE Team SET TeamName = ?, WinCount = ?, LostCount = ?, CoachID = ? WHERE ID = ?";
      if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("siiii", $teamnameDB, $wincountDB, $lostcountDB, $coachidDB, $teamidDB);
        $stmt->execute();
        echo '<script type="text/javascript"> alert("You have successfully changed team\'s information!")</script>';
        echo "<script>window.location = 'modifyteam.php';</script>";
      } else {
        echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
        echo "<script>window.location = 'dashboard.php';</script>";
      }
    }
  }
  ?>

</body>
</html>
