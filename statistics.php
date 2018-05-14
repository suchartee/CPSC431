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
  <title>Statistics Page</title>
  <link rel="stylesheet" href="style.css">
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script type="text/javascript">
      $(function () {
          $("#search").change(function () {
              if ($(this).val() == "searchByPlayerName" || $(this).val() == "searchByPlayerNameAvg") {
                  $("#criteria").show();
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
    $db = configDB($_SESSION["role"]);
    $query = "SELECT ID, FirstName, LastName FROM Player";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($playerid, $playerfirstname, $playerlastname);
      $stmt->data_seek(0);
    ?>
    <div class="header">
      <form action="statistics.php" method="post">
      <select name="searchbox" id="search" onchange="showfield(this.options[this.selectedIndex].value)">
        <option value="viewAllStatistics">View All Statistics</option>
        <option value="viewAllPlayer">View All Player</option>
        <option value="searchByPlayerName">Search By Player's Name</option>
        <option value="searchByPlayerNameAvg">Search By Player's Name (Average)</option>
      </select>
      <select name="playerid" id="criteria" style="display: none;"/>
        <option value="" disabled selected value>Select Player's Name</option>
        <?php
        while ($stmt->fetch()) {
            echo "<option value=".$playerid.">".ucwords($playerfirstname)." ".ucwords($playerlastname)."</option>";
        }
        ?>
      </select>
      <input type="submit" id="searchButton" name="searchbutton" value="Search"/>
    </form>
    </div>


    <?php
    // by default, all statistics is displayed
    if (empty($_POST["searchbutton"])) {
      $count = 1;
      $query = "SELECT PlayerID, FirstName, LastName, PlayTimeMin, PlayTimeSec, Point, Assist, Rebound
                FROM Statistics
                JOIN Player ON PlayerID = Player.ID
                ORDER BY LastName, FirstName";
      $db = configDB($_SESSION["role"]);
      if ($stmt = $db->prepare($query)) {
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($pid, $firstname, $lastname, $timemin, $timesec, $point, $assist, $rebound);

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
                </tr>";
            while( $stmt->fetch() ) {
                  $row = array('id'=>$count++, 'pid'=>$pid, 'firstname'=>$firstname,'lastname'=>$lastname,
                  'timemin'=>$timemin, 'timesec'=>$timesec, 'point'=>$point, 'assist'=>$assist, 'rebound'=>$rebound);
                  echo "<tr>
                    <td>". $row['id'] ."</td>
                    <td>". $row['firstname'] ."</td>
                    <td>". $row['lastname'] ."</td>
                    <td>". str_pad($row['timemin'], 2, "0", STR_PAD_LEFT) .":". str_pad($row['timesec'], 2, "0", STR_PAD_LEFT) ."</td>
                    <td>". intval($row['point']) ."</td>
                    <td>". intval($row['assist']) ."</td>
                    <td>". intval($row['rebound']) ."</td>
                  </tr>";
            }
        echo "</table>
        </div>";
        }
      } else {
          if (isset($_POST["searchbox"]) && !empty($_POST["searchbox"])) {
          // check what kind of selection is
          switch($_POST["searchbox"]) {
            case "searchByPlayerName":
              $query = "SELECT PlayerID, FirstName, LastName, PlayTimeMin, PlayTimeSec, Point, Assist, Rebound
                        FROM Statistics
                        JOIN Player ON PlayerID = Player.ID
                        WHERE PlayerID = ?";
            break;
            case "searchByPlayerNameAvg":
              $query = "SELECT PlayerID, FirstName, LastName, ROUND(AVG(PlayTimeMin), 0), ROUND(AVG(PlayTimeSec)), ROUND(AVG(Point)),
                        ROUND(AVG(Assist)), ROUND(AVG(Rebound))
                        FROM Statistics JOIN Player ON PlayerID = Player.ID WHERE PlayerID = ? GROUP BY PlayerID";
            break;
            case "viewAllPlayer":
              $query = "SELECT PlayerID, FirstName, LastName, PlayTimeMin, PlayTimeSec, Point, Assist, Rebound
                        FROM Statistics
                        RIGHT JOIN Player ON PlayerID = Player.ID";
            break;
            default:
              $query = "SELECT PlayerID, FirstName, LastName, PlayTimeMin, PlayTimeSec, Point, Assist, Rebound
                        FROM Statistics
                        JOIN Player ON PlayerID = Player.ID";
            break;
          }
          $count = 1;
          $db = configDB($_SESSION["role"]);
          if ($stmt = $db->prepare($query)) {
            // check SQL injection for textbox, if any
            if (isset($_POST["playerid"]) && !empty($_POST["playerid"])) {
              $searchtextbox = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST["playerid"]))));
              $stmt->bind_param("i", $searchtextbox);
            }
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($pid, $firstname, $lastname, $timemin, $timesec, $point, $assist, $rebound);
            $stmt->data_seek(0);
            echo "<div class=\"header\" style=\"display:table;\">All Matches</div>
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
                    </tr>";
                while( $stmt->fetch() ) {
                    $row = array('id'=>$count++, 'pid'=>$pid, 'firstname'=>$firstname,'lastname'=>$lastname,
                    'timemin'=>$timemin, 'timesec'=>$timesec, 'point'=>$point, 'assist'=>$assist, 'rebound'=>$rebound);
                    echo "<tr>
                    <td>". $row['id'] ."</td>
                    <td>". $row['firstname'] ."</td>
                    <td>". $row['lastname'] ."</td>
                    <td>". str_pad($row['timemin'], 2, "0", STR_PAD_LEFT) .":". str_pad($row['timesec'], 2, "0", STR_PAD_LEFT) ."</td>
                    <td>". intval($row['point']) ."</td>
                    <td>". intval($row['assist']) ."</td>
                    <td>". intval($row['rebound']) ."</td>
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
