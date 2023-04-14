<style>
    <?php include 'main.css'; ?>
</style>

<?php

require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die(errorFunction());

//user LOG IN
// check if username and password is present
if ((isset($_POST['studentid1'])) && (isset($_POST['password1']))) {
    // sanitize input
    $id_temp = mysql_entities_fix_string($conn, $_POST['studentid1']);
    $pw_temp = mysql_fix_string($conn, $_POST['password1']);

    // search for username in the database 
    $query = "SELECT * FROM credentials WHERE id='$id_temp'";

    $result = $conn->query($query);
    // check if there was a match in the database
    if (mysqli_num_rows($result) != 0) {
        $row = $result->fetch_array(MYSQLI_NUM);
        // verify password (with salt)
        $userverified = password_verify($pw_temp, $row[3]);
        if ($userverified) {
            session_start();
            $_SESSION['studentid'] = $id_temp;
            echo "<p><a href=firstPage.php>Click here to continue</a></p>";
        } else {
            echo "Invalid username/password.";
        }
    }
} else {
    goBack();
}

echo <<<_END
    <div>
    <form action="firstPage.php" method="post">
        <input type="submit" class="submitBtn" style=" background-color: 2a9d8f; box-shadow: 0 10px 25px rgba(92, 99, 105, .2); margin-left: 20px;" value="Back">
    </form>
    </div>

            <div class="signupForm">
                <form action="secondPage.php" class="form" method="post">
                    <h1 class="title">Sign up</h1>

                    <div class="inputContainer">
                        <input type="text" class="input" placeholder="a" name="name">
                        <label for="name" class="label">Name</label>
                    </div>

                    <div class="inputContainer">
                        <input type="text" class="input" placeholder="a" name="studentid">
                        <label for="studentid" class="label">10-digit Student ID</label>
                    </div>

                    <div class="inputContainer">
                        <input type="text" class="input" placeholder="a" name="email">
                        <label for="email" class="label">Email</label>
                    </div>

                    <div class="inputContainer">
                        <input type="password" class="input" placeholder="a" name="password">
                        <label for="password" class="label">Password</label>
                    </div>

                    <input type="submit" class="submitBtn" style="background-color: e9c46a;" value="Sign up">
                </form>
            </div>

            <div class="loginForm" >
                <form action="secondPage.php" class="form" method="post">
                <h1 class="title">Log in</h1>

                <div class="inputContainer">
                    <input name="studentid1" type="text" class="input" placeholder="a">
                    <label for="studentid1" class="label">10-digit Student ID</label>
                </div>

                <div class="inputContainer">
                    <input name="password1" type="text" class="input" placeholder="a">
                    <label for="password1" class="label">Password</label>
                </div>
                <input type="submit" class="submitBtn" value="Log in" style="background-color: f4a261;">
                </form> 
            </div>
_END;




//user SIGN UP
if ((isset($_POST['name'])) && (isset($_POST['studentid'])) && (isset($_POST['email'])) && (isset($_POST['password']))) {

    // sanitizing the same way as login
    $un_temp = mysql_entities_fix_string($conn, $_POST['name']);
    $id_temp = mysql_entities_fix_string($conn, $_POST['studentid']);
    $email_temp = mysql_entities_fix_string($conn, $_POST['email']);
    $pw_temp = mysql_fix_string($conn, $_POST['password']);

    // password hash adds salt to password and securely hashes it - can be verified by password_verify
    $hash = password_hash($pw_temp, PASSWORD_DEFAULT);

    //store salted and hashed password in a database
    $query = "INSERT INTO credentials VALUES" .
        "('$un_temp', '$id_temp', '$email_temp', '$hash')";
    $result = $conn->query($query);
    echo "You just signed up, $un_temp!   ";
    if (!$result) {
        errorFunction();
    }
}


function goBack()
{
    echo "Log in to be redirected to advisors page.";
}


// print out generic error message
function errorFunction()
{
    echo "Ooops, there was an error.";
}

// helper functions for sanitizing strings
function mysql_entities_fix_string($conn, $string)
{
    return htmlentities(mysql_fix_string($conn, $string));
}

function mysql_fix_string($conn, $string)
{
    return $conn->real_escape_string($string);
}

$result->close();
$conn->close();

function get_post($conn, $var)
{
    return $conn->real_escape_string($_POST[$var]);
}
