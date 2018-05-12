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
  <title>Modify Coach Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $count = 1;
    $db = configDB($_SESSION["role"]);
    $query = "SELECT Coach.ID, FirstName, LastName, Team.ID, Team.TeamName FROM Coach LEFT JOIN Team ON TeamID = Team.ID ORDER BY LastName, FirstName";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($coachid, $firstname, $lastname, $teamid, $teamname);
      $stmt->data_seek(0);
      echo "<div class=\"header\" style=\"display:table;\">Modify Coach</div>
            <div class=\"container\">
            <table>
              <tr>
                <th>No.</th>
                <th>Coach's First Name</th>
                <th>Coach's Last Name</th>
                <th>Team Name</th>
                <th>Action</th>
              </tr>";

          while( $stmt->fetch() ) {
            $row = array('id'=>$count++, 'coachid'=>$coachid, 'firstname'=>$firstname, 'lastname'=>$lastname, 'teamid'=>$teamid, 'teamname'=>$teamname);
            echo "<tr>
              <td>". $row['id'] ."</td>
              <td>". $row['firstname'] ."</td>
              <td>". $row['lastname'] ."</td>
              <td>". $row['teamname'] ."</td>
              <td><a href=\"changecoachinfo.php?coachid=".$row['coachid']."&coachfirstname=".$row['firstname']."&coachlastname=".$row['lastname']."&teamid=".$row['teamid']."&teamname=".$row['teamname']."\">Change</a>
            </tr>";
          }
      echo "</table>
      </div>";
    }

    ?>


</body>
</html>
