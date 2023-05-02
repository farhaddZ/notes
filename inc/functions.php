<?php
require_once 'db.php';
session_start();

//add new user
if (isset($_POST['do-register'])) {

    $displayName = $_POST['display-name'];
    $userName = $_POST['username'];
    $password = $_POST['password'];
    $passconf = $_POST['pass-conf'];

    $checkUsers = mysqli_query($db, "SELECT * FROM users WHERE username = '$userName'");

    if (mysqli_num_rows($checkUsers) > 0) {
        setMessage('shoma ghablan sabt nam kardeid');
        header("Location: ../register.php");
    } else {
        if ($_POST['password'] != $_POST['pass-conf']) {
            setMessage('ramz obor va tekrar yeksan nistand');
            header("Location: ../register.php");
        } else {
            $insert = mysqli_query($db, "INSERT INTO users (display_name, username, password) VALUES ('$displayName', '$userName', '$password')");
            if ($insert) {

                //session_start();
                $_SESSION['message'] = 'sabt nam ba movafaghiat anjam shod';

                header("Location: ../login.php");
            } else {
                echo 'error';
            }
        }
    }
}

//do logout
if (isset($_GET['logout'])) {
    //session_start();
    unset($_SESSION['loggedin']);
    header("Location: login.php");
}

// add note
if (isset($_POST['user-note'])) {
    $userNote = $_POST['user-note'];
    $userId = getUserId();

    $addNote = mysqli_query($db, "INSERT INTO notes (notes_text, user_id) VALUES ('$userNote', '$userId') ");

    if ($addNote) {
        header("Location: ../index.php");
    }
}

//check login
if (isset($_POST['do-login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $checkUser = mysqli_query($db, "SELECT * FROM users WHERE username='$username' AND password='$password'");

    if (mysqli_num_rows($checkUser)) {
        //session_start();
        $_SESSION['loggedin'] = $username;
        header("Location: ../index.php");
    } else {
        setMessage('nam karbari ya ramz obor eshtebah ast');
        header("Location: ../login.php");
    }
}

function setMessage($message)
{
    //session_start();
    $_SESSION['message'] = $message;
}

function showMessage()
{
    //session_start();
    if (isset($_SESSION['message'])) {
        echo "<div class='alert alert-warning'>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']);
    }
}

//check log in
function checkLogin()
{
    //session_start();
    if (!isset($_SESSION['loggedin'])) {
        header('Location: login.php');
    }
}

// get user notes
function getUserNotes($limit = false)
{

    global $db;
    $userId = getUserId();

    if ($limit) {
        $getNotes = mysqli_query($db, "SELECT * FROM notes WHERE user_id='$userId' AND is_done='0' ORDER BY id DESC LIMIT $limit");
    } else {
        $getNotes = mysqli_query($db, "SELECT * FROM notes WHERE user_id='$userId' AND is_done='0' ORDER BY id DESC");
    }

    $userNotes = [];
    while ($notes = mysqli_fetch_array($getNotes)) {
        $userNotes[] = $notes;
    }

    return $userNotes;
}

// get users notes
function getDoneNotes()
{
    global $db;
    $userId = getUserId();

    $getNotes = mysqli_query($db, "SELECT * FROM notes WHERE user_id='$userId' AND is_done='1' ORDER BY id DESC");

    $userNotes = [];
    while ($notes = mysqli_fetch_array($getNotes)) {
        $userNotes[] = $notes;
    }

    return $userNotes;
}

// get user id from username
function getUserId()
{
    global $db;
    //session_start();
    $username = $_SESSION['loggedin'];

    $getUser = mysqli_query($db, "SELECT * FROM users WHERE username='$username'");
    $userArray = mysqli_fetch_array($getUser);

    return $userArray['id'];
}

// get user display name
function getUserDisplayName()
{
    global $db;
    $username = $_SESSION['loggedin'];
    $getUser = mysqli_query($db, "SELECT * FROM users WHERE username='$username'");
    $userArray = mysqli_fetch_array($getUser);

    return $userArray['display_name'];
}

// make note done
if (isset($_GET['done'])) {
    $noteId = $_GET['done']; // note id
    $updateNote = mysqli_query($db, "UPDATE notes SET is_done='1' WHERE id='$noteId'");
    if ($updateNote) {
        header("Location: notes.php");
    }
}

// delete done notes
if (isset($_GET['delete'])) {
    $noteId = $_GET['delete'];
    $deleteNote = mysqli_query($db, "DELETE FROM notes WHERE id='$noteId'");
    if ($deleteNote) {
        header("Location: notes.php");
    }
}

// search
if (isset($_GET['search'])) {
    function getSearchResults()
    {
        global $db;
        $searchInput = $_GET['search'];
        $userId = getUserId();
        $search = mysqli_query($db, "SELECT * FROM notes WHERE notes_text LIKE '%$searchInput%' AND user_id ='$userId' AND is_done='0'");

        $searchResults = [];
        while ($result = mysqli_fetch_array($search)) {
            $searchResults[] = $result;
        }
        return $searchResults;
    }
}

// get user data for setting page
function getUserData()
{
    global $db;
    $userId = getUserId();

    $getUserName = mysqli_query($db, "SELECT * FROM users WHERE id='$userId'");

    $userData = mysqli_fetch_array($getUserName);

    return $userData;
}

// update user data
if (isset($_POST['do-update'])) {
    $newDisplayName = $_POST['display-name'];
    $userId = getUserId();
    $newTitle = $_POST['title'];
    $newSubTitle = $_POST['subtitle'];
    $updateSetting = mysqli_query($db, "UPDATE users SET display_name='$newDisplayName', title='$newTitle', subtitle='$newSubTitle' WHERE id='$userId'");

    if ($updateSetting) {
        setMessage('اطلاعات با موفقیت بروزرسانی شد');
        header("Location: ../setting.php");
    }
}