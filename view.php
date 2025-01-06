<?php
session_start();
if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}
?>
<!DOCTYPE html>
<html>
<head>
    
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

<?php require_once "bootstrap.php"; ?>
<title>MISHECK MALAMA</title>
</head>
<body>
<div class="container">

<p> <h1>Tracking Autos for  <?= htmlentities($_SESSION['name']); ?>! </h1>
</p>
<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
} 
?>

<h2>Automobiles</h2>
<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query('SELECT profile_id,first_name, headline FROM profile ');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)) {
    echo '<p>No records found</p>';
} else {
    echo '<table border="1">';
    echo '<tr><th>Name</th><th>Headline</th><th>Action</th></tr>';
    foreach ($rows as $row) {
        echo '<tr>';
        echo '<td>' . htmlentities($row['first_name']) . '</td>';
        echo '<td>' . htmlentities($row['headline']) . '</td>';
       // echo '<td>' . htmlentities($row['year']) . '</td>';
       // echo '<td>' . htmlentities($row['mileage']) . '</td>';
        echo '<td>';
        echo '<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> ';
        echo '<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
}
?>


<p><a href="add.php">Add New Entry</a>  </p>| <br>
<form method="POST" action="logout.php" style="display:inline;">
    <input type="submit" value="Logout">
</form>
</div>
</body>
</html>
