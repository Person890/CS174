<?php

require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die(errorFunction());

$username;
$hash = $pw_temp = $cookie_username = $cookie_value = $cookie_name = "";
// LOGIN form
echo <<<_END
    <form action="loginUser.php" method="post">
        <div class="container" style="background-color:#f5bac6">
            <h2 style="display: inline;">Log in: </h2>
            <label for="uname2"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="uname">
            <label for="pwd2"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="pwd">

            <button type="submit">Log In</button>
        </div>

    </form>
    _END;


//user LOG IN
// check if username and password is present
if ((isset($_POST['uname'])) && (isset($_POST['pwd']))) {

    // sanitize input
    $un_temp = mysql_entities_fix_string($conn, $_POST['uname']);
    $pw_temp = mysql_fix_string($conn, $_POST['pwd']);

    // search for username in the database 
    $query = "SELECT * FROM users WHERE username='$un_temp'";
    $result = $conn->query($query);
    // check if there was a match in the database
    if (mysqli_num_rows($result) != 0) {
        $row = $result->fetch_array(MYSQLI_NUM);
        // verify password (with salt)
        $userverified = password_verify($pw_temp, $row[1]);

        if ($userverified) {
            session_start();
            $_SESSION['username'] = $un_temp;
            $_SESSION['forename'] = $row[0];
            echo "Hi $row[0], you are now logged in.";
            die("<p><a href=displayData.php>Click here to continue</a></p>");
        } else {
            echo "Invalid username/password.";
        }
    }
} else {
    echo "Return to sign up page: ";
    goBack();
}


if (isset($_GET['logout'])) {
    destroy_session_and_data();
}

function destroy_session_and_data()
{
    session_start();
    $_SESSION = array();
    // setcookie(session_name(), '', time() - 2592000, '/');
    session_destroy();
}

function goBack()
{
    echo <<<_END
    <form action="midterm-2.php" method="post">
            <button type="submit" style="background-color:#f5bac6">Back</button>
    </form>
    _END;
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
