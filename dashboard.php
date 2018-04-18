<?php
  session_start();
  if (empty($_SESSION["role"]) && (empty($_SESSION["authenticated"]))) {
    echo "<script type=\"text/javascript\"> alert(\"You must log in to the system first\")</script>";
    echo "<script>window.location = 'index.php';</script>"; // redirect to index.php (login page)
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
        <a class=\"active\" href=\"Dashboard.php\">Home</a>
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
          <button class=\"dropbtn\" onclick=\"window.location='profile.php'\">Profile &dArr;</button>
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
        <a class=\"active\" href=\"dashboard.php\">Home</a>
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
          <button class=\"dropbtn\" onclick=\"window.location='profile.php'\">Profile &dArr;</button>
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
        <a class=\"active\" href=\"Dashboard.php\">Home</a>
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
          <button class=\"dropbtn\" onclick=\"window.location='profile.php'\">Profile &dArr;</button>
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
        <a class=\"active\" href=\"Dashboard.php\">Home</a>
        <a href=\"player.php\">Player</a>
        <a href=\"statistics.php\">Statistics</a>


        <a style=\"float:right\" href=\"logout.php\">Logout</a>
        <div style=\"float:right\" class=\"dropdown\">
          <button class=\"dropbtn\" onclick=\"window.location='profile.php'\">Profile &dArr;</button>
          <div class=\"dropdown-content\">
            <a href=\"profile.php\">View Profile</a>
            <a href=\"changePassword.php\">Change Password</a>
          </div>
        </div>
        <a style=\"float:right\" href=\"about.php\">About</a>
      </div>";

    }
  ?>
<div class="header">
  Welcome, <?php echo $_SESSION["username"]?>!
</div>
<a href="player.php"><div class="container">
  <div class="smallboxleft">
    <h3>Player</h3>
    <hr/>
    <h6>
      All the player related is here.
    </h6>
  </div>
</a>
<a href="statistics.php">
  <div class="smallboxright">
    <h3>Statistics</h3>
    <hr/>
    <h6>
      All the statistics related is here.
    </h6>
  </div>
</a>

<a href="profile.php">
  <div class="smallboxleft">
    <h3>Profile</h3>
    <hr/>
    <h6>
      Change your password here.
    </h6>
  </div>
  <?php
  if ($_SESSION["role"] == "operator" || $_SESSION["role"] == "observer") {
    echo "<a href=\"about.php\">
    <div class=\"smallboxright\">
      <h3>About</h3>
      <hr/>
      <h6>
        Learn more about this website here.
      </h6>
    </div>";
  } else {
    echo "<a href=\"privilege.php\">
    <div class=\"smallboxright\">
      <h3>Privilege</h3>
      <hr/>
      <h6>
        View and change privilege of other roles here.
      </h6>
    </div>";
  }
  ?>
  </a>
</a>

</div>
</body>
</html>
