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
    // retrieve info from previous page (user's choice)
    if (isset($_GET["matchid"]) && !empty($_GET["matchid"]) && isset($_GET["hometeam"]) && !empty($_GET["hometeam"]) &&
    isset($_GET["awayteam"]) && !empty($_GET["awayteam"]) && isset($_GET["homescore"]) && isset($_GET["dateplayed"]) &&
    !empty($_GET["dateplayed"]) && isset($_GET["awayscore"]) && isset($_GET["winteam"]) && !empty($_GET["winteam"]) &&
    isset($_GET["lostteam"]) && !empty($_GET["lostteam"])) {
      $_SESSION["matchid"] = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["matchid"]))));
      $hometeam = trim(strip_tags(htmlspecialchars(htmlentities($_GET["hometeam"]))));
      $awayteam = trim(strip_tags(htmlspecialchars(htmlentities($_GET["awayteam"]))));
      $homescore = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["homescore"]))));
      $awayscore = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["awayscore"]))));
      $dateplayed = trim(strip_tags(htmlspecialchars(htmlentities($_GET["dateplayed"]))));
      $winteam = trim(strip_tags(htmlspecialchars(htmlentities($_GET["winteam"]))));
      $lostteam = trim(strip_tags(htmlspecialchars(htmlentities($_GET["lostteam"]))));
    } else {
      $matchid = 0;
      $hometeam = "";
      $lostteam = "";
      $homescore = 0;
      $awayscore = 0;
      $dateplayed = "";
      $winteam = "";
      $lostteam = "";
    }

    $db = configDB($_SESSION["role"]);
    // try to find the id for the hometeam and awayteam and winteam and lostteam
    $query = "SELECT HomeTeamID, AwayTeamID, WinTeamID, LostTeamID FROM Matches WHERE ID = ?";
    if ($stmt = $db->prepare($query)) {
      $stmt->bind_param("i", $_SESSION["matchid"]);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($hometeamidDB, $awayteamidDB, $winteamidDB, $lostteamidDB);
      $stmt->data_seek(0);
      while ($stmt->fetch()) {
        $hometeamidGet = $hometeamidDB;
        $awayteamidGet = $awayteamidDB;
        $winteamidGet = $winteamidDB;
        $lostteamidGet = $lostteamidDB;
      }
    } else {
      echo '<script type="text/javascript"> alert("Error!")</script>';
      echo "<script>window.location = 'modifymatch.php';</script>";
    }

    // prepare for the <select><option></option></select>
    $query = "SELECT ID, TeamName FROM Team";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($teamiddb, $teamnamedb);
    } else {
      echo '<script type="text/javascript"> alert("Error!")</script>';
      echo "<script>window.location = 'modifymatch.php';</script>";
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
      <option value="<?php echo $hometeamidGet; ?>"><?php echo $hometeam; ?></option>
            <?php
            $stmt->data_seek(0);
            while ($stmt->fetch()){
              echo "<option value=\"".$teamiddb."\">".$teamnamedb."</option>";
            }
            ?>
    </select><br/>
    <label>Away Team</label><br/>
    <select class="select" name="awayteamid" required>
      <option value="<?php echo $awayteamidGet; ?>"><?php echo $awayteam; ?></option>
            <?php
            $stmt->data_seek(0);
            while ($stmt->fetch()){
              echo "<option value=\"".$teamiddb."\">".$teamnamedb."</option>";
            }
            ?>
    </select><br/>
    <label>Date Played</label><br/>
    <input type="date" name="datepicker" id="datepicker" value="<?php echo $dateplayed; ?>" class="textbox" required/><br/>
    <label>Home Team Score</label><br/>
    <input type="num" name="homescore" value="<?php echo $homescore; ?>" class="textbox" min="0" required/><br/>
    <label>Away Team Score</label><br/>
    <input type="num" name="awayscore" value="<?php echo $awayscore; ?>" class="textbox" min="0" required/><br/>
    <label>Win Team</label><br/>
    <select class="select" name="winteamid" required>
      <option value="<?php echo $winteamidGet; ?>"><?php echo $winteam; ?></option>
            <?php
            $stmt->data_seek(0);
            while ($stmt->fetch()){
              echo "<option value=\"".$teamiddb."\">".$teamnamedb."</option>";
            }
            ?>
    </select><br/>
    <label>Lost Team</label><br/>
    <select class="select" name="lostteamid" required>
      <option value="<?php echo $lostteamidGet; ?>"><?php echo $lostteam; ?></option>
            <?php
            $stmt->data_seek(0);
            while ($stmt->fetch()){
              echo "<option value=\"".$teamiddb."\">".$teamnamedb."</option>";
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
    if (isset($_POST["hometeamid"]) && !empty($_POST["hometeamid"]) && isset($_POST["awayteamid"]) && !empty($_POST["awayteamid"]) &&
    isset($_POST["homescore"]) && isset($_POST["awayscore"]) && isset($_POST["datepicker"]) && !empty($_POST["datepicker"]) &&
    isset($_POST["winteamid"]) && !empty($_POST["winteamid"]) && isset($_POST["lostteamid"]) && !empty($_POST["lostteamid"])) {
      $hometeamidDB = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["hometeamid"]))));
      $awayteamidDB = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["awayteamid"]))));
      $dateplayedDB = trim(strip_tags(htmlspecialchars(htmlentities($_POST["datepicker"]))));
      $homescoreDB = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["homescore"]))));
      $awayscoreDB = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["awayscore"]))));
      $winteamidDB = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["winteamid"]))));
      $lostteamidDB = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["lostteamid"]))));

      $query = "UPDATE Matches
                SET HomeTeamID = ?, AwayTeamID = ?, HomeScore = ?,
                    AwayScore = ?, DatePlayed = ?, WinTeamID = ?,
                    LostTeamID = ?
                WHERE ID = ?";
      if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("iiiisiii",
        $hometeamidDB, $awayteamidDB, $homescoreDB, $awayscoreDB, $dateplayedDB, $winteamidDB, $lostteamidDB, $_SESSION["matchid"]);
        $stmt->execute();

        unset($_SESSION["matchid"]);

        echo '<script type="text/javascript"> alert("You have successfully changed match\'s information!")</script>';
        echo "<script>window.location = 'modifymatch.php';</script>";
      } else {
        echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
        echo "<script>window.location = 'dashboard.php';</script>";
      }
    } else {
      echo '<script type="text/javascript"> alert("Error!")</script>';
      echo "<script>window.location = 'modifymatch.php';</script>";
    }
  }
  ?>

</body>
</html>
