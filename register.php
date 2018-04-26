<?php
  require_once "config.php";

?>

<!DOCTYPE html>
<html>
<head>
  <title>Basketball Roster Project</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <ul class="drop_menu">
    <li><a href='index.php'>Home</a></li>
    <li><a href='about.php'>About</a></li>
  </ul>

  <form method="post" action="register.php">
    <div class="box">
      <h1>Registration</h1>

      <input type="text" name="username" placeholder="Username" class="textbox" value="<?php if(isset($_POST['email']) && !empty($_POST['username'])) { echo $_POST['username']; } ?>" required/><br/>
      <input type="password" name="password1" placeholder="Password" class="textbox" title="Enter a password with at least 6 characters, at least one lowercase, at least one uppercase and at least one special character" required/><br/>
      <input type="password" name="password2" placeholder="Confirm Password" class="textbox" title="Enter a password with at least 6 characters, at least one lowercase, at least one uppercase and at least one special character" required/><br/>
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
      <a href="index.php"><input type="button" class="btn_login" value="Back to Login" name="login"/></a>
    </div>
  </form>

  <?php
    // When the register button is clicked
    if(isset($_POST['register'])){
      // SQL injection
      $username = strtolower(strip_tags(htmlspecialchars($_POST['username'])));
      $password1 = strip_tags(htmlspecialchars($_POST['password1']));
      $password2 = strip_tags(htmlspecialchars($_POST['password2']));
      $email = strtolower(strip_tags(htmlspecialchars(strtolower($_POST['email']))));
      $role = 1;

      // Check if question is selected
      if (isset($_POST['question']) && !empty($_POST['question'])) {
        $question = $_POST['question'];
      }
      $answer = strip_tags(htmlspecialchars($_POST['answer']));

      // validate password format
      //^(?=.*[A-Z])(?=.*[a-z])(?=.*\w)(?=.*\d).{6,}$
      //"/^S*(?=S{6,})(?=S*[a-z])(?=S*[A-Z])(?=S*[d])(?=S*[W])S*$/"
      if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\w)(?=.*\d).{6,}$/", $password1)) {
        /* ^: beginning of string
         S*: any set of characters
         (?=S{6,}): of at least length 6
         (?=S*[a-z]): containing at least one lowercase letter
         (?=S*[A-Z]): and at least one uppercase letter
         (?=S*[d]): and at least one number
         (?=S*[W]): non-word characters = special characters
         $: the end of the string
         */
        echo '<script type="text/javascript"> alert("The password must be in the correct format") </script>';
      } else {
        // validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  	       echo '<script type="text/javascript"> alert("The email is not in the correct format") </script>';
        } else {
          // Check if password and confirm password are the same
          if ($password1 == $password2) {
            $db = configDB(5);
            // DONT FORGET TO HASH PASSWORD !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $password = $password1;
            $query = "SELECT * FROM Account WHERE Username = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();

            // check if this username is existed
            if($stmt->num_rows > 0){
              // there is the username already in the database
              echo '<script type="text/javascript"> ert("This username is already exists") </script>';
            } else {
              $db = configDB(5);
              $query = "SELECT * FROM Account WHERE Email = ?";
              $stmt = $db->prepare($query);
              $stmt->bind_param("s", $email);
              $stmt->execute();

              // check if this email is existed
              if($stmt->num_rows > 0){
                // there is the email already in the database
                echo '<script type="text/javascript"> alert("This email is already exists") </script>';
              }
              else {
                // add this username into database
                $db = configDB(5);
                $query = "INSERT INTO Account (Username, Password, Email, RoleID, QuestionNum, Answer) VALUES(?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->bind_param("sssiis", $username, $password, $email, $role, $question, $answer);
                $stmt->execute();

                if ($stmt) {
                  // redirect to index.php
                  echo '<script type="text/javascript"> alert("You are registered") </script>';
                  echo "<script>window.location = 'index.php';</script>";
                }
                else {
                  // something is wrong
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
