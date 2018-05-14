<?php
  session_start();
  if (empty($_SESSION["question"])) {
    echo "<script>window.location = 'index.php';</script>"; // redirect to index.php (login page)
  }
  require_once "config.php";
  require_once "functions.php";
 ?>

<!DOCTYPE html>
<html>
<head>
  <title>Welcome to Basketball Roster</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <!-- Navigation menu on top -->
  <ul class="drop_menu">
    <li><a href='index.php'>Home</a></li>
    <li><a href='about.php'>About</a></li>
  </ul>


  <form method="post" action="securityquestion.php">
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
        $answer = trim(strip_tags(htmlspecialchars(htmlentities($_POST['answer']))));
        // ---- hash Answer
        if ($answer == $_SESSION["answer"]) {
          // prepare for generating new password
          $password = randPassword();
          $email = $_SESSION["email"];

          // store new password to database;
          // Hash Password
          $hashedpassword = password_hash($password, PASSWORD_DEFAULT);

          // Connect to database
      	  $db = configDB(5);
          $query = "UPDATE Account SET Password = ? WHERE Email = ?";
          if ($stmt = $db->prepare($query)) {
            $stmt->bind_param("ss", $hashedpassword, $email);
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

            session_destroy(); // destroy the $_SESSION["question"] and $_SESSION["answer"] and $_SESSION["email"] and $_SESSION["database"];;
            echo '<script type="text/javascript"> alert("Email is sent! Please check your email") </script>';
            echo "<script>window.location = 'index.php';</script>";
          } else {
            echo '<script type="text/javascript"> alert("Error!") </script>';
          }
        } else {
        echo '<script type="text/javascript"> alert("Wrong Answer!!") </script>';
      }
    }
   ?>



</body>
</html>
