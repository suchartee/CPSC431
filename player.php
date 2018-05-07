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
  <title>Player Page</title>
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
  <?php
    include 'logged_navbar.php';
    ?>




  <div class="header">
    <form action="player.php" method="post">
    <select name="searchbox" id="search" onchange="showfield(this.options[this.selectedIndex].value)">
      <option value="viewAllPlayer">View All Player</option>
      <option value="searchByFirstName">Search By First Name</option>
      <option value="searchByLastName">Search By Last Name</option>
      <option value="searchByTeam">Search By Team Name</option>
    </select>
    <input type="text" name="textbox" id="criteria" style="display: none;" />
    <input type="submit" id="searchButton" value="Search"/>
  </form>
  </div>


  <?php
  // by default, all player is listed
  if (isset($_POST["searchbox"])) {
    // check SQL injection
    $searchtextbox = lcfirst(strip_tags(htmlspecialchars($_POST["textbox"]))) . "%"; // First letter uppercase and search anything that starts with the value in textbox
    switch($_POST["searchbox"]) {
      case "searchByFirstName":
        $query = "SELECT FirstName, LastName, TeamName FROM Player JOIN Team on teamID = Team.ID WHERE FirstName LIKE ? ORDER BY TeamName, LastName";
      break;
      case "searchByLastName":
        $query = "SELECT FirstName, LastName, TeamName FROM Player JOIN Team on teamID = Team.ID WHERE LastName LIKE ? ORDER BY TeamName, FirstName";
      break;
      case "searchByTeam":
        $query = "SELECT FirstName, LastName, TeamName FROM Player JOIN Team on teamID = Team.ID WHERE TeamName LIKE ? ORDER BY TeamName, LastName";
      break;
      default:
      break;
    }
    if (isset($_POST["textbox"]) && !empty($_POST["textbox"])) {
      $count = 1;
      $db = configDB($_SESSION["role"]);
      $stmt = $db->prepare($query);
      $stmt->bind_param("s", $searchtextbox);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($firstname, $lastname, $teamname);

      $stmt->data_seek(0);
      echo "<div class=\"header\" style=\"display:table;\">All Player</div>
            <div class=\"container\">
            <table>
              <tr>
                <th>No.</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Team Name</th>
              </tr>";
          while( $stmt->fetch() ) {
                echo "<tr>
                  <td>". $count++ ."</td>
                  <td>". $firstname ."</td>
                  <td>". $lastname ."</td>
                  <td>". $teamname ."</td>
                </tr>";
          }
      echo "</table>
      </div>";
    } else {
      $count = 1;
      $db = configDB($_SESSION["role"]);
      $query = "SELECT FirstName, LastName, TeamName FROM Player JOIN Team on teamID = Team.ID ORDER BY TeamName, LastName";
      $stmt = $db->prepare($query);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($firstname, $lastname, $teamname);

      $stmt->data_seek(0);
      echo "<div class=\"header\" style=\"display:table;\">All Player</div>
            <div class=\"container\">
            <table>
              <tr>
                <th>No.</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Team Name</th>
              </tr>";
          while( $stmt->fetch() ) {
                echo "<tr>
                  <td>". $count++ ."</td>
                  <td>". $firstname ."</td>
                  <td>". $lastname ."</td>
                  <td>". $teamname ."</td>
                </tr>";
          }
      echo "</table>
      </div>";
    }
  }
  ?>

</body>
</html>
