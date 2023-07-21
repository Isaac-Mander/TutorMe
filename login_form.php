<?php
//Check if an error msg has appeared
$error_msg = "";
if(isset($_GET['error']))
{
    $error_msg = $_GET['error'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="sys_page/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"> 

</head>
<body>
    <?php
    //Add the header
    include("sys_page/header.html");
    ?>
    <p class="error_msg"><?php echo $error_msg; ?></p>

<div id="login">
        <h3 class="text-center text pt-5"></h3>
        <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center">
                <div id="login-column" class="col-md-6">
                    <div id="login-box" class="col-md-12">
                        <form id="login-form" class="form" action="login_check.php" method="post">
                            <h3 class="text-center text">Student Login</h3>
                            <div class="form-group">
                                <label for="username" class="text">Username:</label><br>
                                <input type="text" placeholder="Enter Username" name="username" id="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password" class="text">Password:</label><br>
                                <input type="text" autocomplete="off" placeholder="Enter Password" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="remember" class="text"><span>Remember me</span> <span><input id="remember-me" checked="checked" name="remember" type="checkbox"></span></label><br>
                                <input type="submit" name="submit" class="btn btn-success btn-md" value="submit">
                            </div>
                            <div id="form-group" class="text-right">
                                <a href="signup_form_1.html" class="text">Sign up form</a>
                                <span class="password">Forgot <a href="#">password?</a></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <script src="content.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script> 
</body>
</html>