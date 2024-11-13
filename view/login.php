<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login Page</title>
       <!-- <link rel="stylesheet" href="../pages/css/sign_up_css.css"> -->
    </head>
    <body>
        <h1>
            Quiz Quest
        </h1>

        <div>
            <form method="post" action="../actions/register_user.php" data-parsley-validate>
               <!-- <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Enter Username"> -->
                <label for="useremail">Email</label>
                <input type="email" name="user_email" id="useremail" placeholder="Enter Email" required data-parsley-type="email" data-parsley-trigger="change"
                       data-parsley-pattern="/^(([^<>()\[\]\\.,;:\s@]+(\.[^<>()\[\]\\.,;:\s@]+)*)|(.+))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/"
                       data-parsley-required-message="Email is required." data-parsley-type-message="Please enter a valid email address.">

                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter Password" required data-parsley-minlength="8"
                       data-parsley-pattern="/^(?=.*[A-Z])(?=.*\d.*\d.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/" data-parsley-trigger="keyup" data-parsley-required-message="Password is required." data-parsley-minlength-message="Password must be at least 8 characters long."
                       data-parsley-pattern-message="Password must contain at least one uppercase and one lowercase letter.">

                <!-- Add a remember me -->
                <input type="submit" value="Submit" name="login_submit">
            </form>
        </div>

        <p>Have an account already? <a href="signup.php">Sign Up</a></p>

        <script src="../assets/js/sign_up_js.js"></script>
    </body>
</html>