<style>
    <?php include 'main.css'; ?>
</style>

<?php

require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die(errorFunction());

$studentid;

contentform();

// printing out Content input form
function contentform()
{
    echo <<<_END
    <div>
        <form action="secondPage.php" method="post">
            <input type="submit" class="submitBtn" style=" display: flex; background-color: 2a9d8f; margin: 20px; box-shadow: 0 10px 25px rgba(92, 99, 105, .2); " value="Log In">
        </form>
    </div>
        
    <div class="searchForm">
        <form action="firstPage.php" class="form" method="post">
            <h1 class="title">Search Form</h1>

            <div class="inputContainer">
                <input type="text" class="input" placeholder="a">
                <label for="name" class="label">Name</label>
            </div>

            <div class="inputContainer">
                <input type="text" class="input" placeholder="a">
                <label for="studentid" class="label">10-digit Student ID</label>
            </div>
            <input style="background-color: e76f51;" type="submit" class="submitBtn" value="Search">
        </form>
    </div>

_END;
}


// preventing session fixation
session_start();
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id();
    $_SESSION['initiated'] = 1;
}


if (isset($_SESSION['studentid'])) {
    $studentid = mysql_entities_fix_string($conn, $_SESSION['studentid']);
    echo "got here**";

    echo "Welcome back.<br>";
    goBack();
    logoutButton();
    contentform();
    fileDrop();
    displayTable($conn, $studentid);
} else echo "Please <a href='secondPage.php'>click here</a> to log in.";


if (!isset($_SESSION['count'])) $_SESSION['count'] = 0;
else ++$_SESSION['count'];


function goToSecondPage()
{
    echo <<<_END
    <form action="secondPage.php" method="post">
            <button type="submit" style="background-color:#f5bac6">Go To Login Page</button>
    </form>
    _END;
}

// iterating through the table and displaying all data
// newest entries are displayed at the bottom 
function displayTable($conn, $username)
{
    $query = "SELECT COUNT(*) FROM advisors WHERE ID BETWEEN lowerBoundID AND upperBoundID; ";
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
    echo <<<_END
        <table class="styled-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Points</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Dom</td>
                <td>6000</td>
            </tr>
            <tr class="active-row">
                <td>Melissa</td>
                <td>5150</td>
            </tr>
            <!-- and so on... -->
        </tbody>
    </table>
    _END;

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

// searching for advisor
// if (isset($_SESSION['name']) && isset($_POST['contentName']) && isset($_POST['fileContent'])) {

//     //  sanitizing the input with htmlentities and real_escape_string
//     $username = mysql_entities_fix_string($conn, $_SESSION['username']);
//     $contentName = mysql_entities_fix_string($conn, $_POST['contentName']);
//     $fileContent = mysql_entities_fix_string($conn, $_POST['fileContent']);

//     // inserting a content into database
//     $query = "INSERT INTO content VALUES" .
//         "('$username', '$contentName', '$fileContent')";
//     $result = $conn->query($query);
//     if (!$result) {
//         errorFunction();
//     }
// }


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
