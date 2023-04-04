<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form action="login_check.php" method="post">
    <p>Dexter Dark's login: dexter203 password</p>
    <div class="container">
        <label for="username"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="username" required><br>

        <label for="password"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="password" required><br>

        <button type="submit">Login</button>
        <label>
        <input type="checkbox" checked="checked" name="remember"> Remember me
        </label>
    </div>

    <div class="container" style="background-color:#f1f1f1">
        <button type="button" class="cancelbtn">Cancel</button>
        <span class="password">Forgot <a href="#">password?</a></span>
    </div>
    </form> 
</body>
</html>