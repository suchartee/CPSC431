<?php
  session_start();
  if (empty($_SESSION["question"])) {
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
  <!-- Navigation menu on top -->
  <div class="navmenu">
    <a class="active" href="index.php">Home</a>
    <a href="about.php">About</a>
  </div>


  <form method="post" action="securityQuestion.php">
    <div class="box">
      <h1>Security Question</h1>
      <?php echo $_SESSION["question"]; ?>
      <input type="text" name="answer" placeholder="Please Enter Your Answer" class="textbox" required/><br/>
      <input type="submit" class="btn_forgot" value="Submit" name="forgotPassword"/>
      <a href="index.php"><input type="button" class="btn_login" value="Back to Login" name="login"/>
    </div>
  </form>

  <?php
    if(isset($_POST['answer']) && !empty($_POST['answer'])) {
        $answer = strip_tags(htmlspecialchars($_POST['answer']));
        // ---- hash Answer
        if ($answer == $_SESSION["answer"]) {
          // prepare for generating new password
          $password_special = '@#$%*-_+&';
          $password_num = '0123456789';
          $password_lower = 'abcdefghijklmnpqrstuwxyz';
          $password_upper = 'ABCDEFGHIJKLMNPQRSTUWXYZ';
          $password_rand = '@#$%*-_+&0123456789abcdefghijklmnpqrstuwxyzABCDEFGHIJKLMNPQRSTUWXYZ';
          $password="";
          // generate 6 characters with atleast one lowercase, atleast one uppercase, atleast one number, and atleast one special character
          $password .= substr(str_shuffle($password_special), 0, 1);
          $password .= substr(str_shuffle($password_num), 0, 1);
          $password .= substr(str_shuffle($password_lower), 0, 1);
          $password .= substr(str_shuffle($password_upper), 0, 1);
          $password .= substr(str_shuffle($password_rand), 0, 2);

          $password = str_shuffle($password);
          $email = $_SESSION["email"];

          // store new password to database;
          // dont forget to hash password!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
          $host = 'localhost';
          $usernameSQL = 'manager';
          $passwordSQL = 'managerPassword';
          $database = 'db_project';

          // Connect to database
      	  $db = new mysqli($host, $usernameSQL, $passwordSQL, $database);
      	  if (mysqli_connect_errno()){
      		  echo '<p>Error: Could not connect to database.<br/>
      		  Please try again later.</p>';
      		  die();
      	  }

          $query = "UPDATE Account SET Password = ? WHERE Email = ?";
          $stmt = $db->prepare($query);
          $stmt->bind_param("ss", $password, $email);
      	  $stmt->execute();

          // prepare for Email
          $subject = "Basketball Roster New Password";
          $message =
          "Hi,

          You have requested the new password.
          Please use this password to login. Remember to change the password immediately after you logged in.

          ".$password."

          Sincerely,

          Webmaster
          CPSC431 Basketball Roster
          ";
//          $header = "From: <webmaster@CPSC431basketballroster.com>";

          // send Email
          mail($email, $subject, $message);


          session_destroy(); // destroy the $_SESSION["question"] and $_SESSION["answer"] and $_SESSION["email"];
          echo '<script type="text/javascript"> alert("Email is sent! Please check your email") </script>';
          echo "<script>window.location = 'index.php';</script>";

        } else {
        echo '<script type="text/javascript"> alert("Wrong Answer!!") </script>';
      }
    }
   ?>



</body>
</html>
