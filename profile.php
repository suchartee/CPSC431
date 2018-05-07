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
  <title>Profile Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    ?>

  <div class="header">
    Your profile
  </div>
  <div class="container">
    <div class="smallboxcenter">
      <h2 style="text-align:left; padding-left: 10%; color:#594F4F;">
      Your username:</h2>
      <h3 style="text-align:left; padding-left: 20%;"><?php echo $_SESSION["username"];?></h3>
      <h2 style="text-align:left; padding-left: 10%; color:#594F4F;">Your email:
      </h2>
      <h3 style="text-align:left; padding-left: 20%;"><?php echo $_SESSION["email"];?></h3>
      <h2 style="text-align:left; padding-left: 10%; color:#594F4F;">
      Your role:
      </h2>
      <h3 style="text-align:left; padding-left: 20%;">
      <?php
      switch($_SESSION["role"]) {
          case 1:
            echo "Observer";
          break;
          case 2:
            echo "Operator";
          break;
          case 3:
            echo "Manager";
          break;
          case 4:
            echo "Admin";
          break;
          default:
          echo "Account Authorized Person";
          break;
      }
      ?>
      </h3>
    <a href="changepassword.php"><input type="button" class="btn_reg" value="Change Password" name="changepassword"/></a>
    </div>
  </div>

</body>
</html>
