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

      <input type="text" name="username" placeholder="Username" class="textbox"
      value="<?php if(isset($_POST['email']) && !empty($_POST['username'])) { echo $_POST['username']; } ?>" required/><br/>
      <input type="password" name="password1" placeholder="Password" class="textbox"
      title="Enter a password with at least 6 characters, at least one lowercase, at least one uppercase and at least one special character" required/><br/>
      <input type="password" name="password2" placeholder="Confirm Password" class="textbox"
      title="Enter a password with at least 6 characters, at least one lowercase, at least one uppercase and at least one special character" required/><br/>
      <input type="email" name="email" placeholder="Email" class="textbox"
      value="<?php if(isset($_POST['email']) && !empty($_POST['email'])) { echo $_POST['email']; } ?>" required/><br/>
      <select class="select" name="question" required>
        <option disabled selected value>Security Question</option>
        <?php
        $db = configDB(5);
        // prepare for <select><option></option></select>
        // question
        $query = "SELECT ID, Question FROM Question";
        if ($stmt = $db->prepare($query)) {
          $stmt->execute();
          $stmt->store_result();
          $stmt->bind_result($questionID, $question);
        }
        $stmt->data_seek(0);
        while ($stmt->fetch()){
          echo "<option value=\"".$questionID."\">".$question."</option>";
        }
        ?>
      </select>
      <input type="text" name="answer" placeholder="Answer" class="textbox" required/>

      <input type="submit" class="btn_reg" value="Register" name="register"/>
      <a href="index.php"><input type="button" class="btn_login" value="Back to Login" name="login"/></a>
    </div>
  </form>

  <?php
    // When the register button is clicked
    if(isset($_POST['register']) && !empty($_POST["register"])){
      // SQL injection
      $username = trim(strtolower(strip_tags(htmlspecialchars(htmlentities($_POST['username'])))));
      $password1 = trim(strip_tags(htmlspecialchars(htmlentities($_POST['password1']))));
      $password2 = trim(strip_tags(htmlspecialchars(htmlentities($_POST['password2']))));
      $email = strtolower(trim(strip_tags(htmlspecialchars(htmlentities($_POST['email'])))));
      $role = 1; // by default = observer

      // Check if question is selected
      if (isset($_POST['question']) && !empty($_POST['question'])) {
        $question = (int)trim(strip_tags(htmlspecialchars(htmlentities($_POST['question']))));
      }
      $answer = trim(strip_tags(htmlspecialchars(htmlentities($_POST['answer']))));

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

        $password_warning = 'The password must be in the correct format:'
          .'\n- At least 6 characters long'
          .'\n- Contain at least one lowercase and uppercase letter '
          .'\n- Have at least one number  '
          .'\n- Contain at least one special characters (such as @))';
        echo '<script type="text/javascript">';
        echo 'alert("'.$password_warning.'")';
        echo  '</script>';
      } else {
        // validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  	       echo '<script type="text/javascript"> alert("The email is not in the correct format") </script>';
        } else {
          // Check if password and confirm password are the same
          if ($password1 == $password2) {
            // DONT FORGET TO HASH PASSWORD !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $password = $password1;
            $db = configDB(5);
            $query = "SELECT * FROM Account WHERE Username = ?";
            if ($stmt = $db->prepare($query)) {
              $stmt->bind_param("s", $username);
              $stmt->execute();

              // check if this username is existed
              if($stmt->num_rows > 0){
                // there is the username already in the database
                echo '<script type="text/javascript"> alert("This username is already exists") </script>';
              } else {
                $db = configDB(5);
                $query = "SELECT * FROM Account WHERE Email = ?";
                if ($stmt = $db->prepare($query)) {
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
                    if ($stmt = $db->prepare($query)) {
                      $stmt->bind_param("sssiis", $username, $password, $email, $role, $question, $answer);
                      $stmt->execute();
                      echo '<script type="text/javascript"> alert("You are registered") </script>';
                      echo "<script>window.location = 'index.php';</script>";
                    } else {
                      // prepare is fail
                      echo '<script type="text/javascript"> alert("insert Error!")</script>';
                    }
                  }
                } else {
                  echo '<script type="text/javascript"> alert("email Error!") </script>';
                }
              }
            } else {
              echo '<script type="text/javascript"> alert("username Error!")</script>';
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
