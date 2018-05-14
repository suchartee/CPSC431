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
  <title>Modify Statistic Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $count = 1;
    $db = configDB($_SESSION["role"]);
    $query = "SELECT Statistics.ID, Player.ID, FirstName, LastName, PlayTimeMin, PlayTimeSec, Point, Assist, Rebound
              FROM Statistics
              JOIN Player ON PlayerID = Player.ID
              ORDER BY LastName, FirstName";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($statid, $pid, $firstname, $lastname, $timemin, $timesec, $point, $assist, $rebound);

      $stmt->data_seek(0);
      echo "<div class=\"header\" style=\"display:table;\">Modify Statistic</div>
            <div class=\"container\">
            <table>
              <tr>
                <th>No.</th>
                <th>Player's First Name</th>
                <th>Player's Last Name</th>
                <th>Play Time (Minute:Second)</th>
                <th>Point(s)</th>
                <th>Assist(s)</th>
                <th>Rebound(s)</th>
                <th>Action</th>
              </tr>";
          while( $stmt->fetch() ) {
                $row = array('id'=>0, 'statid'=>0, 'pid'=>0, 'firstname'=>"",'lastname'=>"", 'timemin'=>0, 'timesec'=>0, 'point'=>0, 'assist'=>0, 'rebound'=>0);
                $row['id'] = $count++;
                $row['statid'] = $statid;
                $row['pid'] = $pid;
                $row['firstname'] = $firstname;
                $row['lastname'] = $lastname;
                $row['timemin'] = $timemin;
                $row['timesec'] = $timesec;
                $row['point'] = $point;
                $row['assist'] = $assist;
                $row['rebound'] = $rebound;
                echo "<tr>
                  <td>". $row['id'] ."</td>
                  <td>". $row['firstname'] ."</td>
                  <td>". $row['lastname'] ."</td>
                  <td>". str_pad($row['timemin'], 2, "0", STR_PAD_LEFT) .":". str_pad($row['timesec'], 2, "0", STR_PAD_LEFT) ."</td>
                  <td>". intval($row['point']) ."</td>
                  <td>". intval($row['assist']) ."</td>
                  <td>". intval($row['rebound']) ."</td>
                  <td><a href=\"changestatinfo.php?statid=".$row['statid']."&pid=".$row['pid']."&firstname=".$row['firstname']."&lastname="
                  .$row['lastname']."&timemin=".$row['timemin']."&timesec=".$row['timesec']."&point=".$row['point']
                  ."&assist=".$row['assist']."&rebound=".$row['rebound']."\">Change</a>
                </tr>";
          }
      echo "</table>
      </div>";
    }
    ?>

</body>
</html>
