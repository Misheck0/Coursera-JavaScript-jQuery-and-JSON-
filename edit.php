<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline']) ) {

    // Data validation
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1) {
        $_SESSION['error'] = 'Missing data';
        header("Location: edit.php?user_id=".$_POST['profile_id  ']);
        return;
    }

    /**if ( strpos($_POST['email'],'@') === false ) {
        $_SESSION['error'] = 'Bad data';
        header("Location: edit.php?user_id=".$_POST['user_id']);
        return;
    } */

    $sql = "UPDATE profile SET first_name = :first_name,
            last_name = :last_name, email = :email,
            headline = :headline,
            summary = :summary
            WHERE profile_id = :profile_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':profile_id' => $_POST['profile_id']
    ));
    $_SESSION['success'] = 'Record updated';
    header( 'Location: view.php' ) ;
    return;
}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing user_id";
  header('Location: view.php');
  return;
} 

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for user_id';
    header( 'Location: view.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$firstname = htmlentities($row['first_name']);
$lastname = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
$profile_id = $row['profile_id'];
?>
<!DOCTYPE html>

<head>
<?php require_once "bootstrap.php"; ?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 

  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

<title>MISHECK MALAMA</title>
</head>
<body>
<p>Edit User</p>
<h1>Adding Profile for UMSI</h1>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60" value="<?php echo $firstname ?>" /></p>
<p>Last Name:
<input type="text" name="last_name" size="60" value="<?php echo $lastname ?>"/></p>
<p>Email:
<input type="text" name="email" size="30" value="<?php echo $email ?>"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80" value="<?php echo $headline ?>"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80" value="<?php echo $summary ?>">
</textarea>
<p></p>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<p><input type="submit" value="Save"/>
<a href="view.php">Cancel</a></p>
</form>
</body>
</html>