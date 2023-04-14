<?php

require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die(errorFunction());

$username;
$hash = $pw_temp = $cookie_username = $cookie_value = $cookie_name = "";


// sign up form
echo <<<_END
    <form action="midterm-2.php" method="post">
        <div class="container" style="background-color:#9fa7d9">
            <h2 style="display: inline;">Sign Up: </h2>
            <label for="uname2"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="uname2">

            <label for="pwd2"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="pwd2">

            <button type="submit">Sign Up</button>
        </div>

    </form>
    _END;

echo <<<_END
    <p>Already a user? <a href="loginUser.php">Log in</a></p>
_END;



//user SIGN UP
if ((isset($_POST['uname2'])) && (isset($_POST['pwd2']))) {

    // sanitizing the same way as login
    $un_temp = mysql_entities_fix_string($conn, $_POST['uname2']);
    $pw_temp = mysql_fix_string($conn, $_POST['pwd2']);

    // password hash adds salt to password and securely hashes it - can be verified by password_verify
    $hash = password_hash($pw_temp, PASSWORD_DEFAULT);

    //store salted and hashed password in a database
    $query = "INSERT INTO users VALUES" .
        "('$un_temp', '$hash')";
    $result = $conn->query($query);
    echo "You just signed up, $un_temp! Login to view your comments.";
    if (!$result) {
        errorFunction();
    }
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
