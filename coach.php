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
  <title>Coach Page</title>
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
      <form action="coach.php" method="post">
      <select name="searchbox" id="search" onchange="showfield(this.options[this.selectedIndex].value)">
        <option value="viewAllCoach">View All Coach</option>
        <option value="searchByFirstName">Search By First Name</option>
        <option value="searchByLastName">Search By Last Name</option>
        <option value="searchByTeam">Search By Team Name</option>
      </select>
      <input type="text" name="textbox" id="criteria" style="display: none;" />
      <input type="submit" id="searchButton" name="searchbutton" value="Search"/>
    </form>
    </div>


    <?php
    // by default, all coach is displayed
    if (empty($_POST["searchbutton"])) {
      $count = 1;
      $query = "SELECT FirstName, LastName, TeamName FROM Coach JOIN Team on teamID = Team.ID ORDER BY LastName, FirstName";
      $db = configDB($_SESSION["role"]);
      if ($stmt = $db->prepare($query)) {
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($firstname, $lastname, $teamname);

        $stmt->data_seek(0);
        echo "<div class=\"header\" style=\"display:table;\">All Coach</div>
              <div class=\"container\">
              <table>
                <tr>
                  <th>No.</th>
                  <th>Coach's First Name</th>
                  <th>Coach's Last Name</th>
                  <th>Team Name</th>
                </tr>";
            while( $stmt->fetch() ) {
                  $row = array('id'=>$count++, 'firstname'=>$firstname, 'lastname'=>$lastname,'teamname'=>$teamname);
                  echo "<tr>
                    <td>". $row['id'] ."</td>
                    <td>". $row['firstname'] ."</td>
                    <td>". $row['lastname'] ."</td>
                    <td>". $row['teamname'] ."</td>
                  </tr>";
            }
      echo "</table>
      </div>";
      }
    } else {
        if (isset($_POST["searchbox"]) && !empty($_POST["searchbox"])) {
        // check what kind of selection is
        switch($_POST["searchbox"]) {
          case "searchByFirstName":
            $query = "SELECT FirstName, LastName, TeamName FROM Coach JOIN Team on teamID = Team.ID WHERE FirstName LIKE ? ORDER BY LastName, FirstName";
          break;
          case "searchByLastName":
            $query = "SELECT FirstName, LastName, TeamName FROM Coach JOIN Team on teamID = Team.ID WHERE LastName LIKE ? ORDER BY LastName, FirstName";
          break;
          case "searchByTeam":
            $query = "SELECT FirstName, LastName, TeamName FROM Coach JOIN Team on teamID = Team.ID WHERE TeamName LIKE ? ORDER BY TeamName, LastName";
          break;
          default:
            $query = "SELECT FirstName, LastName, TeamName FROM Coach JOIN Team on teamID = Team.ID ORDER BY LastName, FirstName";
          break;
        }
        $count = 1;
        $db = configDB($_SESSION["role"]);
        if ($stmt = $db->prepare($query)) {
          // check SQL injection for textbox, if any
          if (isset($_POST["textbox"]) && !empty($_POST["textbox"])) {
            $searchtextbox = lcfirst(trim(strip_tags(htmlspecialchars(htmlentities($_POST["textbox"]))))) . "%"; // First letter uppercase and search anything that starts with the value in textbox
            $stmt->bind_param("s", $searchtextbox);
          }
          $stmt->execute();
          $stmt->store_result();
          $stmt->bind_result($firstname, $lastname, $teamname);

          $stmt->data_seek(0);
          echo "<div class=\"header\" style=\"display:table;\">All Coach</div>
                <div class=\"container\">
                <table>
                  <tr>
                    <th>No.</th>
                    <th>Coach's First Name</th>
                    <th>Coach's Last Name</th>
                    <th>Team Name</th>
                  </tr>";
              while( $stmt->fetch() ) {
                $row = array('id'=>0, 'firstname'=>"", 'lastname'=>"", 'teamname'=>"");
                $row['id'] = $count++;
                $row['firstname'] = $firstname;
                $row['lastname'] = $lastname;
                $row['teamname'] = $teamname;
                echo "<tr>
                  <td>". $row['id'] ."</td>
                  <td>". $row['firstname'] ."</td>
                  <td>". $row['lastname'] ."</td>
                  <td>". $row['teamname'] ."</td>
                </tr>";
              }
          echo "</table>
          </div>";
          }
        }
      }
    ?>

</body>
</html>
