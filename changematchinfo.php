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
  <title>Modify Match Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $matchid = $_GET["matchid"];
    $hometeam = $_GET["hometeam"];
    $awayteam = $_GET["awayteam"];
    $winteam = $_GET["winteam"];
    $lostteam = $_GET["lostteam"];
    $db = configDB($_SESSION["role"]);
    // try to find the id of the hometeam and awayteam and winteam and lostteam
    $query = "SELECT HomeTeamID, AwayTeamID, WinTeamID, LostTeamID FROM Matches WHERE ID = ?";
    if ($stmt = $db->prepare($query)) {
      $stmt->bind_param("i", $matchid);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($hometeamid, $awayteamid, $winteamid, $lostteamid);
      $stmt->data_seek(0);
      while ($stmt->fetch()) {
        $_SESSION["hometeamid"] = $hometeamid;
        $_SESSION["awayteamid"] = $awayteamid;
        $_SESSION["winteamid"] = $winteamid;
        $_SESSION["lostteamid"] = $lostteamid;
      }
    } else {
      echo '<script type="text/javascript"> alert("Error ha!")</script>';
      echo "<script>window.location = 'modifymatch.php';</script>";
    }

    // prepare for the <select><option></option></select>
    $query = "SELECT ID, TeamName FROM Team";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($teamid, $teamname);
    }

    // retrieve info from previous page (user's choice)
    if (isset($_GET["homescore"]) && isset($_GET["awayscore"]) && isset($_GET["dateplayed"])) {
      $_SESSION["dateplayed"] = $_GET["dateplayed"];
      $_SESSION["homescore"] = $_GET["homescore"];
      $_SESSION["awayscore"] = $_GET["awayscore"];
      $_SESSION["matchid"] = $matchid;
    } else {
      $hometeamid = 0;
      $awayteamid = 0;
      $winteamid = 0;
      $lostteamid = 0;
      $dateplayed = "";
      $homescore = "";
      $awayscore = "";
    }
    ?>

  <div class="header">
    Modify Match
  </div>
  <div class="container">
  <div class="box">
  <form action="changematchinfo.php" method="post">
  <label>Home Team</label><br/>
  <select class="select" name="hometeamid" required>
    <option value="<?php echo $_SESSION["hometeamid"]; ?>"><?php echo $hometeam; ?></option>
          <?php
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$teamid."\">".$teamname."</option>";
					}
          ?>
  </select><br/>
  <label>Away Team</label><br/>
  <select class="select" name="awayteamid" required>
    <option value="<?php echo $_SESSION["awayteamid"]; ?>"><?php echo $awayteam; ?></option>
          <?php
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$teamid."\">".$teamname."</option>";
					}
          ?>
  </select><br/>
  <label>Date Played</label><br/>
  <input type="date" name="datepicker" id="datepicker" value="<?php echo $_SESSION["dateplayed"]; ?>" required/><br/>
  <label>Home Team Score</label><br/>
  <input type="text" name="homescore" value="<?php echo $_SESSION["homescore"]; ?>" class="textbox" required/><br/>
  <label>Away Team Score</label><br/>
  <input type="text" name="awayscore" value="<?php echo $_SESSION["awayscore"]; ?>" class="textbox" required/><br/>
  <label>Win Team</label><br/>
  <select class="select" name="winteamid" required>
    <option value="<?php echo $_SESSION["winteamid"]; ?>"><?php echo $winteam; ?></option>
          <?php
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$teamid."\">".$teamname."</option>";
					}
          ?>
  </select><br/>
  <label>Lost Team</label><br/>
  <select class="select" name="lostteamid" required>
    <option value="<?php echo $_SESSION["lostteamid"]; ?>"><?php echo $lostteam; ?></option>
          <?php
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$teamid."\">".$teamname."</option>";
					}
          ?>
  </select><br/>
  <input type="submit" class="btn_reg" value="Modify Match" name="submit"/>
  </form>
  </div>
  </div>

  <?php
  if (isset($_POST["submit"])) {
    // check SQL injection
    if (!empty($_POST["hometeamid"]) && !empty($_POST["awayteamid"]) && !empty($_POST["homescore"]) && !empty($_POST["awayscore"]) && !empty($_POST["datepicker"]) && !empty($_POST["winteamid"]) && !empty($_POST["lostteamid"])) {
      $hometeamidDB = strip_tags(htmlspecialchars($_POST["hometeamid"]));
      $awayteamidDB = strip_tags(htmlspecialchars($_POST["awayteamid"]));
      $dateplayedDB = strip_tags(htmlspecialchars($_POST["datepicker"]));
      $homescoreDB = strip_tags(htmlspecialchars($_POST["homescore"]));
      $awayscoreDB = strip_tags(htmlspecialchars($_POST["awayscore"]));
      $winteamidDB = strip_tags(htmlspecialchars($_POST["winteamid"]));
      $lostteamidDB = strip_tags(htmlspecialchars($_POST["lostteamid"]));

      $query = "UPDATE Matches
              SET HomeTeamID = ?, AwayTeamID = ?, HomeScore = ?,
                  AwayScore = ?, DatePlayed = ?, WinTeamID = ?,
                  LostTeamID = ?
              WHERE ID = ?";
      if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("iiiisiii",
        $hometeamidDB, $awayteamidDB, $homescoreDB, $awayscoreDB, $dateplayedDB, $winteamidDB, $lostteamidDB, $_SESSION["matchid"]);
        $stmt->execute();

        unset($_SESSION["hometeamid"]);
        unset($_SESSION["awayteamid"]);
        unset($_SESSION["winteamid"]);
        unset($_SESSION["lostteamid"]);
        unset($_SESSION["dateplayed"]);
        unset($_SESSION["homescore"]);
        unset($_SESSION["awayscore"]);
        unset($_SESSION["matchid"]);
        echo '<script type="text/javascript"> alert("You have successfully changed match\'s information!")</script>';
        echo "<script>window.location = 'modifymatch.php';</script>";
      } else {
        echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
        echo "<script>window.location = 'dashboard.php';</script>";
      }
    }
  }
  ?>


</body>
</html>
