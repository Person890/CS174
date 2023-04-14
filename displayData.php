<?php

require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die(errorFunction());

//declaring variables
$username;
$forename;

// printing out Content input form
function contentform()
{
    echo <<<_END
        <H3>Add content:</H3>
        <form action="displayData.php" method="post">
        <pre> 
        Content Name: <input type="text" name="contentName">
        File Content: <input type="text" name="fileContent">
        <input type="submit" value="ADD RECORD"> 
        </pre></form>
        _END;
}

// form for file upload
function fileDrop()
{
    echo <<<_END
        <H3>Or upload a file:</H3>
        <html><head><title>PHP Form Upload</title></head><body>
        <form method="post" action='displayData.php' enctype='multipart/form-data'> 
        Content Name: <input type="text" name="contentName1">
        <br>
        Select File: <input type='file' name='filename' size='10'>
        <br>
        <input type='submit' value='Upload'>
        </form>
        </body></html>
    _END;
}

// preventing session fixation
session_start();
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id();
    $_SESSION['initiated'] = 1;
}

if (isset($_SESSION['username'])) {
    $username = mysql_entities_fix_string($conn, $_SESSION['username']);
    $forename = mysql_entities_fix_string($conn, $_SESSION['forename']);

    echo "Welcome back $forename.<br>";
    goBack();
    logoutButton();
    contentform();
    fileDrop();
    displayTable($conn, $username);
} else echo "Please <a href='loginUser.php'>click here</a> to log in.";


if (!isset($_SESSION['count'])) $_SESSION['count'] = 0;
else ++$_SESSION['count'];


function goBack()
{
    echo <<<_END
    <form action="loginUser.php" method="post">
            <button type="submit" style="background-color:#f5bac6">Back</button>
    </form>
    _END;
}

// iterating through the table and displaying all data
// newest entries are displayed at the bottom 
function displayTable($conn, $username)
{
    $query = "SELECT COUNT(*) FROM content";
    $result = $conn->query($query);
    if (!$result) errorFunction();
    else {
        $result->data_seek(0);
        $row = $result->fetch_array(MYSQLI_NUM);
        if ($row[0] >= 4 && !isset($_POST['showMore'])) {
            $query = "SELECT * FROM content where username='$username' LIMIT 0, 3";
            $result = $conn->query($query);
            if (!$result) errorFunction();
            else {
                printTable($result);
            }
            showMore();
        } else if ($row[0] >= 4 && isset($_POST['showMore'])) {
            $query = "SELECT * FROM content where username='$username'";
            $result = $conn->query($query);
            if (!$result) errorFunction();
            else {
                printTable($result);
            }
        }
    }
}

function printTable($result)
{
    $rows = $result->num_rows;
    echo "<table><tr><th>Content Name</th><th>&emsp; | &emsp;File Content</th></tr>";

    for ($j = 0; $j < $rows; ++$j) {
        $result->data_seek($j);
        $row = $result->fetch_array(MYSQLI_NUM);
        echo "<tr>";
        for ($k = 1; $k < 3; ++$k) {
            echo "<td>&emsp; | &emsp;$row[$k]</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
//  getting USER COMMENTS from text box
if (isset($_SESSION['username']) && isset($_POST['contentName']) && isset($_POST['fileContent'])) {

    //  sanitizing the input with htmlentities and real_escape_string
    $username = mysql_entities_fix_string($conn, $_SESSION['username']);
    $contentName = mysql_entities_fix_string($conn, $_POST['contentName']);
    $fileContent = mysql_entities_fix_string($conn, $_POST['fileContent']);

    // inserting a content into database
    $query = "INSERT INTO content VALUES" .
        "('$username', '$contentName', '$fileContent')";
    $result = $conn->query($query);
    if (!$result) {
        errorFunction();
    }
}


//  getting USER COMMENTS from file
if (isset($_SESSION['username']) && isset($_POST['contentName1'])) {
    //  sanitizing the input with htmlentities and real_escape_string
    $username = mysql_entities_fix_string($conn, $_SESSION['username']);
    $contentName = mysql_entities_fix_string($conn, $_POST['contentName1']);
    $fileContent = mysql_entities_fix_string($conn, $_POST['fileContent']);

    // opening the uploaded file
    if ($_FILES) {
        // sanitizing the name of the file 
        $name = mysql_entities_fix_string($conn, $_FILES['filename']['name']);

        // checking if the uploaded file is in the correct format
        // only text file allowed
        switch ($_FILES['filename']['type']) {
            case 'text/plain':
                $ext = 'txt';
                break;
            default:
                $ext = '';
                break;
        }
        if ($ext) {
            echo "<p>File uploaded sucessfully!</p>";
        } else {
            echo "Invalid input file.";
            die;
        }

        $fh = fopen(($_FILES['filename']['tmp_name']), 'r') or die("Failed to open file<br>");

        // Implement a PHP function that reads in input a string from the user and store it in a table (in a field called "Name")
        if ($_FILES) {
            // saving and sanitizing the input from the file first with htmlentities and then with real_escape_string
            $fileContent = file_get_contents($_FILES['filename']['tmp_name']);
            $fileContent = mysql_entities_fix_string($conn, $fileContent);
            $fileContent = explode("\n", $fileContent);
            // inserting values into the database
            for ($i = 0; $i < count($fileContent); $i++) {
                $query = "INSERT INTO content VALUES" .
                    "('$username', '$contentName', '$fileContent[$i]')";
                $result = $conn->query($query);
                if (!$result) {
                    errorFunction();
                }
            }
        }
        // closing open file pointer
        fclose($fh);
    }
}

// print out generic error message
function errorFunction()
{
    echo "Ooops, there was an error.";
}

function logoutButton()
{
    echo <<<_END
    <form action="loginUser.php" method="get">
        <input type="submit" style="background-color:#8fa3b6; color:white" name="logout" value="Log Out"> 
        </form>
    _END;
}

// button to display the full length of the comments
function showMore()
{
    echo <<<_END
    <form action="displayData.php" method="post">
        <pre> 
        <input type="submit" name="showMore" value="Show more"> 
        </pre></form>
    _END;
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
// closing the connection
$result->close();
$conn->close();

function get_post($conn, $var)
{
    return $conn->real_escape_string($_POST[$var]);
}
