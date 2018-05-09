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
  <title>Team Page</title>
  <link rel="stylesheet" href="style.css">
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script type="text/javascript">
      $(function () {
          $("#search").change(function () {
              if ($(this).val() == "searchByTeamName" || $(this).val() == "searchByCoachFirstName" || $(this).val() == "searchByCoachLastName") {
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
      <form action="team.php" method="post">
      <select name="searchbox" id="search" onchange="showfield(this.options[this.selectedIndex].value)">
        <option value="viewAllTeam">View All Team</option>
        <option value="searchByTeamName">Search By Team Name</option>
        <option value="searchByCoachFirstName">Search By Coach First Name</option>
        <option value="searchByCoachLastName">Search By Coach Last Name</option>
        <option value="searchByMostWin">Search By Most Game Win</option>
        <option value="searchByLeastWin">Search By Least Game Win</option>
        <option value="searchByMostLost">Search By Most Game Lost</option>
        <option value="searchByLeastLost">Search By Least Game Lost</option>
      </select>
      <input type="text" name="textbox" id="criteria" style="display: none;" />
      <input type="submit" id="searchButton" name="searchbutton" value="Search"/>
    </form>
    </div>


    <?php
    // by default, all team is displayed
    if (empty($_POST["searchbutton"])) {
      $count = 1;
      $query = "SELECT TeamName, WinCount, lostCount, Coach.FirstName, Coach.LastName FROM Team JOIN Coach ON CoachID = Coach.ID ORDER BY TeamName";
      $db = configDB($_SESSION["role"]);
      if ($stmt = $db->prepare($query)) {
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($teamname, $wincount, $lostcount, $coachfirstname, $coachlastname);

        $stmt->data_seek(0);
        echo "<div class=\"header\" style=\"display:table;\">All Team</div>
              <div class=\"container\">
              <table>
                <tr>
                  <th>No.</th>
                  <th>Team Name</th>
                  <th>Coach First Name</th>
                  <th>Coach Last Name</th>
                  <th>Win Count</th>
                  <th>Lost Count</th>
                </tr>";
            while( $stmt->fetch() ) {
                  $row = array('id'=>$count++, 'teamname'=>$teamname, 'wincount'=>$wincount,'lostcount'=>$lostcount,'coachfirstname'=>$coachfirstname,'coachlastname'=>$coachlastname);
                  echo "<tr>
                    <td>". $row['id'] ."</td>
                    <td>". $row['teamname'] ."</td>
                    <td>". $row['coachfirstname'] ."</td>
                    <td>". $row['coachlastname'] ."</td>
                    <td>". $row['wincount'] ."</td>
                    <td>". $row['lostcount'] ."</td>
                  </tr>";
            }
        echo "</table>
        </div>";
      }
    } else {
        if (isset($_POST["searchbox"]) && !empty($_POST["searchbox"])) {
        // check what kind of selection is
        switch($_POST["searchbox"]) {
          case "searchByTeamName":
            $query = "SELECT TeamName, WinCount, lostCount, Coach.FirstName, Coach.LastName FROM Team JOIN Coach ON CoachID = Coach.ID WHERE TeamName LIKE ? ORDER BY TeamName";
          break;
          case "searchByCoachFirstName":
            $query = "SELECT TeamName, WinCount, lostCount, Coach.FirstName, Coach.LastName FROM Team JOIN Coach ON CoachID = Coach.ID WHERE Coach.FirstName LIKE ? ORDER BY TeamName";
          break;
          case "searchByCoachLastName":
            $query = "SELECT TeamName, WinCount, lostCount, Coach.FirstName, Coach.LastName FROM Team JOIN Coach ON CoachID = Coach.ID WHERE Coach.LastName LIKE ? ORDER BY TeamName";
          break;
          case "searchByMostWin":
            $query = "SELECT TeamName, WinCount, lostCount, Coach.FirstName, Coach.LastName FROM Team JOIN Coach ON Coach.ID = CoachID WHERE WinCount = (SELECT MAX(WinCount) FROM Team)";
          break;
          case "searchByLeastWin":
            $query = "SELECT TeamName, WinCount, lostCount, Coach.FirstName, Coach.LastName FROM Team JOIN Coach ON Coach.ID = CoachID WHERE WinCount = (SELECT MIN(WinCount) FROM Team)";
          break;
          case "searchByMostLost":
            $query = "SELECT TeamName, WinCount, lostCount, Coach.FirstName, Coach.LastName FROM Team JOIN Coach ON Coach.ID = CoachID WHERE lostCount = (SELECT Max(lostCount) FROM Team)";
          break;
          case "searchByLeastLost":
            $query = "SELECT TeamName, WinCount, lostCount, Coach.FirstName, Coach.LastName FROM Team JOIN Coach ON Coach.ID = CoachID WHERE lostCount = (SELECT MIN(WinCount) FROM Team)";
          break;
          default:
            $query = "SELECT TeamName, WinCount, lostCount, Coach.FirstName, Coach.LastName FROM Team JOIN Coach ON CoachID = Coach.ID ORDER BY TeamName";
          break;
        }
        $count = 1;
        $db = configDB($_SESSION["role"]);
        if ($stmt = $db->prepare($query)) {
          // check SQL injection for textbox, if any
          if (isset($_POST["textbox"]) && !empty($_POST["textbox"])) {
            $searchtextbox = lcfirst(strip_tags(htmlspecialchars($_POST["textbox"]))) . "%"; // First letter uppercase and search anything that starts with the value in textbox
            $stmt->bind_param("s", $searchtextbox);
          }
          $stmt->execute();
          $stmt->store_result();
          $stmt->bind_result($teamname, $wincount, $lostcount, $coachfirstname, $coachlastname);

          $stmt->data_seek(0);
          echo "<div class=\"header\" style=\"display:table;\">All Team</div>
                <div class=\"container\">
                <table>
                  <tr>
                    <th>No.</th>
                    <th>Team Name</th>
                    <th>Coach First Name</th>
                    <th>Coach Last Name</th>
                    <th>Win Count</th>
                    <th>Lost Count</th>
                  </tr>";
              while( $stmt->fetch() ) {
                $row = array('id'=>$count++, 'teamname'=>$teamname, 'wincount'=>$wincount,'lostcount'=>$lostcount,'coachfirstname'=>$coachfirstname,'coachlastname'=>$coachlastname);
                echo "<tr>
                  <td>". $row['id'] ."</td>
                  <td>". $row['teamname'] ."</td>
                  <td>". $row['coachfirstname'] ."</td>
                  <td>". $row['coachlastname'] ."</td>
                  <td>". $row['wincount'] ."</td>
                  <td>". $row['lostcount'] ."</td>
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
