<?php
//Credit to Muhammed at https://stackoverflow.com/questions/35778218/deleting-session-cookies-with-php
session_start();
//From PHP manual: Unset all of the session variables.
//No need to do in a loop for all $_SESSION[] keys
$_SESSION = array();

//For cookies you do similar, from PHP docs:
//http://php.net/manual/en/function.setcookie.php#73484

if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}
session_destroy();



//Redirect to landing page/login page
header("Location: ../login_form.php");
?>