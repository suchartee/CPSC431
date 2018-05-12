<?php
  session_start();
  if (empty($_SESSION["role"]) && (empty($_SESSION["authenticated"]))) {
    echo "<script type=\"text/javascript\"> alert(\"You must log in to the system first\")</script>";
    echo "<script>window.location = 'index.php';</script>"; // redirect to index.php (login page)
  }

  require_once "config.php";
  require_once "functions.php";

  if (!checkPermission($_SESSION["role"], basename($_SERVER['PHP_SELF']))) {
    echo "<script type=\"text/javascript\"> alert(\"You cannot see this page\")</script>";
    echo "<script>window.location = 'dashboard.php';</script>"; // redirect to index.php (login page)
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add New Match Page</title>
  <link rel="stylesheet" href="style.css">
  <script type="text/javascript">

  </script>
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $db = configDB($_SESSION["role"]);
    $query = "SELECT ID, TeamName FROM Team";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($teamID, $teamname);
    }

    ?>

  <div class="header">
    Add New Match
  </div>
  <div class="container">
  <div class="box">
  <form action="addnewmatch.php" method="post">
    <label>Date Played</label><br/>
    <input type="date" name="datepicker" id="datepicker" required/>
    <select class="select" id="hometeamsearch" name="hometeamid" required>
      <option value="" disabled selected hidden>Home Team Name</option>
            <?php
  					$stmt->data_seek(0);
  					while ($stmt->fetch()){
  						echo "<option value=\"".$teamID."\">".$teamname."</option>";
  					}
            ?>
    </select><br/>
    <select class="select" id="awayteamsearch" name="awayteamid" required>
      <option value="" disabled selected hidden>Away Team Name</option>
            <?php
  					$stmt->data_seek(0);
  					while ($stmt->fetch()){
  						echo "<option value=\"".$teamID."\">".$teamname."</option>";
  					}
            ?>
    </select><br/>
  <input type="number" name="hometeamscore" placeholder="Home Team Score" pattern="[0-9]*" class="textbox" required/><br/>
  <input type="number" name="awayteamscore" placeholder="Away Team Score" pattern="[0-9]*" class="textbox" required/><br/>
  <select class="select" name="winteamid" required>
    <option value="" disabled selected hidden>Win Team Name</option>
          <?php
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$teamID."\">".$teamname."</option>";
					}
          ?>
  </select><br/>
  <select class="select" name="lostteamid" required>
    <option value="" disabled selected hidden>Lost Team Name</option>
          <?php
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$teamID."\">".$teamname."</option>";
					}
          ?>
  </select><br/>
  <input type="submit" class="btn_reg" value="Add New Match" name="submit"/>
  </form>
  </div>
  </div>


  <?php
  if (isset($_POST["submit"])) {
    // check SQL injection
    if (isset($_POST["hometeamid"]) && !empty($_POST["hometeamid"]) && isset($_POST["awayteamid"])
    && !empty($_POST["awayteamid"]) && isset($_POST["hometeamscore"]) && !empty($_POST["hometeamscore"])
    && isset($_POST["awayteamscore"]) && !empty($_POST["awayteamscore"])
    && isset($_POST["winteamid"]) && !empty($_POST["winteamid"]) && isset($_POST["lostteamid"]) && !empty($_POST["lostteamid"])
    && isset($_POST["datepicker"]) && !empty($_POST["datepicker"])) {
      $dateplayed = $_POST["datepicker"];
      $hometeam = strip_tags(htmlspecialchars($_POST["hometeamid"]));
      $awayteam = strip_tags(htmlspecialchars($_POST["awayteamid"]));
      $hometeamscore = strip_tags(htmlspecialchars($_POST["hometeamscore"]));
      $awayteamscore = strip_tags(htmlspecialchars($_POST["awayteamscore"]));
      $winteam = strip_tags(htmlspecialchars($_POST["winteamid"]));
      $lostteam = strip_tags(htmlspecialchars($_POST["lostteamid"]));

      $query = "INSERT INTO Matches (HomeTeamID, AwayTeamID, HomeScore, AwayScore, DatePlayed, WinTeamID, LostTeamID) VALUES (?, ?, ?, ?, ?, ?, ?)";
      if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("iiiisii", $hometeam, $awayteam, $hometeamscore, $awayteamscore, $dateplayed, $winteam, $lostteam);
        $stmt->execute();
        echo '<script type="text/javascript"> alert("New match is successfully added into the roster!")</script>';
        echo "<script>window.location = 'match.php';</script>";
      } else {
        echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
      }
    }
  }
  ?>

</body>
</html>
