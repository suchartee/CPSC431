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
  <title>Change User Password</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $accountid = $_SESSION["accountid"];
    ?>

  <form method="post" action="changeuserpassword.php">
    <div class="box">
      <h1>Change User Password</h1>
      <input type="password" name="password1" placeholder="New Password" class="textbox" title="Enter a password with at least 6 characters, at least one lowercase, at least one uppercase and at least one special character" required/><br/>
      <input type="password" name="password2" placeholder="Confirm new Password" class="textbox" title="Enter a password with at least 6 characters, at least one lowercase, at least one uppercase and at least one special character" required/><br/>

      <input type="submit" class="btn_reg" value="Submit" name="submit"/>
      <a href="modifyuseraccount.php"><input type="button" class="btn_login" value="Cancel" name="cancel"/></a>
    </div>
  </form>

  <?php
    if(isset($_POST['password1']) && !empty($_POST['password1']) && isset($_POST['password2']) && !empty($_POST['password2'])) {
        $password1 = strip_tags(htmlspecialchars($_POST['password1']));
        $password2 = strip_tags(htmlspecialchars($_POST['password2']));
        // check if 2 passwords are the same
        if ($password1 == $password2) {
            $db = configDB(5);
            $query = "UPDATE Account SET Password = ? WHERE ID = ?";
            if ($stmt = $db->prepare($query)) {
              $stmt->bind_param("ss", $password1, $_SESSION["accountid"]);
              $stmt->execute();
              echo '<script type="text/javascript"> alert("New password is successfully changed!") </script>';
              echo "<script>window.location = 'modifyuseraccount.php';</script>";
            } else {
                echo '<script type="text/javascript"> alert("Error!") </script>';
            }
        } else {
          echo '<script type="text/javascript"> alert("Passwords must be the same!")</script>';
        }
    }
   ?>
</body>
</html>
