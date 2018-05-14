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
  <title>Modify Team Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $count = 1;
    $db = configDB($_SESSION["role"]);
    $query = "SELECT Team.ID, TeamName, Coach.ID, Coach.FirstName, Coach.LastName, WinCount, LostCount
              FROM Team LEFT JOIN Coach ON Coach.ID = CoachID ORDER BY LastName, FirstName";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($teamID, $teamname, $coachID, $coachfirstname, $coachlastname, $wincount, $lostcount);
    }
    $stmt->data_seek(0);
    echo "<div class=\"header\" style=\"display:table;\">Modify Team</div>
          <div class=\"container\">
          <table>
            <tr>
              <th>No.</th>
              <th>Team Name</th>
              <th>Coach's First Name</th>
              <th>Coach's Last Name</th>
              <th>Win Game Count</th>
              <th>Lost Game Count</th>
              <th>Action</th>
            </tr>";

        while( $stmt->fetch() ) {
          $row = array('id'=>0, 'teamid'=>0, 'teamname'=>"", 'coachid'=>0, 'coachfirstname'=>"", 'coachlastname'=>"", 'wincount'=>0,'lostcount'=>0);
          $row['id'] = $count++;
          $row['teamid'] = $teamID;
          $row['teamname'] = $teamname;
          $row['coachid'] = $coachID;
          $row['coachfirstname'] = $coachfirstname;
          $row['coachlastname'] = $coachlastname;
          $row['wincount'] = $wincount;
          $row['lostcount'] = $lostcount;
          echo "<tr>

            <td>". $row['id'] ."</td>
            <td>". $row['teamname'] ."</td>
            <td>". $row['coachfirstname'] ."</td>
            <td>". $row['coachlastname'] ."</td>
            <td>". $row['wincount'] ."</td>
            <td>". $row['lostcount'] ."</td>
            <td><a href=\"changeteaminfo.php?teamid=".$row['teamid']."&teamname=".$row['teamname']."&coachid="
            .$row['coachid']."&coachfirstname=".$row['coachfirstname']."&coachlastname=".$row['coachlastname']."&wincount="
            .$row['wincount']."&lostcount=".$row['lostcount']."\">Change</a>
          </tr>";
        }
    echo "</table>
    </div>";
    ?>


</body>
</html>
