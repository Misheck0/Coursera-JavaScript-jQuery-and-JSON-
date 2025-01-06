<?php
session_start();
if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function validatePos() {
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;
  
      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];
  
      if ( strlen($year) == 0 || strlen($desc) == 0 ) {
        $_SESSION["error"] =   "All fields are required";
        header('location:add.php');
        return;
      }
  
      if ( ! is_numeric($year) ) {
        $_SESSION["error"] =  "Position year must be numeric";
        header('location:add.php');
        return;
        
      }
    }
    return true;
  }
    

  function validateEdu() {
     for ($i = 1; $i <= 9; $i++) { if (!isset($_POST['edu_year' . $i]) || !isset($_POST['edu_school' . $i])) continue;
         $year = $_POST['edu_year' . $i];
          $school = $_POST['edu_school' . $i]; 
          if (strlen($year) == 0 || strlen($school) == 0) {
             $_SESSION["error"] = "All fields are required";
              header('Location: add.php'); return false; }
               if (!is_numeric($year)) { $_SESSION["error"] = "Education year must be numeric"; 
                header('Location: add.php'); return false; } 
            } return true;
        }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])) {
        $firstname = $_POST['first_name'];
        $lastname = $_POST['last_name'];
        $email = $_POST['email'];
        $headline = $_POST['headline'];
        $summary = $_POST['summary'];
        
       if(strlen($firstname) < 1 && strlen($lastname) < 1 && strlen($email) < 1 && strlen($summary) < 1) {
        
                $_SESSION["error"] = 'All fields are required';
                header('location:add.php');
                return;
            }
        
        elseif (is_numeric($summary) || is_numeric($headline)) {
            $_SESSION['error'] = 'Summary and year must be numeric';
            header("Location: add.php");
            return;
        } 
        
        else {
            validatePos();
            try{ 
           
            $stmt = $pdo->prepare('INSERT INTO Profile
            (user_id, first_name, last_name, email, headline, summary)
            VALUES ( :uid, :fn, :ln, :em, :he, :su)');
        $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'])
        );
        $rank = 1;
        for($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;

            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];

            $profile_id = $pdo->lastInsertId();



        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

        $stmt->execute(array(
        ':pid' => $profile_id,
        ':rank' => $rank,
        ':year' => $year,
        ':desc' => $desc)
        );

        $rank++;

    }
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['edu_year' . $i]) || !isset($_POST['edu_school' . $i])) continue;
        $year = $_POST['edu_year' . $i];
        $school = $_POST['edu_school' . $i];

        // Find institution_id
        $stmt = $pdo->prepare('SELECT institution_id FROM institution WHERE name = :name');
        $stmt->execute([':name' => $school]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false) {
            $institution_id = $row['institution_id'];
            $stmt = $pdo->prepare('INSERT INTO education (profile_id, institution_id, rank, year) VALUES (:pid, :iid, :rank, :year)');
            $stmt->execute([
                ':pid' => $profile_id,
                ':iid' => $institution_id,
                ':rank' => $rank,
                ':year' => $year
            ]);
            $rank++;
        }

       
    }
                $_SESSION['success'] = 'Record added';
                header("Location: view.php");
                return;
            } catch (PDOException $e) {
                $_SESSION['error'] = $e->getMessage();
                header("Location: add.php");
                return;
            }
        }
    } 
}

?>
<!DOCTYPE html>
<html>
<head>
<title>MISHECK MALAMA</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 

  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

<?php require_once "bootstrap.php"; ?>
</head>
<body>

<!-- bootstrap.php - this is HTML -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">

</head>
<body>
<div class="container">
<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . '</p>';
    unset($_SESSION['error']);
}
?>


<h1>Adding Profile for UMSI</h1>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"/></p>
<p>Email:
<input type="text" name="email" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80">
</textarea>
<p>

Education: <input type="submit" id="addEdu" value="+">
<div id="edu_fields">
</div>
</p>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
</p>
<p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script>
countPos = 0;
countEdu = 0;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');

    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"><br>\
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

    $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        $('#edu_fields').append(
            '<div id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
            </p></div>'
        );

        $('.school').autocomplete({
            source: "school.php"
        });

    });

});

</script>
</div>
</body>
</html>