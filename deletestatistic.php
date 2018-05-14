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
  <title>Delete Statistic Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $count = 1;
    $db = configDB($_SESSION["role"]);
    $query = "SELECT Statistics.ID, FirstName, LastName, PlayTimeMin, PlayTimeSec, Point, Assist, Rebound
              FROM Statistics
              JOIN Player ON PlayerID = Player.ID
              ORDER BY LastName, FirstName";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($statid, $firstname, $lastname, $timemin, $timesec, $point, $assist, $rebound);
      $stmt->data_seek(0);
      echo "<div class=\"header\" style=\"display:table;\">All Statistics</div>
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
                $row = array('id'=>$count++, 'statid'=>$statid, 'firstname'=>$firstname,'lastname'=>$lastname,
                'timemin'=>$timemin, 'timesec'=>$timesec, 'point'=>$point, 'assist'=>$assist, 'rebound'=>$rebound);
                echo "<tr>
                  <td>". $row['id'] ."</td>
                  <td>". $row['firstname'] ."</td>
                  <td>". $row['lastname'] ."</td>
                  <td>". str_pad($row['timemin'], 2, "0", STR_PAD_LEFT) .":". str_pad($row['timesec'], 2, "0", STR_PAD_LEFT) ."</td>
                  <td>". intval($row['point']) ."</td>
                  <td>". intval($row['assist']) ."</td>
                  <td>". intval($row['rebound']) ."</td>
                  <td><a href=\"deletestatistic.php?statid=".$row['statid']."\">Delete</a>
                </tr>";
          }
      echo "</table>
      </div>";
      }
    ?>

    <?php
      if (isset($_GET["statid"]) && !empty($_GET["statid"])) {
        $statid = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["statid"]))));
        $query = "DELETE FROM Statistics WHERE ID = ?";
        if ($stmt = $db->prepare($query)) {
          $stmt->bind_param("i", $statid);
          $stmt->execute();
          echo '<script type="text/javascript"> alert("You have successfully deleted this statistic record!")</script>';
          echo "<script>window.location = 'deletestatistic.php';</script>";
        } else {
          echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
          echo "<script>window.location = 'dashboard.php';</script>";
        }

      }
     ?>


</body>
</html>
