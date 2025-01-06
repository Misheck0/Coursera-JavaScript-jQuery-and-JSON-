<?php
session_start();
if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function validatePos() {
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i]) || !isset($_POST['desc' . $i])) continue;
        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];
        if (strlen($year) == 0 || strlen($desc) == 0) {
            $_SESSION["error"] = "All fields are required";
            header('Location: add.php');
            return false;
        }
        if (!is_numeric($year)) {
            $_SESSION["error"] = "Position year must be numeric";
            header('Location: add.php');
            return false;
        }
    }
    return true;
}

function validateEdu() {
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['edu_year' . $i]) || !isset($_POST['edu_school' . $i])) continue;
        $year = $_POST['edu_year' . $i];
        $school = $_POST['edu_school' . $i];
        if (strlen($year) == 0 || strlen($school) == 0) {
            $_SESSION["error"] = "All fields are required";
            header('Location: add.php');
            return false;
        }
        if (!is_numeric($year)) {
            $_SESSION["error"] = "Education year must be numeric";
            header('Location: add.php');
            return false;
        }
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])) {
        $firstname = $_POST['first_name'];
        $lastname = $_POST['last_name'];
        $email = $_POST['email'];
        $headline = $_POST['headline'];
        $summary = $_POST['summary'];
        
        if (strlen($firstname) < 1 || strlen($lastname) < 1 || strlen($email) < 1 || strlen($summary) < 1) {
            $_SESSION["error"] = 'All fields are required';
            header('Location: add.php');
            return;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email address';
            header("Location: add.php");
            return;
        }

        if (is_numeric($summary) || is_numeric($headline)) {
            $_SESSION['error'] = 'Summary and headline must not be numeric';
            header("Location: add.php");
            return;
        } 
        
        if (!validatePos() || !validateEdu()) {
            return;
        }
        
        try { 
            $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)');
            $stmt->execute([
                ':uid' => $_SESSION['user_id'],
                ':fn' => $firstname,
                ':ln' => $lastname,
                ':em' => $email,
                ':he' => $headline,
                ':su' => $summary
            ]);
            $profile_id = $pdo->lastInsertId();
            
            $rank = 1;
            for ($i = 1; $i <= 9; $i++) {
                if (!isset($_POST['year' . $i]) || !isset($_POST['desc' . $i])) continue;
                $year = $_POST['year' . $i];
                $desc = $_POST['desc' . $i];
                $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :desc)');
                $stmt->execute([
                    ':pid' => $profile_id,
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc
                ]);
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
        <textarea name="summary" rows="8" cols="80"></textarea></p>
        
        <p>
        Education: <input type="submit" id="addEdu" value="+">
        <div id="edu_fields"></div>
        </p>
        <p>
        Position: <input type="submit" id="addPos" value="+">
        <div id="position_fields"></div>
        </p>
        
        <p>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
        </p>
    </form>

    <script>
    countPos = 0;
    countEdu = 0;

    $(document).ready(function() {
        $('#addPos').click(function(event) {
            event.preventDefault();
            if (countPos >= 9) {
                alert("Maximum of nine position entries exceeded[_{{{CITATION{{{_1{](https://github.com/raoulsuli/CRUD-App/tree/ad93757dcece9c206af45656b7ddf2ebab1484a2/source%2Fadd.php)[_{{{CITATION{{{_2{](https://github.com/NasoohOlabi/WA4E_course_work/tree/8c1ac82a8a37a1752e1329ffe1819507317ebcf9/htdocs%20for%20the%20project%2Fedit.php)[_{{{CITATION{{{_3{](https://github.com/ShreyT1257/Coursera-Applications/tree/82914d5a3e00530fb3adb6bce0265f4892af6a4b/JS%2CJQuery%2CJSON%2FFinal_Assignment%2Futil.php)[_{{{CITATION{{{_4{](https://github.com/AkashArumugam/wa4e-coursera/tree/1364f9e5df82960fbd819bafd686a074eb9d31a2/JavaScript%2C%20JQuery%2C%20JSON%2Fweek-4-assignment%2Futil.php)[_{{{CITATION{{{_5{](https://github.com/raoulsuli/CRUD-App/tree/ad93757dcece9c206af45656b7ddf2ebab1484a2/source%2Futil.php)[_{{{CITATION{{{_6{](https://github.com/priyal200/resumeA/tree/53e18694ed7dadb85bb0e0a34548f7fe4a164e25/util.php)[_{{{CITATION{{{_7{](https://github.com/jcavenue/Javascript-jquery-and-json/tree/86d3f0caaace4d1fd255758ae47a92d26e0b76fe/edit.php)[_{{{CITATION{{{_8{](https://github.com/smakhdum/education/tree/19651ab566a29115d96bf6809fb477d626538d2d/add1.php)[_{{{CITATION{{{_9{](https://github.com/cleancodifier/coursera/tree/3d1113760093de4b6825d6c2d244728f34c4ae7e/javascript-jquery-json%2Fweek1%2Fadd.php)[_{{{CITATION{{{_10{](https://github.com/aseerkt/chuck_php/tree/f88321ec5581efedbbd52d194ca31aa2a82222c7/c4_javascript%2Fadd.php)[_{{{CITATION{{{_11{](https://github.com/cleancodifier/coursera/tree/3d1113760093de4b6825d6c2d244728f34c4ae7e/javascript-jquery-json%2Fweek3%2Fedit.php)[_{{{CITATION{{{_12{](https://github.com/lestercardoz11/resume-registry/tree/340616221bcf76ad1b132bed544c13d1a28bcf72/util.php)[_{{{CITATION{{{_13{](https://github.com/islamhannachii/JavaScript-jQuery-and-JSON/tree/e4ddc3550e0ec7875c0ab17d810bc712a3ba7785/week%204%2Fadd.php)[_{{{CITATION{{{_14{](https://github.com/manuelitoo532/qsjasasljan/tree/eee0ca414047a17f3f0456753470562b96e8d25e/Facturacion%2Flol%2Fhead.php)[_{{{CITATION{{{_15{](https://github.com/yuvashreeb/Laravel5/tree/ba40584c601d253149a78be21852d49eb3e54d68/resources%2Fviews%2FimageUpload%2Fimageuploadview.blade.php)[_{{{CITATION{{{_16{](https://github.com/intanon55/login01/tree/f57d4dbfb05f9eb0aa08a3f57abd576fd19dc056/admin.php)[_{{{CITATION{{{_17{](https://github.com/garluna/movieDatabase/tree/a3e05b983ec77b2703fadf7ae641edd99f84bdc1/php%2Fsearch.php)[_{{{CITATION{{{_18{](https://github.com/S-Jenya/FirstSTP/tree/efab376b4eb50397d1106e49fc5fcc5ac24da7ad/add_institut.php)[_{{{CITATION{{{_19{](https://github.com/AnishBade/Building-web-Applications-php-Coursera/tree/9fef30d4d1f91165f5090c5713a7d63aebcefa2a/JavaScript%2C%20jQuery%2C%20and%20JSON%2Fweek_4_assignment%2Fhelpers%2Fhead_helper.php)[_{{{CITATION{{{_20{](https://github.com/zenond/PROJET_JAMEL_2/tree/4e39108a7cdc877855a2605b18bbf506ebdbae16/pages%2FdataGenerator.php)[_{{{CITATION{{{_21{](https://github.com/SubashiniPiumali/GymManagementSystem/tree/ac43b5a31ba6f43c9a070d5a08350f0d06b1ceff/adminDashboard1.php)[_{{{CITATION{{{_22{](https://github.com/Vectoryzed/Web-Applications/tree/d3c5a1baec6a5a7d32082ebe151c092d16afbd92/4%20-%20JAVASCRIPT%2C%20JQUERY%2C%20JSON%2F1%20-%20Database%20with%20JavaScript%2Fadd.php)[_{{{CITATION{{{_23{](https://github.com/bschewe/Code-Examples/tree/6f4e3603d8ace251d2075bc645ebe59d516b6bca/PHP%2FjQuery%2Fadd.php)[_{{{CITATION{{{_24{](https://github.com/SilenMe/web-devlopment-using-PHP/tree/dfbfacb23b61b315c310628e372e4fdef672223f/first%2Fassignment41%2Fadd.php)[_{{{CITATION{{{_25{](https://github.com/mengshi123/coursera-wa4e/tree/918d118947754e7e2cf4a76ff6d26b0fb1c10d79/jshw1%2Fadd.php)[_{{{CITATION{{{_26{](https://github.com/mengshi123/coursera-wa4e/tree/918d118947754e7e2cf4a76ff6d26b0fb1c10d79/resedu%2Fadd.php)[_{{{CITATION{{{_27{](https://github.com/pilohem/crud/tree/d8671957538cd23e4dfbb9944472930e71873562/add.php)[_{{{CITATION{{{_28{](https://github.com/apoorvsingh090/CRUD-WA4E/tree/05f6bafbf2049369457cdaec7e19b1dcb84afe9f/course4-week4%2Fadd.php)[_{{{CITATION{{{_29{](https://github.com/ahmed-sakka/Resume-Registry/tree/ffc0934a033bee00fe255a61076853cf15c9cba6/add.php)[_{{{CITATION{{{_30{](https://github.com/MichaeLaudrup/WebDesingFullStack_zone/tree/2766badae572b8a416a9cacc857a5d957e0ef98d/v4.0_CRUD_PHP_SQL_JS_JQuery_JSON%2Fadd.php)[_{{{CITATION{{{_31{](https://github.com/serenascalzi/registry/tree/876ddccbe8c287daf742208add506f210c51d817/add.php)