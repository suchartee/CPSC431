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
  <title>Add New User Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    ?>

  <div class="header">
    Add New User
  </div>
  <div class="container">
  <div class="box">
  <form action="addnewuser.php" method="post">
    <input type="text" name="username" placeholder="Username" class="textbox"
    value="<?php if(isset($_POST['email']) && !empty($_POST['username'])) { echo $_POST['username']; } ?>" required/><br/>
    <input type="password" name="password1" placeholder="Password" class="textbox" required/><br/>
    <input type="password" name="password2" placeholder="Confirm Password" class="textbox" required/><br/>
    <input type="email" name="email" placeholder="Email" class="textbox"
    value="<?php if(isset($_POST['email']) && !empty($_POST['email'])) { echo $_POST['email']; } ?>" required/><br/>
    <select class="select" name="question" required>
      <option disabled selected value>Security Question</option>
      <?php
      $db = configDB($_SESSION["role"]);
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


    <select class="select" name="role" required>
      <option disabled selected value>User's Role</option>
      <?php
      $db = configDB($_SESSION["role"]);
      // prepare for <select><option></option></select>
      // question
      $query = "SELECT ID, RoleName FROM Role";
      if ($stmt = $db->prepare($query)) {
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($roleID, $rolename);
      }
      $stmt->data_seek(0);
      while ($stmt->fetch()){
        echo "<option value=\"".$roleID."\">".$rolename."</option>";
      }
      ?>
    </select>

    <input type="submit" class="btn_reg" value="Add New User" name="register"/>
    </div>
    </form>
  </div>
  </div>


  <?php
  if (isset($_POST["register"])) {
    if (isset($_POST["username"]) && !empty($_POST["username"]) && isset($_POST["password1"]) && !empty($_POST["password1"]) &&
      isset($_POST["password2"]) && !empty($_POST["password2"]) && isset($_POST["email"]) && !empty($_POST["email"]) &&
      isset($_POST["question"]) && !empty($_POST["question"]) && isset($_POST["role"]) && !empty($_POST["role"]) &&
      isset($_POST["answer"]) && !empty($_POST["answer"])) {
        // SQL injection
        $username = strtolower(trim(strip_tags(htmlspecialchars(htmlentities($_POST['username'])))));
        $password1 = trim(strip_tags(htmlspecialchars($_POST['password1'])));
        $password2 = trim(strip_tags(htmlspecialchars($_POST['password2'])));
        $email = strtolower(strip_tags(htmlspecialchars(htmlentities($_POST['email']))));
        $question = (int)strip_tags(htmlspecialchars(htmlentities($_POST["question"])));
        $role = (int)strip_tags(htmlspecialchars(htmlentities($_POST["role"])));
        $answer = strip_tags(htmlspecialchars(htmlentities($_POST['answer'])));

        // Check if question is selected
        if (isset($_POST['question']) && !empty($_POST['question'])) {
          $question = $_POST['question'];
        }
        // validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           echo '<script type="text/javascript"> alert("The email is not in the correct format") </script>';
        } else {
          // Check if password and confirm password are the same
          if ($password1 == $password2) {
            // Hash Password
            $password = $password1;
            $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
            
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
                      $stmt->bind_param("sssiis", $username, $hashedpassword, $email, $role, $question, $answer);
                      $stmt->execute();
                      echo '<script type="text/javascript"> alert("New user account is successfully added into the account table!")</script>';
                      echo "<script>window.location = 'addnewuser.php';</script>";
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
