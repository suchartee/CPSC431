<?php
  /*if(isset($_SESSION['email']) && (isset($_SESSION['password']))){
    header('location:homepage.php');
  }*/
 ?>

<!DOCTYPE html>
<html>
<head>
  <title>Basketball Roster Project</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="navmenu">
    <a class="active" href="index.php">Home</a>
    <a href="about.php">About</a>
  </div>

  <form method="post" action="register.php">
    <div class="box">
      <h1>Registration</h1>

      <input type="text" name="username" placeholder="Username" class="textbox" value="<?php if(isset($_POST['email']) && !empty($_POST['username'])) { echo $_POST['username']; } ?>" required/><br/>
      <input type="password" name="password1" placeholder="Password" class="textbox" pattern="(?=^.{6,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="Enter a password with at least 6 characters, at least one lowercase, at least one uppercase and at least one special character" required/><br/>
      <input type="password" name="password2" placeholder="Confirm Password" class="textbox" pattern="(?=^.{6,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="Enter a password with at least 6 characters, at least one lowercase, at least one uppercase and at least one special character" required/><br/>
      <input type="email" name="email" placeholder="Email" class="textbox" value="<?php if(isset($_POST['email']) && !empty($_POST['email'])) { echo $_POST['email']; } ?>" required/><br/>
      <select class="select" name="question" required>
        <option disabled selected value>Security Question</option>
        <option value="1">Where were you when you had your first kiss?</option>
        <option value="2">Where were you New Year's 2000?</option>
        <option value="3">What is the last name of the teacher who gave you your first failing grade?</option>
        <option value="4">What was the last name of your third grade teacher?</option>
        <option value="5">Where were you when you first heard about 9/11?</option>
      </select>
      <input type="text" name="answer" placeholder="Answer" class="textbox" required/>

      <input type="submit" class="btn_reg" value="Register" name="register"/>
      <a href="index.php"><input type="button" class="btn_login" value="Login" name="login"/></a>
    </div>
  </form>

  <?php
    // When the register button is clicked
    if(isset($_POST['register'])){

      //echo '<script type="text/javascript"> alert("Register button clicked") </script>';
      $username = strtolower(strip_tags(htmlspecialchars($_POST['username'])));
      $password1 = strip_tags(htmlspecialchars($_POST['password1']));
      $password2 = strip_tags(htmlspecialchars($_POST['password2']));
      $email = strtolower(strip_tags(htmlspecialchars(strtolower($_POST['email']))));
      $role = 'observer';

      if (isset($_POST['question']) && !empty($_POST['question'])) {
        $question = $_POST['question'];
      }
      $answer = strip_tags(htmlspecialchars($_POST['answer']));

      // validate password format
      if (!preg_match("/^\S*(?=\S{6,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$/", $password1)) {
        /* ^: beginning of string
         \S*: any set of characters
         (?=\S{6,}): of at least length 6
         (?=\S*[a-z]): containing at least one lowercase letter
         (?=\S*[A-Z]): and at least one uppercase letter
         (?=\S*[\d]): and at least one number
         (?=\S*[\W]): non-word characters = special characers
         $: the end of the string
         */
        echo '<script type="text/javascript"> alert("The password must be in the correct format") </script>';
      } else {
        // validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  	       echo '<script type="text/javascript"> alert("The email is not in the correct format") </script>';
        } else {
          if ($password1 == $password2) {
              $host = 'localhost';
              $usernameSQL = 'manager';
              $passwordSQL = 'managerPassword';
              $database = 'db_project';
              $db = mysqli_connect($host, $usernameSQL, $passwordSQL) or die("Unable to connect");
              mysqli_select_db($db, $database);
            //$hashed_password = password_hash($password1, PASSWORD_DEFAULT);
            $password = $password1;
            $query = "SELECT * FROM Account WHERE Username = '$username'";
            $stmt = mysqli_query($db, $query);

            // check username
            if(mysqli_num_rows($stmt) > 0){
              // there is the username already in the database
              echo '<script type="text/javascript"> alert("This username is already exists") </script>';
            } else {

              $query = "SELECT * FROM Account WHERE Email = '$email'";
              $stmt = mysqli_query($db, $query);

              // check email
              if(mysqli_num_rows($stmt) > 0){
                // there is the email already in the database
                echo '<script type="text/javascript"> alert("This email is already exists") </script>';
              }
              else {
                $query = "INSERT INTO Account (Username, Password, Email, RoleName, QuestionNum, Answer) VALUES('$username', '$password', '$email', '$role', '$question', '$answer')";
                $stmt = mysqli_query($db, $query);

                if ($stmt) {
                  echo '<script type="text/javascript"> alert("You are registered") </script>';
                  echo "<script>window.location = 'index.php';</script>";
                }
                else {
                  echo '<script type="text/javascript"> alert("Error!")</script>';
                }
              }
            }
          }
          else {
            echo '<script type="text/javascript"> alert("Your passwords must be the same!")</script>';
          }
        }
      }
    }
   ?>

</body>
</html>
