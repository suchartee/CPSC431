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
  <title>Delete Team Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $count = 1;
    $db = configDB($_SESSION["role"]);
    $query = "SELECT ID, TeamName FROM Team ORDER BY TeamName";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($teamID, $teamname);
    }
    $stmt->data_seek(0);
    echo "<div class=\"header\" style=\"display:table;\">Delete Player</div>
          <div class=\"container\">
          <table>
            <tr>
              <th>No.</th>
              <th>Team Name</th>
              <th>Action</th>
            </tr>";
            $row = array();
        while( $stmt->fetch() ) {
            $row = array('id'=>$count++, 'teamid'=>$teamID, 'teamname'=>$teamname);
              echo "<tr>
                <td>". $row['id'] ."</td>
                <td>". $row['teamname'] ."</td>
                <td><a href=\"deleteteam.php?teamid=".$row['teamid']."\">Delete</a>
              </tr>";
        }
    echo "</table>
    </div>";
    ?>

    <?php
      if (isset($_GET["teamid"]) && !empty($_GET["teamid"])) {
        $teamID = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["teamid"]))));
        $query = "DELETE FROM Team WHERE ID = ?";
        if ($stmt = $db->prepare($query)) {
          $stmt->bind_param("i", $teamID);
          $stmt->execute();
          echo '<script type="text/javascript"> alert("You have successfully deleted this player!")</script>';
          echo "<script>window.location = 'deleteplayer.php';</script>";
        } else {
          echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
          echo "<script>window.location = 'dashboard.php';</script>";
        }
      }
     ?>


</body>
</html>
