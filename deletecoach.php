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
  <title>Delete Coach Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $count = 1;
    $db = configDB($_SESSION["role"]);
    $query = "SELECT Coach.ID, FirstName, LastName, Team.ID, TeamName FROM Coach JOIN Team ON TeamID = Team.ID ORDER BY TeamName, LastName";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($coachid, $firstname, $lastname, $teamid, $teamname);
    }
    $stmt->data_seek(0);
    echo "<div class=\"header\" style=\"display:table;\">Delete Coach</div>
          <div class=\"container\">
          <table>
            <tr>
              <th>No.</th>
              <th>Coach's First Name</th>
              <th>Coach's Last Name</th>
              <th>Coach's Team Name</th>
              <th>Action</th>
            </tr>";
            $row = array();
        while( $stmt->fetch() ) {
            $row = array('id'=>$count++, 'coachid'=>$coachid, 'firstname'=>$firstname, 'lastname'=>$lastname, 'teamname'=>$teamname);
              echo "<tr>
                <td>". $row['id'] ."</td>
                <td>". $row['firstname'] ."</td>
                <td>". $row['lastname'] ."</td>
                <td>". $row['teamname'] ."</td>
                <td><a href=\"deletecoach.php?coachid=".$row['coachid']."\">Delete</a>
              </tr>";
        }
    echo "</table>
    </div>";
    ?>

    <?php
      if (isset($_GET["coachid"]) && !empty($_GET["coachid"])) {
        $coachid = $_GET["coachid"];
        $query = "DELETE FROM Coach WHERE ID = ?";
        if ($stmt = $db->prepare($query)) {
          $stmt->bind_param("i", $coachid);
          $stmt->execute();
          echo '<script type="text/javascript"> alert("You have successfully deleted this coach!")</script>';
          echo "<script>window.location = 'deletecoach.php';</script>";
        } else {
          echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
          echo "<script>window.location = 'dashboard.php';</script>";
        }

      }
     ?>


</body>
</html>
