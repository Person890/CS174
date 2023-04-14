<?php

require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die(errorFunction());

$username;
$hash = $pw_temp = $cookie_username = $cookie_value = $cookie_name = "";


//used following command to create a table credentials:
// create table credentials(
//     -> username varchar(1000) NOT NULL,
//     -> password varchar(1000) NOT NULL,
//     -> CONSTRAINT username_unique UNIQUE(username));


//   login form
echo <<<_END
    <form action="assignment5.php" method="post">
        <div class="container" style="background-color:#ffafcc">
            <h2 style="display: inline;">Login: </h2>
            <label for="uname"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="uname">

            <label for="pwd"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="pwd">

            <button type="submit">Login</button>
        </div>

    </form>
    _END;

// sign up form
echo <<<_END
    <form action="assignment5.php" method="post">
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

//user LOG IN
// check if username and password is present
if ((isset($_POST['uname'])) && (isset($_POST['pwd']))) {

    // destroy existing cookie
    setcookie('username', $username, time() - 295000);
    unset($username);

    // sanitize input
    $un_temp = mysql_entities_fix_string($conn, $_POST['uname']);
    $pw_temp = mysql_fix_string($conn, $_POST['pwd']);

    // search for username in the database 
    $query = "SELECT * FROM credentials WHERE username='$un_temp'";
    $result = $conn->query($query);
    // check if there was a match in the database
    if (mysqli_num_rows($result) != 0) {
        $row = $result->fetch_array(MYSQLI_NUM);
        // verify password (with salt)
        $userverified = password_verify($pw_temp, $row[1]);
        if ($userverified) {
            $username = $un_temp;
            // cookie expires in 5 min
            setcookie('username', $username, time() + (60 * 5));
        } else {
            echo "Invalid username/password.";
        }
    }
} else if (isset($_COOKIE['username'])) {
    // checking if user is still logged in
    $username = $_COOKIE['username'];
}


//  getting USER COMMENTS
if (isset($_COOKIE['username']) && isset($_POST['usersname']) && isset($_POST['comment'])) {

    //  sanitizing the input with htmlentities and real_escape_string
    $usersname = mysql_entities_fix_string($conn, $_POST['usersname']);
    $comment = mysql_entities_fix_string($conn, $_POST['comment']);

    // inserting a comment into database
    $query = "INSERT INTO inputInfo VALUES" .
        "('$username', '$comment', '$usersname')";
    $result = $conn->query($query);
    if (!$result) {
        errorFunction();
    }
}


//user SIGN UP
if ((isset($_POST['uname2'])) && (isset($_POST['pwd2']))) {
    setcookie('username', $username, time() - 295000);
    unset($username);

    // sanitizing the same way as login
    $un_temp = mysql_entities_fix_string($conn, $_POST['uname2']);
    $pw_temp = mysql_fix_string($conn, $_POST['pwd2']);

    // password hash adds salt to password and securely hashes it - can be verified by password_verify
    $hash = password_hash($pw_temp, PASSWORD_DEFAULT);

    //store salted and hashed password in a database
    $query = "INSERT INTO credentials VALUES" .
        "('$un_temp', '$hash')";
    $result = $conn->query($query);
    echo "You just signed up, $un_temp! Login to view your comments.";
    if (!$result) {
        errorFunction();
    }
}

// checking if user is logged in - then displaying personalized message
if (isset($username)) {
    echo "<H1>Hello, " . $username . "</h1>";
    contentform();
    displayTable($conn, $username);
} else {
    echo "<h1>Hello!</h1>";
}


// printing out Content input form
function contentform()
{
    echo <<<_END
        <H3>Add a comment:</H3>
        <form action="assignment5.php" method="post">
        <pre> 
        User's name <input type="text" name="usersname">
        Comment <input type="text" name="comment">
        <input type="submit" value="ADD RECORD"> 
        </pre></form>
        _END;
}

// iterating through the table and displaying all data
function displayTable($conn, $username)
{
    $query = "SELECT * FROM inputInfo where username='$username'";
    $result = $conn->query($query);
    if (!$result) errorFunction();
    else {
        $rows = $result->num_rows;
        echo "<table><tr><th>Name</th><th>Comment</th><th>User's Name</th></tr>";

        for ($j = 0; $j < $rows; ++$j) {
            $result->data_seek($j);
            $row = $result->fetch_array(MYSQLI_NUM);
            echo "<tr>";
            for ($k = 0; $k < 3; ++$k)
                echo "<td>$row[$k]</td>";
            echo "</tr>";
        }
        echo "</table>";
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
