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
  <title>Modify Statistic Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $db = configDB($_SESSION["role"]);
    $query = "SELECT ID, FirstName, LastName From Player ORDER BY LastName, FirstName";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($pid, $playerfirstnameDB, $playerlastnameDB);
    }

    // retrieve info from previous page (user's choice)
    if (isset($_GET["statid"]) && !empty($_GET["statid"]) && isset($_GET["pid"]) && !empty($_GET["pid"]) &&
    isset($_GET["firstname"]) && !empty($_GET["firstname"]) && isset($_GET["lastname"]) && !empty($_GET["lastname"]) &&
    isset($_GET["timemin"]) && isset($_GET["timesec"]) && isset($_GET["point"]) && isset($_GET["assist"]) &&
    isset($_GET["rebound"])) {  // 0 is treated as empty
      $_SESSION["statid"] = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["statid"]))));
      $playerid = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["pid"]))));
      $firstname = trim(strip_tags(htmlspecialchars(htmlentities($_GET["firstname"]))));
      $lastname = trim(strip_tags(htmlspecialchars(htmlentities($_GET["lastname"]))));
      $timemin = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["timemin"]))));
      $timesec = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["timesec"]))));
      $point = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["point"]))));
      $assist = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["assist"]))));
      $rebound = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["rebound"]))));
    } else {
      $statid = 0;
      $playerid = 0;
      $firstname = "";
      $lastname = "";
      $timemin = 0;
      $timesec = 0;
      $point = 0;
      $assist = 0;
      $rebound = 0;
    }
    ?>

  <div class="header">
    Modify Statistic
  </div>
  <div class="container">
  <div class="box">
  <form action="changestatinfo.php" method="post">
  <label>Player's Name</label><br/>
     <select class="select" name="playerid" required>
       <option value="<?php echo $playerid; ?>"><?php echo $firstname." ".$lastname; ?></option>
             <?php
   					$stmt->data_seek(0);
   					while ($stmt->fetch()){
   						echo "<option value=\"".$pid."\">".$playerfirstnameDB." ".$playerlastnameDB."</option>";
   					}
             ?>
     </select><br/>
  <label>Player's Playing Time</label><br/>
  <input type="text" name="playingtime" value="<?php echo str_pad($timemin, 2, "0", STR_PAD_LEFT) .":". str_pad($timesec, 2, "0", STR_PAD_LEFT); ?>" class="textbox" required/><br/>
  <label>Player's Point</label><br/>
  <input type="number" name="point" value="<?php echo $point; ?>" class="textbox" min="0" required/><br/>
  <label>Player's Assist</label><br/>
  <input type="number" name="assist" value="<?php echo $assist; ?>" class="textbox" min="0" required/><br/>
  <label>Player's Rebound</label><br/>
  <input type="number" name="rebound" value="<?php echo $rebound; ?>" class="textbox" min="0" required/><br/>
  <input type="submit" class="btn_reg" value="Modify Statistic" name="submit"/>
  </form>
  </div>
  </div>


  <?php
  if (isset($_POST["submit"])) {
    // check SQL injection
    if (isset($_POST["playerid"]) && !empty($_POST["playerid"]) && isset($_POST["playingtime"]) &&
    !empty($_POST["playingtime"]) && isset($_POST["point"]) && isset($_POST["assist"]) &&
    isset($_POST["rebound"])) {
      // set array for playingtime
      $playingtimearr = array('min'=>0, 'sec'=>0);
      $playingtime = trim(strip_tags(htmlspecialchars(htmlentities($_POST["playingtime"]))));
      $point = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["point"]))));
      $rebound = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["rebound"]))));
      $assist = (int)trim(strip_tags(htmlspecialchars($_POST["assist"])));
      $statid = (int)$_SESSION["statid"];
      $playerid = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["playerid"]))));
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
          echo "<script>window.location = 'modifystatistic.php';</script>";
        }
      }

      unset($_SESSION["statid"]);

      echo $playerid, $playingtimearr['min'], $playingtimearr['sec'], $point, $assist, $rebound, $statid;

      $query = "UPDATE Statistics SET PlayerID = ?, PlayTimeMin = ?, PlayTimeSec = ?, Point = ?, Assist = ?, Rebound = ? WHERE ID = ?";
      if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("iiiiiii", $playerid, $playingtimearr['min'], $playingtimearr['sec'], $point, $assist, $rebound, $statid);
        $stmt->execute();
        echo '<script type="text/javascript"> alert("You have successfully changed player\'s statistic!")</script>';
        echo "<script>window.location = 'modifystatistic.php';</script>";
      } else {
        echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
        echo "<script>window.location = 'dashboard.php';</script>";
      }
    }
  }
  ?>

</body>
</html>
