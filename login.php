<?php
session_start(); // Start the session
require_once "pdo.php";
if (isset($_POST['cancel'])) {
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
  // Password is php123
$failure = false;  // If we have no POST data

if (isset($_POST['email']) && isset($_POST['pass'])) {
    if (strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
        $_SESSION['error'] = "Email and password are required";
        header("Location: login.php");
        return;
    } elseif (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        return;
    } else {
       //$check = hash('md5', $salt . $_POST['pass']);

       $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users
        WHERE email = :em AND password = :pw');
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
       
        
            if ( $row !== false ) {
                error_log("Login success " . $_POST['email']);
                $_SESSION['name'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];
            
           
            header("Location: view.php");
            return; 
        } else {
            error_log("Login fail " . $_POST['email'] . " $check");
            $_SESSION['error'] = "Incorrect password";
            header("Location: login.php");
            return;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>MISHECK MALAMA</title>
<script type="text/javascript">
    function doValidate() {
        console.log('Validating...');
        try {
            let email = document.getElementById('nam').value;
            let pw = document.getElementById('id_1723').value;
            console.log("Validating email=" + email + " pw=" + pw);
            if (email == null || email == "" || pw == null || pw == "") {
                alert("Both fields must be filled out");
                return false;
            }
            if (email.indexOf('@') == -1) {
                alert("Invalid email address");
                return false;
            }
            return true;
        } catch (e) {
            return false;
        }
        return false;
    }
</script>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
if (isset($_SESSION['error'])) {
   
    echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n";
    unset($_SESSION['error']);
}
?>
<form method="POST" onsubmit="return doValidate();">
    <label for="nam">Email</label>
    <input type="text" name="email" id="nam"><br/>
    <label for="id_1723">Password</label>
    <input type="password" name="pass" id="id_1723"><br/>
    <input type="submit" value="Log In">
    <input type="submit" name="cancel" value="Cancel">
</form>

</div>
</body>
</html>
