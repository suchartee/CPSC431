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
  <title>Matches Page</title>
  <link rel="stylesheet" href="style.css">
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script type="text/javascript">
      $(function () {
          $("#search").change(function () {
              if ($(this).val() == "searchByTeamName") {
                  $("#criteria").show();
                  $("#criteria2").hide();
                  $("#datepicker").hide();
              } else if ($(this).val() == "searchByPlayerName") {
                  $("#criteria").hide();
                  $("#criteria2").show();
                  $("#datepicker").hide();
              } else if ($(this).val() == "searchByDatePlayed") {
                  $("#datepicker").show();
                  $("#criteria2").hide();
                  $("#criteria").hide();
              } else {
                  $("#datepicker").hide();
                  $("#criteria2").hide();
                  $("#criteria").hide();
              }
          });
      });
  </script>
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $db = configDB($_SESSION["role"]);
    $query = "SELECT ID, FirstName, LastName FROM Player";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($playerid, $playerfirstname, $playerlastname);
      $stmt->data_seek(0);
    ?>
    <div class="header">
      <form action="match.php" method="post">
      <select name="searchbox" id="search" onchange="showfield(this.options[this.selectedIndex].value)">
        <option value="viewAllMatches">View All Matches</option>
        <option value="searchByTeamName">Search By Team Name</option>
        <option value="searchByDatePlayed">Search By Date Played</option>
        <option value="searchByPlayerName">Search By Player's Name</option>
      </select>
      <input type="text" name="textbox" id="criteria" style="display: none;" />
      <select name="playerid" id="criteria2" style="display: none;"/>
        <option value="" disabled selected value>Select Player's Name</option>
        <?php
        while ($stmt->fetch()) {
            echo "<option value=".$playerid.">".ucwords($playerfirstname)." ".ucwords($playerlastname)."</option>";
        }
        ?>
      </select>
      <input type="date" name="datepicker" id="datepicker" style="display: none;" />
      <input type="submit" id="searchButton" name="searchbutton" value="Search"/>
    </form>
    </div>


    <?php
    // by default, all matches is displayed
    if (empty($_POST["searchbutton"])) {
      $count = 1;
      $query = "SELECT
                (SELECT TeamName FROM Team WHERE ID = HomeTeamID),
                (SELECT TeamName FROM Team WHERE ID = AwayTeamID),
                HomeScore, AwayScore, DatePlayed,
                (SELECT TeamName FROM Team WHERE ID = WinTeamID),
                (SELECT TeamName FROM Team WHERE ID = LostTeamID)
                FROM Matches
                ORDER BY DatePlayed";
      $db = configDB($_SESSION["role"]);
      if ($stmt = $db->prepare($query)) {
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hometeam, $awayteam, $homescore, $awayscore, $dateplayed, $winteam, $lostteam);

        $stmt->data_seek(0);
        echo "<div class=\"header\" style=\"display:table;\">All Matches</div>
              <div class=\"container\">
              <table>
                <tr>
                  <th>No.</th>
                  <th>Date Played</th>
                  <th>Home Team</th>
                  <th>Away Team</th>
                  <th>Home Team Score</th>
                  <th>Away Team Score</th>
                  <th>Win Team</th>
                  <th>Lost Team</th>
                </tr>";
            while( $stmt->fetch() ) {
                  $row = array('id'=>$count++, 'dateplayed'=>$dateplayed, 'hometeam'=>$hometeam,'awayteam'=>$awayteam,
                  'homescore'=>$homescore, 'awayscore'=>$awayscore, 'winteam'=>$winteam, 'lostteam'=>$lostteam);
                  echo "<tr>
                    <td>". $row['id'] ."</td>
                    <td>". $row['dateplayed'] ."</td>
                    <td>". $row['hometeam'] ."</td>
                    <td>". $row['awayteam'] ."</td>
                    <td>". $row['homescore'] ."</td>
                    <td>". $row['awayscore'] ."</td>
                    <td>". $row['winteam'] ."</td>
                    <td>". $row['lostteam'] ."</td>
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
              $query = "SELECT *
                        FROM
        	             (SELECT
             	            (SELECT TeamName FROM Team WHERE Team.ID = Matches.HomeTeamID) AS HomeTeam,
             	            (SELECT TeamName FROM Team WHERE team.ID = Matches.AwayTeamID) AS AwayTeam,
             	            HomeScore, AwayScore, DatePlayed,
             	            (SELECT TeamName FROM Team WHERE Team.ID = Matches.WinTeamID) AS WinTeam,
             	            (SELECT TeamName FROM Team WHERE Team.ID = Matches.LostTeamID) AS LostTeam
                        FROM Matches) AS InnerTable
                        WHERE HomeTeam LIKE ? || AwayTeam LIKE ?
                        ORDER BY DatePlayed";
            break;
            case "searchByDatePlayed":
              $query = "SELECT *
                        FROM
        	             (SELECT
             	            (SELECT TeamName FROM Team WHERE Team.ID = Matches.HomeTeamID) AS HomeTeam,
             	            (SELECT TeamName FROM Team WHERE team.ID = Matches.AwayTeamID) AS AwayTeam,
             	            HomeScore, AwayScore, DatePlayed,
             	            (SELECT TeamName FROM Team WHERE Team.ID = Matches.WinTeamID) AS WinTeam,
             	            (SELECT TeamName FROM Team WHERE Team.ID = Matches.LostTeamID) AS LostTeam
                        FROM Matches) AS InnerTable
                        WHERE DatePlayed = ?";
            break;
            case "searchByPlayerName":
              $query = "SELECT *
                        FROM
                        (SELECT
                            (SELECT TeamName FROM Team WHERE Team.ID = Matches.HomeTeamID) AS HomeTeam,
                            (SELECT TeamName FROM Team WHERE team.ID = Matches.AwayTeamID) AS AwayTeam,
                            HomeScore, AwayScore, DatePlayed,
                            (SELECT TeamName FROM Team WHERE Team.ID = Matches.WinTeamID) AS WinTeam,
                            (SELECT TeamName FROM Team WHERE Team.ID = Matches.LostTeamID) AS LostTeam
                        FROM Matches) AS InnerTable
                        WHERE HomeTeam =
                            (SELECT Team.TeamName
                            FROM Team JOIN Player ON TeamID = Team.ID
                            JOIN Matches ON TeamID = HomeTeamID WHERE Player.ID = ?) OR
                            AwayTeam = (SELECT Team.TeamName
                            FROM Team JOIN Player ON TeamID = Team.ID
                            JOIN Matches ON TeamID = AwayTeamID WHERE Player.ID = ?)
                        ORDER BY DatePlayed";
            break;
            default:
              $query = "SELECT
                        (SELECT TeamName FROM Team WHERE ID = HomeTeamID),
                        (SELECT TeamName FROM Team WHERE ID = AwayTeamID),
                        HomeScore, AwayScore, DatePlayed,
                        (SELECT TeamName FROM Team WHERE ID = WinTeamID),
                        (SELECT TeamName FROM Team WHERE ID = LostTeamID)
                        FROM Matches ORDER BY DatePlayed";
            break;
          }
          $count = 1;
          $db = configDB($_SESSION["role"]);
          if ($stmt = $db->prepare($query)) {
            // check SQL injection for textbox, if any
            if (isset($_POST["textbox"]) && !empty($_POST["textbox"])) {
              $searchtextbox = lcfirst(trim(strip_tags(htmlspecialchars(htmlentities($_POST["textbox"]))))) . "%"; // First letter uppercase and search anything that starts with the value in textbox
              $stmt->bind_param("ss", $searchtextbox, $searchtextbox);
            } else if (isset($_POST["playerid"]) && !empty($_POST["playerid"])) {
              $searchtextbox = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["playerid"]))));
              $stmt->bind_param("ii", $searchtextbox, $searchtextbox);
            } else if (isset($_POST["datepicker"]) && !empty($_POST["datepicker"])) {
              $date = date("Y-m-d", strtotime($_POST["datepicker"]));
              $stmt->bind_param("s", $date);
            }
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($hometeam, $awayteam, $homescore, $awayscore, $dateplayed, $winteam, $lostteam);
            $stmt->data_seek(0);
            echo "<div class=\"header\" style=\"display:table;\">All Matches</div>
                  <div class=\"container\">
                  <table>
                    <tr>
                      <th>No.</th>
                      <th>Date Played</th>
                      <th>Home Team</th>
                      <th>Away Team</th>
                      <th>Home Team Score</th>
                      <th>Away Team Score</th>
                      <th>Win Team</th>
                      <th>Lost Team</th>
                    </tr>";
                while( $stmt->fetch() ) {
                      $row = array('id'=>$count++, 'dateplayed'=>$dateplayed, 'hometeam'=>$hometeam,'awayteam'=>$awayteam,
                      'homescore'=>$homescore, 'awayscore'=>$awayscore, 'winteam'=>$winteam, 'lostteam'=>$lostteam);
                      echo "<tr>
                        <td>". $row['id'] ."</td>
                        <td>". $row['dateplayed'] ."</td>
                        <td>". $row['hometeam'] ."</td>
                        <td>". $row['awayteam'] ."</td>
                        <td>". $row['homescore'] ."</td>
                        <td>". $row['awayscore'] ."</td>
                        <td>". $row['winteam'] ."</td>
                        <td>". $row['lostteam'] ."</td>
                      </tr>";
                }
            echo "</table>
            </div>";
            }
          }
        }
      }
      ?>

</body>
</html>
