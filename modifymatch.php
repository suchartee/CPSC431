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
  <title>Modify Match Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $count = 1;
    $db = configDB($_SESSION["role"]);
    $query = "SELECT
              ID,
              (SELECT TeamName FROM Team WHERE ID = HomeTeamID) AS HomeTeam,
              (SELECT TeamName FROM Team WHERE ID = AwayTeamID) AS AwayTeam,
              HomeScore, AwayScore, DatePlayed,
              (SELECT TeamName FROM Team WHERE ID = WinTeamID) AS WinTeam,
              (SELECT TeamName FROM Team WHERE ID = LostTeamID) AS LostTeam
              FROM Matches";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($matchid, $hometeam, $awayteam, $homescore, $awayscore, $dateplayed, $winteam, $lostteam);
      $stmt->data_seek(0);
      echo "<div class=\"header\" style=\"display:table;\">All Matches</div>
            <div class=\"container\">
            <table>
              <tr>
                <th>No.</th>
                <th>Date Played</th>
                <th>Home Team</th>
                <th>Away Team</th>
                <th>Home Team Score</th>
                <th>Away Team Score</th>
                <th>Win Team</th>
                <th>Lost Team</th>
                <th>Action</th>
              </tr>";
          while( $stmt->fetch() ) {
                $row = array('id'=>$count++, 'matchid'=>$matchid, 'dateplayed'=>$dateplayed, 'hometeam'=>$hometeam,'awayteam'=>$awayteam,
                'homescore'=>$homescore, 'awayscore'=>$awayscore, 'winteam'=>$winteam, 'lostteam'=>$lostteam);
                echo "<tr>
                  <td>". $row['id'] ."</td>
                  <td>". $row['dateplayed'] ."</td>
                  <td>". $row['hometeam'] ."</td>
                  <td>". $row['awayteam'] ."</td>
                  <td>". $row['homescore'] ."</td>
                  <td>". $row['awayscore'] ."</td>
                  <td>". $row['winteam'] ."</td>
                  <td>". $row['lostteam'] ."</td>
                  <td><a href=\"changematchinfo.php?matchid=".$row['matchid']."&hometeam=".$row['hometeam']."&awayteam="
                  .$row['awayteam']."&homescore=".$row['homescore']."&awayscore=".$row['awayscore']."&dateplayed="
                  .$row['dateplayed']."&winteam=".$row['winteam']."&lostteam=".$row['lostteam']."\">Change</a>
                </tr>";
          }
      echo "</table>
      </div>";
    }
    ?>


</body>
</html>
