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
  <title>Modify Player Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $count = 1;
    $db = configDB($_SESSION["role"]);
    $query = "SELECT Player.ID, FirstName, LastName, Team.ID, TeamName FROM Player
              JOIN Team ON TeamID = Team.ID ORDER BY TeamName, LastName";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($playerID, $firstname, $lastname, $teamid, $teamname);
    }
    $stmt->data_seek(0);
    echo "<div class=\"header\" style=\"display:table;\">Modify Player</div>
          <div class=\"container\">
          <table>
            <tr>
              <th>No.</th>
              <th>Player's First Name</th>
              <th>Player's Last Name</th>
              <th>Player's Team Name</th>
              <th>Action</th>
            </tr>";

        while( $stmt->fetch() ) {
          $row = array('id'=>0, 'playerid'=>0, 'firstname'=>"", 'lastname'=>"", 'teamid'=>0,'teamname'=>"");
          $row['id'] = $count++;
          $row['playerid'] = $playerID;
          $row['firstname']= $firstname;
          $row['lastname']= $lastname;
          $row['teamid']= $teamid;
          $row['teamname']= $teamname;
          echo "<tr>
            <td>". $row['id'] ."</td>
            <td>". $row['firstname'] ."</td>
            <td>". $row['lastname'] ."</td>
            <td>". $row['teamname'] ."</td>
            <td><a href=\"changeplayerinfo.php?playerid=".$row['playerid']."&firstname=".$row['firstname']."&lastname="
            .$row['lastname']."&teamid=".$row['teamid']."&teamname=".$row['teamname']."\">Change</a>
          </tr>";
        }
    echo "</table>
    </div>";
    ?>


</body>
</html>
