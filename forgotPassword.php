<?php
  session_start();
  require_once "config.php";
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


  <form method="post" action="forgotPassword.php">
    <div class="box">
      <h1>Forgot Your Password</h1>
      <input type="text" name="username" placeholder="Please Enter Your Username" class="textbox" required/><br/>
      <input type="submit" class="btn_forgot" value="Submit" name="forgotPassword"/>
      <a href="index.php"><input type="button" class="btn_login" value="Back to Login" name="login"/>
    </div>
  </form>

  <?php
  if(isset($_POST['username']) && !empty($_POST['username'])) {
      $username = strtolower(strip_tags(htmlspecialchars($_POST['username'])));
      if ($username == 'admin') {
        // admin cannot forget passwords
        echo '<script type="text/javascript"> alert("You are tricky! Do not do this again.") </script>';
      } else {
        $db = configDB(5);

        $query = "SELECT Question.Question, Account.Answer, Account.Email FROM Account JOIN Question ON Account.QuestionNum = Question.ID AND Account.Username = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $username);
    	  $stmt->execute();
    	  $stmt->store_result();
    	  $stmt->bind_result($question, $answer, $email);

        if($stmt->num_rows > 0) {
          // retrieve question for displaying on the next page
          $stmt->data_seek(0);
          while ($stmt->fetch()){
            $_SESSION["question"] = $question;
            $_SESSION["answer"] = $answer;
            $_SESSION["email"] = $email;
  				}
          echo "<script>window.location = 'securityQuestion.php';</script>";
        } else {
          // That username does not exist
          echo '<script type="text/javascript"> alert("The username does not exist!") </script>';
        }
      }
    }
   ?>



</body>
</html>
