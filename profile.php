<?php
  session_start();
  if (empty($_SESSION)) {
    echo "<script>window.location = history.back();</script>"; // go back to the previous page
  }

 ?>
<!DOCTYPE html>
<html>
<head>
  <title>Welcome to Basketball Roster Project</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    // check role
    switch($_SESSION["role"]) {
      case "operator" :
      echo "<div class=\"navmenu\">
        <a href=\"Dashboard.php\">Home</a>
        <div style=\"float:left\" class=\"dropdown\">
          <button class=\"dropbtn\" onclick=\"window.location='player.php'\">Player &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"#.php\">View Player</a>
            <a href=\"#.php\">Add Player</a>
            <a href=\"#.php\">Edit Player</a>
          </div>
        </div>
        <div style=\"float:left\" class=\"dropdown\">
          <button class=\"dropbtn\" onclick=\"window.location='statistics.php'\">Statistics &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"#.php\">View Statistics</a>
            <a href=\"#.php\">Add Statistics</a>
            <a href=\"#.php\">Edit Statistics</a>
          </div>
        </div>

        <a style=\"float:right\" href=\"logout.php\">Logout</a>
        <div style=\"float:right\" class=\"dropdown\">
          <button class=\"dropbtn-active\" onclick=\"window.location='profile.php'\">Profile &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"profile.php\">View Profile</a>
            <a href=\"changePassword.php\">Change Password</a>
          </div>
        </div>
        <a style=\"float:right\" href=\"about.php\">About</a>
      </div>";

      break;
      case "manager" :
      echo "<div class=\"navmenu\">
        <a href=\"dashboard.php\">Home</a>
        <div style=\"float:left\" class=\"dropdown\">
          <button class=\"dropbtn\" onclick=\"window.location='player.php'\">Player &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"#.php\">View Player</a>
            <a href=\"#.php\">Add Player</a>
            <a href=\"#.php\">Edit Player</a>
            <a href=\"#.php\">Delete Statistics</a>
          </div>
        </div>
        <div style=\"float:left\" class=\"dropdown\">
          <button class=\"dropbtn\" onclick=\"window.location='statistics.php'\">Statistics &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"#.php\">View Statistics</a>
            <a href=\"#.php\">Add Statistics</a>
            <a href=\"#.php\">Edit Statistics</a>
            <a href=\"#.php\">Delete Statistics</a>
          </div>
        </div>
        <div style=\"float:left\" class=\"dropdown\">
          <button class=\"dropbtn\" onclick=\"window.location='privilege.php'\">Privilege &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"#.php\">View Privilege</a>
            <a href=\"#.php\">Change Role\'s Privilege</a>
          </div>
        </div>


        <a style=\"float:right\" href=\"logout.php\">Logout</a>
        <div style=\"float:right\" class=\"dropdown\">
          <button class=\"dropbtn-active\" onclick=\"window.location='profile.php'\">Profile &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"profile.php\">View Profile</a>
            <a href=\"changePassword.php\">Change Password</a>
          </div>
        </div>
        <a style=\"float:right\" href=\"about.php\">About</a>
      </div>";

      break;
      case "admin" :
      echo "<div class=\"navmenu\">
        <a href=\"Dashboard.php\">Home</a>
        <div style=\"float:left\" class=\"dropdown\">
          <button class=\"dropbtn\" onclick=\"window.location='player.php'\">Player &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"#.php\">View Player</a>
            <a href=\"#.php\">Add Player</a>
            <a href=\"#.php\">Edit Player</a>
            <a href=\"#.php\">Delete Statistics</a>
          </div>
        </div>
        <div style=\"float:left\" class=\"dropdown\">
          <button class=\"dropbtn\" onclick=\"window.location='statistics.php'\">Statistics &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"#.php\">View Statistics</a>
            <a href=\"#.php\">Add Statistics</a>
            <a href=\"#.php\">Edit Statistics</a>
            <a href=\"#.php\">Delete Statistics</a>
          </div>
        </div>
        <div style=\"float:left\" class=\"dropdown\">
          <button class=\"dropbtn\" onclick=\"window.location='privilege.php'\">Privilege &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"#.php\">View Privilege</a>
            <a href=\"#.php\">Change Role\'s Privilege</a>
            <a href=\"#.php\">Delete Role\'s Privilege</a>
          </div>
        </div>


        <a style=\"float:right\" href=\"logout.php\">Logout</a>
        <div style=\"float:right\" class=\"dropdown\">
          <button class=\"dropbtn-active\" onclick=\"window.location='profile.php'\">Profile &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"profile.php\">View Profile</a>
            <a href=\"changePassword.php\">Change Password</a>
          </div>
        </div>
        <a style=\"float:right\" href=\"about.php\">About</a>
      </div>";

      break;
      default :
      echo "<div class=\"navmenu\">
        <a href=\"Dashboard.php\">Home</a>
        <a href=\"player.php\">Player</a>
        <a href=\"statistics.php\">Statistics</a>


        <a style=\"float:right\" href=\"logout.php\">Logout</a>
        <div style=\"float:right\" class=\"dropdown\">
          <button class=\"dropbtn-active\" onclick=\"window.location='profile.php'\">Profile &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"profile.php\">View Profile</a>
            <a href=\"changePassword.php\">Change Password</a>
          </div>
        </div>
        <a style=\"float:right\" href=\"about.php\">About</a>
      </div>";

    }
  ?>

View Profile
Username :
Email :
Role :

<button>Change password</button>

</body>
</html>