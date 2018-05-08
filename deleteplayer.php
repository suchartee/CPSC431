<?php
  session_start();
  if (empty($_SESSION["role"]) && (empty($_SESSION["authenticated"]))) {
    echo "<script type=\"text/javascript\"> alert(\"You must log in to the system first\")</script>";
    echo "<script>window.location = 'index.php';</script>"; // redirect to index.php (login page)
  }

  require_once "config.php";
  require_once "functions.php";
?>

<!DOCTYPE html>
<html>
<head>
  <title>Delete Player Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $count = 1;
    $db = configDB($_SESSION["role"]);
    $query = "SELECT Player.ID, FirstName, LastName, Team.ID, TeamName FROM Player JOIN Team ON TeamID = Team.ID ORDER BY TeamName, LastName";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($playerID, $firstname, $lastname, $teamid, $teamname);
    }
    $stmt->data_seek(0);
    echo "<div class=\"header\" style=\"display:table;\">Delete Player</div>
          <div class=\"container\">
          <table>
            <tr>
              <th>No.</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Team Name</th>
              <th>Action</th>
            </tr>";
            $row = array();
        while( $stmt->fetch() ) {
            $row = array('id'=>$count++, 'playerid'=>$playerID, 'firstname'=>$firstname, 'lastname'=>$lastname, 'teamname'=>$teamname);
              echo "<tr>
                <td>". $row['id'] ."</td>
                <td>". $row['firstname'] ."</td>
                <td>". $row['lastname'] ."</td>
                <td>". $row['teamname'] ."</td>
                <td><a href=\"deleteplayer.php?playerid=".$row['playerid']."\">Delete</a>
              </tr>";
        }
    echo "</table>
    </div>";
    ?>

    <?php
      if (isset($_GET["playerid"]) && !empty($_GET["playerid"])) {
        $playerID = $_GET["playerid"];
        $query = "DELETE FROM Player WHERE ID = ?";
        if ($stmt = $db->prepare($query)) {
          $stmt->bind_param("i", $playerID);
          $stmt->execute();
        }
        echo '<script type="text/javascript"> alert("You have successfully deleted this player!")</script>';
        echo "<script>window.location = 'deleteplayer.php';</script>";
      }
     ?>


</body>
</html>
