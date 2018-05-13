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
  <title>Add New Statistic Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $db = configDB($_SESSION["role"]);
    $query = "SELECT ID, FirstName, LastName FROM Player";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($playerid, $playerfirstname, $playerlastname);
    }

    ?>

  <div class="header">
    Add New Statistic
  </div>
  <div class="container">
  <div class="box">
  <form action="addnewstatistic.php" method="post">
    <select class="select" name="playerid" required>
      <option value="" disabled selected hidden>Player's Name</option>
            <?php
  					$stmt->data_seek(0);
  					while ($stmt->fetch()){
  						echo "<option value=\"".$playerid."\">".$playerfirstname." ".$playerlastname."</option>";
  					}
            ?>
    </select><br/>
  <input type="text" name="playingtime" placeholder="Playing Time (Minute:Second)" class="textbox" required/><br/>
  <input type="number" name="point" placeholder="Point" class="textbox" min="0" required/><br/>
  <input type="number" name="assist" placeholder="Assist" class="textbox" min="0" required/><br/>
  <input type="number" name="rebound" placeholder="Rebound" class="textbox" min="0" required/><br/>
  <input type="submit" class="btn_reg" value="Add New Statistic" name="submit"/>
  </form>
  </div>
  </div>


  <?php
  if (isset($_POST["submit"])) {
    // check SQL injection
    if (isset($_POST["playerid"]) && !empty($_POST["playerid"]) && isset($_POST["playingtime"]) && !empty($_POST["playingtime"])
    && isset($_POST["point"]) && !empty($_POST["point"]) && isset($_POST["rebound"]) && !empty($_POST["rebound"])
    && isset($_POST["assist"]) && !empty($_POST["assist"])) {
      // set array for playingtime
      $playingtimearr = array('min'=>0, 'sec'=>0);
      $playingtime = trim(strip_tags(htmlspecialchars(htmlentities($_POST["playingtime"]))));
      $point = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["point"]))));
      $rebound = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["rebound"]))));
      $assist = (int)trim(strip_tags(htmlspecialchars($_POST["assist"])));
      $playerid = (int)trim(strip_tags(htmlspecialchars($_POST["playerid"])));
      if(is_string($playingtime)) {
        $playingtime = preg_replace('/[^0-9:]/', '', $playingtime);
        $playingtime = explode(':', $playingtime);
        if (count($playingtime) >= 2) {
          $playingtimearr['sec'] = (int)$playingtime[1];
        } else {
          $playingtimearr['sec'] = (int)0;
        }
        $playingtimearr['min'] = (int)$playingtime[0];
        if ($playingtimearr['min'] < 0 || $playingtimearr['sec'] < 0 || $playingtimearr['min'] > 40 ||
        $playingtimearr['sec'] >= 60 || ($playingtimearr['min'] == 40 && $playingtimearr['sec'] != 0)) {
          echo "<script type=\"text/javascript\"> alert(\"Your input is invalid!\")</script>";
          echo "<script>window.location = 'addnewstatistic.php';</script>";
        }
      }

      $query = "INSERT INTO Statistics (PlayerID, PlayTimeMin, PlayTimeSec, Point, Assist, Rebound) VALUES (?, ?, ?, ?, ?, ?)";
      if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("iiiiii", $playerid, $playingtimearr['min'], $playingtimearr['sec'], $point, $assist, $rebound);
        $stmt->execute();
        echo '<script type="text/javascript"> alert("New statistic  is successfully added into the roster!")</script>';
        echo "<script>window.location = 'statistics.php';</script>";
      } else {
        echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
      }
    }
  }
  ?>

</body>
</html>
