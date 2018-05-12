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
    $hometeam = $_GET["hometeam"];
    $awayteam = $_GET["awayteam"];
    $winteam = $_GET["winteam"];
    $lostteam = $_GET["lostteam"];
    $db = configDB($_SESSION["role"]);
    // try to find the id of the hometeam and awayteam and winteam and lostteam
    $query = "SELECT ID FROM Team WHERE TeamName = ?";
    if ($stmt = $db->prepare($query)) {
      $stmt->bind_param("s", $hometeam);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($hometeamid);
      $stmt->data_seek(0);
      while ($stmt->fetch()) {
        $_SESSION["hometeamid"] = $hometeamid;
      }
    } else {
      echo '<script type="text/javascript"> alert("Error!")</script>';
      echo "<script>window.location = 'modifymatch.php';</script>";
    }
    if ($stmt = $db->prepare($query)) {
      $stmt->bind_param("s", $awayteam);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($awayteamid);
      $stmt->data_seek(0);
      while ($stmt->fetch()) {
        $_SESSION["awayteamid"] = $awayteamid;
      }
    } else {
      echo '<script type="text/javascript"> alert("Error!")</script>';
      echo "<script>window.location = 'modifymatch.php';</script>";
    }
    if ($stmt = $db->prepare($query)) {
      $stmt->bind_param("s", $winteam);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($winteamid);
      $stmt->data_seek(0);
      while ($stmt->fetch()) {
        $_SESSION["winteamid"] = $winteamid;
      }
    } else {
      echo '<script type="text/javascript"> alert("Error!")</script>';
      echo "<script>window.location = 'modifymatch.php';</script>";
    }
    if ($stmt = $db->prepare($query)) {
      $stmt->bind_param("s", $lostteam);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($lostteamid);
      $stmt->data_seek(0);
      while ($stmt->fetch()) {
        $_SESSION["lostteamid"] = $lostteamid;
      }
    } else {
      echo '<script type="text/javascript"> alert("Error!")</script>';
      echo "<script>window.location = 'modifymatch.php';</script>";
    }

    // prepare for the <select><option></option></select>
    // try to find the id of the hometeam and awayteam
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
      if ($hometeamidDB == $_SESSION["hometeamid"]) {
        $hometeamidDB = $_SESSION["hometeamid"];
      }
      if ($awayteamidDB == $_SESSION["awayteamid"]) {
        $awayteamidDB = $_SESSION["awayteamid"];
      }
      if ($winteamidDB == $_SESSION["winteamid"]) {
        $winteamidDB = $_SESSION["winteamid"];
      }
      if ($lostteamidDB == $_SESSION["lostteamid"]) {
        $lostteamidDB = $_SESSION["lostteamid"];
      }
      if ($dateplayedDB == $_SESSION["dateplayed"]) {
        $dateplayedDB = $_SESSION["dateplayed"];
      }
      if ($homescoreDB == $_SESSION["homescore"]) {
        $homescoreDB = $_SESSION["homescore"];
      }
      if ($awayscoreDB == $_SESSION["awayscore"]) {
        $awayscoreDB = $_SESSION["awayscore"];
      }

      $query = "UPDATE Matches
              SET HomeTeamID = ?, AwayTeamID = ?, HomeScore = ?,
                  AwayScore = ?, DatePlayed = ?, WinTeamID = ?,
                  LostTeamID = ?
              WHERE ID =
                (SELECT * FROM
                  (SELECT ID FROM Matches
                    WHERE HomeTeamID = ? AND AwayTeamID = ?
                      AND HomeScore = ? AND AwayScore = ?
                      AND DatePlayed = ? AND WinTeamID = ?
                      AND LostTeamID = ?)
                    AS Innertable)";
      if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("iiiisiiiiiisii",
        $hometeamidDB, $awayteamidDB, $homescoreDB, $awayscoreDB, $dateplayedDB, $winteamidDB, $lostteamidDB,
        $_SESSION["hometeamid"], $_SESSION["awayteamid"], $_SESSION["homescore"], $_SESSION["awayscore"], $_SESSION["dateplayed"], $_SESSION["winteamid"], $_SESSION["lostteamid"]
        );
        $stmt->execute();
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
