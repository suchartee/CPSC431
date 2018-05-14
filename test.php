<?php
require_once "functions.php";
require_once "config.php";

$db = configDB(5);

 ?>
 <!DOCTYPE html>
 <html>
 <head>
   <title>Basketball Roster Project</title>
   <link rel="stylesheet" href="style.css">
   <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
   <script type="text/javascript">
       $(function () {
           $("#search").change(function () {
               if ($(this).val() == "searchByFirstName" || $(this).val() == "searchByLastName" || $(this).val() == "searchByTeam") {
                   $("#criteria").show();
                   $("#searchButton").show();
               } else {
                   $("#criteria").hide();
               }
           });
       });
   </script>
 </head>

 <body>

   <ul class="drop_menu">
     <?php
     $nextAttempt = date("Y-m-d H:i:s", strtotime("+5 minutes"));

     // reformat for the user's ease of reading
     //$nextAttemptFormat = date("l F d, Y. g:i:s A", strtotime($nextAttempt));
     //echo $nextAttemptFormat;

     // Hash a new password for storing in the database.
     // The function automatically generates a cryptographically safe salt.
     $hashedpassword = password_hash($password, PASSWORD_DEFAULT);

     if (password_verify($password, $hashToStoreInDb)) {
      echo " YAY ";
    }

     // Check if the hash of the entered login password, matches the stored hash.
     // The salt and the cost factor will be extracted from $existingHashFromDb.
     //$isPasswordCorrect = password_hash("admin", PASSWORD_DEFAULT);
     //echo " \\n".$isPasswordCorrect;



     /*
     // select all button that this role can see
     $query = "SELECT Button.ID, Button.Name, Button.Link, Permission.MenuType FROM Permission JOIN Button ON Permission.ButtonID = Button.ID AND RoleID = 2 AND Permission != 0 ORDER BY Button.ID";
     $stmt = $db->prepare($query);
     //$stmt->bind_param("s", $username);
     $stmt->execute();
     $stmt->store_result();
     $stmt->bind_result($buttonID, $buttonName, $link, $menu);
     $stmt->data_seek(0);
     while( $stmt->fetch() ) {
      if ($menu == 0) {
        echo "<li><a href=\"".$link."\">". $buttonName ."</a></li>";
      } else if ($menu == 1) {  // is headmenu
        echo "<li><a href=\"".$link."\">". $buttonName ."</a><ul>";
      } else if ($menu == 2){ // is last submenu
        echo "<li><a href=\"".$link."\">". $buttonName ."</a></li></ul></li>";
      }
    }*/
     ?>
   </ul>

<form action="test.php" method="post">
   <div class="header">
     <select name="search" id="search" onchange="showfield(this.options[this.selectedIndex].value)">
       <option value="viewAllPlayer">View All Player</option>
       <option value="searchByFirstName">Search By First Name</option>
       <option value="searchByLastName">Search By Last Name</option>
       <option value="searchByTeam">Search By Team Name</option>
     </select>
       <input type="date" name="datepicker" id="datepicker" />
     <input type="submit" name="searchButton" id="searchButton" value="Search"/>
   </div>
 </form>

<?php
if (isset($_POST["searchButton"])) {
  $date = date("Y-m-d", strtotime($_POST["datepicker"]));
  echo $date;
} else {
  echo "Yike";
}

?>
</body>
</html>





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
    if (isset($_GET["matchid"]) && !empty($_GET["matchid"]) && isset($_GET["hometeam"]) && !empty($_GET["hometeam"]) &&
    isset($_GET["awayteam"]) && !empty($_GET["awayteam"]) && isset($_GET["homescore"]) && isset($_GET["dateplayed"]) &&
    !empty($_GET["dateplayed"]) && isset($_GET["awayscore"]) && isset($_GET["winteam"]) && !empty($_GET["winteam"]) &&
    isset($_GET["lostteam"]) && !empty($_GET["lostteam"])) {
      $_SESSION["matchid"] = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["matchid"]))));
      $hometeam = ucwords(trim(strip_tags(htmlspecialchars(htmlentities($_GET["hometeam"])))));
      $awayteam = ucwords(trim(strip_tags(htmlspecialchars(htmlentities($_GET["awayteam"])))));
      $homescore = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["homescore"]))));
      $awayscore = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["awayscore"]))));
      $winteam = ucwords(trim(strip_tags(htmlspecialchars(htmlentities($_GET["winteam"])))));
      $lostteam = ucwords(trim(strip_tags(htmlspecialchars(htmlentities($_GET["lostteam"])))));
      $dateplayed = trim(strip_tags(htmlspecialchars(htmlentities($_GET["dateplayed"]))));

      $db = configDB($_SESSION["role"]);
      // try to find the id of the hometeam and awayteam and winteam and lostteam
      $query = "SELECT HomeTeamID, AwayTeamID, WinTeamID, LostTeamID FROM Matches WHERE ID = ?";
      if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $_SESSION["matchid"]);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hometeamid, $awayteamid, $winteamid, $lostteamid);
        $stmt->data_seek(0);
        while ($stmt->fetch()) {
          $hometeamidGet = $hometeamid;
          $awayteamidGet = $awayteamid;
          $winteamidGet = $winteamid;
          $lostteamidGet = $lostteamid;
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
        $stmt->bind_result($teamid, $teamname);
      } else {
        echo '<script type="text/javascript"> alert("Error!")</script>';
        echo "<script>window.location = 'modifymatch.php';</script>";
      }
    } else {
      echo '<script type="text/javascript"> alert("3Error!")</script>';
      //echo "<script>window.location = 'modifymatch.php';</script>";
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
      						echo "<option value=\"".$teamid."\">".$teamname."</option>";
      					}
                ?>
        </select><br/>
        <label>Away Team</label><br/>
        <select class="select" name="awayteamid" required>
          <option value="<?php echo $awayteamidGet; ?>"><?php echo $awayteam; ?></option>
                <?php
      					$stmt->data_seek(0);
      					while ($stmt->fetch()){
      						echo "<option value=\"".$teamid."\">".$teamname."</option>";
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
      						echo "<option value=\"".$teamid."\">".$teamname."</option>";
      					}
                ?>
        </select><br/>
        <label>Lost Team</label><br/>
        <select class="select" name="lostteamid" required>
          <option value="<?php echo $lostteamidGet; ?>"><?php echo $lostteam; ?></option>
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

      echo '<script type="text/javascript"> alert("hey2!")</script>';
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



<!--ref: http://www.cssterm.com/css-menus/horizontal-css-menu/simple-drop-down-menu>
