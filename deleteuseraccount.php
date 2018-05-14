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
  <title>Delete User Account Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $count = 1;
    $db = configDB($_SESSION["role"]);
    $query = "SELECT Account.ID, Username, RoleName FROM Account JOIN Role ON RoleID = Role.ID ORDER BY RoleName";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($accountid, $accountname, $rolename);
    }
    $stmt->data_seek(0);
    echo "<div class=\"header\" style=\"display:table;\">Delete User Account</div>
          <div class=\"container\">
          <table>
            <tr>
              <th>No.</th>
              <th>Username</th>
              <th>Role</th>
              <th>Action</th>
            </tr>";
            $row = array();
        while( $stmt->fetch() ) {
            $row = array('id'=>$count++, 'accountid'=>$accountid, 'accountname'=>$accountname, 'role'=>$rolename);
              echo "<tr>
                <td>". $row['id'] ."</td>
                <td>". $row['accountname'] ."</td>
                <td>". $row['role'] ."</td>
                <td><a href=\"deleteuseraccount.php?accountid=".$row['accountid']."\">Delete</a>
              </tr>";
        }
    echo "</table>
    </div>";
    ?>

    <?php
      if (isset($_GET["accountid"]) && !empty($_GET["accountid"])) {
        $accountid = (int)trim(strip_tags(htmlspecialchars(htmlentities($_GET["accountid"]))));
        $query = "DELETE FROM Account WHERE ID = ?";
        if ($stmt = $db->prepare($query)) {
          $stmt->bind_param("i", $accountid);
          $stmt->execute();
          echo '<script type="text/javascript"> alert("You have successfully deleted this team!")</script>';
          echo "<script>window.location = 'deleteuseraccount.php';</script>";
        } else {
          echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
          echo "<script>window.location = 'dashboard.php';</script>";
        }

      }
     ?>


</body>
</html>
