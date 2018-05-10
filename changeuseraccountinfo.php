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
  <title>Modify User Account Page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    include 'logged_navbar.php';
    $db = configDB($_SESSION["role"]);

    // retrieve info from previous page (user's choice)
    if (isset($_GET["accountid"]) && isset($_GET["username"]) && isset($_GET["email"]) && isset($_GET["roleid"]) && isset($_GET["rolename"]) && isset($_GET["questionid"]) && isset($_GET["question"]) && isset($_GET["answer"])) {
      $_SESSION["accountid"] = $_GET["accountid"];
      $username = $_GET["username"];
      $email = $_GET["email"];
      $_SESSION["roleid"] = $_GET["roleid"];
      $rolename = $_GET["rolename"];
      $_SESSION["questionid"] = $_GET["questionid"];
      $question = $_GET["question"];
      $answer = $_GET["answer"];
    } else {
      $accountid = "";
      $username = "";
      $email = "";
      $roleid = 0;
      $rolename = "";
      $questionid = 0;
      $question = "";
      $answer = "";
    }
    ?>

  <div class="header">
    Modify User Account
  </div>
  <div class="container">
  <div class="box">
  <form action="changeuseraccountinfo.php" method="post">
  <label>Username</label><br/>
  <input type="text" name="username" value="<?php echo $username ?>" class="textbox" readonly="readonly" required/><br/>
  <label>Email</label><br/>
  <input type="email" name="email" value="<?php echo $email ?>" class="textbox" required/><br/>
  <?php
  // prepare for the <select><option></option></select>
  // role
  $query = "SELECT ID, RoleName FROM Role";
  if ($stmt = $db->prepare($query)) {
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($roleidDB, $rolenameDB);
  }
   ?>
  <label>Role</label><br/>
  <select class="select" name="roleid" required>
    <option value="<?php echo $_SESSION["roleid"]?>"><?php echo $rolename ?></option>
          <?php
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$roleidDB."\">".$rolenameDB."</option>";
					}
          ?>
  </select><br/>
  <?php
    // prepare for the <select><option></option></select>
    // question
    $query = "SELECT ID, Question FROM Question";
    if ($stmt = $db->prepare($query)) {
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($questionidDB, $questionDB);
    }
   ?>
  <label>Security Question</label><br/>
  <select class="select" name="questionid" required>
    <option value="<?php echo $_SESSION["questionid"]?>"><?php echo $question ?></option>
          <?php
					$stmt->data_seek(0);
					while ($stmt->fetch()){
						echo "<option value=\"".$questionidDB."\">".$questionDB."</option>";
					}
          ?>
  </select><br/>
  <label>Answer</label><br/>
  <input type="text" name="answer" value="<?php echo $answer ?>" class="textbox" required/><br/>
  <a href="changeuserpassword.php"><input type="button" class="btn_login" value="Change User Password" name="changeuserpassword"/></a>
  <input type="submit" class="btn_reg" value="Modify User Account" name="submit"/>
  </form>
  </div>
  </div>


  <?php
  if (isset($_POST["submit"])) {
    // check SQL injection
    if (!empty($_POST["username"]) && !empty($_POST["email"]) && !empty($_POST["roleid"]) && !empty($_POST["questionid"]) && !empty($_POST["answer"])) {
      $usernameDB = trim(strtolower(strip_tags(htmlspecialchars($_POST["username"]))));
      $emailDB = strtolower(strip_tags(htmlspecialchars(strtolower($_POST["email"]))));
      $roleidDB = $_POST["roleid"];
      if ($roleidDB == $_SESSION["roleid"]) {
        $roleidDB =$_SESSION["roleid"];
      }
      $questionidDB = $_POST["questionid"];
      if ($questionidDB == $_SESSION["questionid"]) {
        $questionidDB = $_SESSION["questionid"];
      }
      $answerDB = trim(strip_tags(htmlspecialchars(strtolower($_POST["answer"]))));
      $accountidDB = $_SESSION["accountid"];

      unset($_SESSION["accountid"]);
      unset($_SESSION["roleid"]);
      unset($_SESSION["questionid"]);

      /*echo $usernameDB;
      echo $emailDB;
      echo $roleidDB;
      echo $questionidDB;
      echo $answerDB;
      echo $accountidDB;*/

      $query = "UPDATE Account SET Username = ?, Email = ?, RoleID = ?, QuestionNum = ?, Answer = ? WHERE ID = ?";
      if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("ssiisi", $usernameDB, $emailDB, $roleidDB, $questionidDB, $answerDB, $accountidDB);
        $stmt->execute();
        echo '<script type="text/javascript"> alert("You have successfully changed user\'s account information!")</script>';
        echo "<script>window.location = 'modifyuseraccount.php';</script>";
      } else {
        echo '<script type="text/javascript"> alert("You do not have this privilege!")</script>';
        echo "<script>window.location = 'dashboard.php';</script>";
      }
    }
  }
  ?>

</body>
</html>
