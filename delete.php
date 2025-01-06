<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: view.php' ) ;
    return;
}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing user_id";
  header('Location: view.php');
  return;
}

$stmt = $pdo->prepare("SELECT profile_id, first_name from profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for user_id';
    header( 'Location: index.php' ) ;
    return;
}

?>

<!DOCTYPE html>

<head>
<?php require_once "bootstrap.php"; ?>
<title>MISHECK MALAMA</title>
</head>
<body>
<p>Confirm: Deleting <?= htmlentities($row['first_name']) ?></p>

<form method="post">
<input type="hidden" name="profile_id" value="<?=$row['profile_id'] ?>">
<input type="submit" value="Delete" name="delete" onclick="check()" >
<a href="view.php">Cancel</a>
</form>
</body>
<script>
  function check(){
    alert('are you sure u want to delect')
    return;
  }
</script>
</html>