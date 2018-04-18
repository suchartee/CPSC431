<!-- this page is just for testing the navbar, will delete after everything is done-->

<html>
<head>
  <title>Welcome to Basketball Roster Project</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
Observer will see this nav menu:
<div class="navmenu">
  <a class="active" href="Dashboard.php">Home</a>
  <a href="player.php">Player</a>
  <a href="statistics.php">Statistics</a>


  <a style="float:right" href="logout.php">Logout</a>
  <div style="float:right" class="dropdown">
    <button class="dropbtn" onclick="window.location='profile.php'">Profile &dArr;</button>
    <div class="dropdown-content">
      <a href="profile.php">View Profile</a>
      <a href="changePassword.php">Change Password</a>
    </div>
  </div>
  <a style="float:right" href="about.php">About</a>
</div>
<!--
// this version is with escape string
 "<div class=\"navmenu\">
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
</div>"
-->

Operator will see this nav menu:
<div class="navmenu">
  <a class="active" href="Dashboard.php">Home</a>
  <div style="float:left" class="dropdown">
    <button class="dropbtn" onclick=\"window.location='player.php'\">Player &dArr;</button>
    <div class="dropdown-content">
      <a href="#.php">View Player</a>
      <a href="#.php">Add Player</a>
      <a href="#.php">Edit Player</a>
    </div>
  </div>
  <div style="float:left" class="dropdown">
    <button class="dropbtn" onclick=\"window.location='statistics.php'\">Statistics &dArr;</button>
    <div class="dropdown-content">
      <a href="#.php">View Statistics</a>
      <a href="#.php">Add Statistics</a>
      <a href="#.php">Edit Statistics</a>
    </div>
  </div>


  <a style="float:right" href="logout.php">Logout</a>
  <div style="float:right" class="dropdown">
    <button class="dropbtn" onclick=\"window.location='profile.php'\">Profile &dArr;</button>
    <div class="dropdown-content">
      <a href="profile.php">View Profile</a>
      <a href="changePassword.php">Change Password</a>
    </div>
  </div>
  <a style="float:right" href="about.php">About</a>
</div>

<!--
// this version is with escape string
"<div class=\"navmenu\">
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
</div>"
-->
Manager will see this nav menu:
<div class="navmenu">
  <a class="active" href="Dashboard.php">Home</a>
  <div style="float:left" class="dropdown">
    <button class="dropbtn" onclick=\"window.location='player.php'\">Player &dArr;</button>
    <div class="dropdown-content">
      <a href="#.php">View Player</a>
      <a href="#.php">Add Player</a>
      <a href="#.php">Edit Player</a>
      <a href="#.php">Delete Statistics</a>
    </div>
  </div>
  <div style="float:left" class="dropdown">
    <button class="dropbtn" onclick=\"window.location='statistics.php'\">Statistics &dArr;</button>
    <div class="dropdown-content">
      <a href="#.php">View Statistics</a>
      <a href="#.php">Add Statistics</a>
      <a href="#.php">Edit Statistics</a>
      <a href="#.php">Delete Statistics</a>
    </div>
  </div>
  <div style="float:left" class="dropdown">
    <button class="dropbtn" onclick=\"window.location='privilege.php'\">Privilege &dArr;</button>
    <div class="dropdown-content">
      <a href="#.php">View Privilege</a>
      <a href="#.php">Change Role's Privilege</a>
    </div>
  </div>


  <a style="float:right" href="logout.php">Logout</a>
  <div style="float:right" class="dropdown">
    <button class="dropbtn" onclick=\"window.location='profile.php'\">Profile &dArr;</button>
    <div class="dropdown-content">
      <a href="profile.php">View Profile</a>
      <a href="changePassword.php">Change Password</a>
    </div>
  </div>
  <a style="float:right" href="about.php">About</a>
</div>


<!--
// this version is with escape string
<div class=\"navmenu\">
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
</div>
-->
Admin will see this nav menu:
<div class="navmenu">
  <a class="active" href="Dashboard.php">Home</a>
  <div style="float:left" class="dropdown">
    <button class="dropbtn" onclick=\"window.location='player.php'\">Player &dArr;</button>
    <div class="dropdown-content">
      <a href="#.php">View Player</a>
      <a href="#.php">Add Player</a>
      <a href="#.php">Edit Player</a>
      <a href="#.php">Delete Statistics</a>
    </div>
  </div>
  <div style="float:left" class="dropdown">
    <button class="dropbtn" onclick=\"window.location='statistics.php'\">Statistics &dArr;</button>
    <div class="dropdown-content">
      <a href="#.php">View Statistics</a>
      <a href="#.php">Add Statistics</a>
      <a href="#.php">Edit Statistics</a>
      <a href="#.php">Delete Statistics</a>
    </div>
  </div>
  <div style="float:left" class="dropdown">
    <button class="dropbtn" onclick=\"window.location='privilege.php'\">Privilege &dArr;</button>
    <div class="dropdown-content">
      <a href="#.php">View Privilege</a>
      <a href="#.php">Change Role's Privilege</a>
      <a href="#.php">Delete Role's Privilege</a>
    </div>
  </div>


  <a style="float:right" href="logout.php">Logout</a>
  <div style="float:right" class="dropdown">
    <button class="dropbtn">Profile &dArr;</button>
    <div class="dropdown-content">
      <a href="profile.php" onclick=\"window.location='profile.php'\">View Profile</a>
      <a href="changePassword.php">Change Password</a>
    </div>
  </div>
  <a style="float:right" href="about.php">About</a>
</div>
<!--
// this version is with escape string
"<div class=\"navmenu\">
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
</div>"
-->
</body>
</html>
