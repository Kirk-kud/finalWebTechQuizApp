<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login Page</title>
        <!--<link rel="stylesheet" href="../assets/css/login_css.css">-->
    </head>
    <body>
        <h1>
            Welcome to my Quiz Quest
        </h1>

        <h2>
            Sign up to get started
        </h2>

        <div>
            <form method="post" action="../actions/login_user.php" data-parsley-validate>
                <label for="fname">First Name</label>
                <input id="fname" type="text" name="first_name" required data-parsley-trigger="change"
                       data-parsley-required-message="First Name is required" data-parsley-message="Enter a valid first name" data-parsley-minlength="2">
                <br>

                <label for="lname">Last Name</label>
                <input id="lname" type="text" name="last_name" required data-parsley-trigger="change"
                       data-parsley-required-message="Last Name is required" data-parsley-message="Enter a valid last name" data-parsley-minlength="2">
                <br>

                <label for="useremail">Email</label>
                <input id="useremail" type="email" name="email" required data-parsley-type="email" data-parsley-trigger="change"
                       data-parsley-pattern="/^(([^<>()\[\]\\.,;:\s@]+(\.[^<>()\[\]\\.,;:\s@]+)*)|(.+))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/"
                       data-parsley-required-message="Email is required." data-parsley-type-message="Please enter a valid email address.">
                <br>

                <label for="dob">Date Of Birth</label>
                <input id="dob" type="date" name="dob" required>
                <br>

                <label for="password">Password</label>
                <input id="password" type="password" name="password" required data-parsley-minlength="8"
                        data-parsley-pattern="/^(?=.*[A-Z])(?=.*\d.*\d.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/" data-parsley-trigger="keyup" data-parsley-required-message="Password is required." data-parsley-minlength-message="Password must be at least 8 characters long."
                        data-parsley-pattern-message="Password must contain at least one uppercase and one lowercase letter.">

                <br>

                <label for="confirm">Confirm Password</label>
                <input id="confirm" type="password" name="confirm" required data-parsley-equalto="#password" data-parsley-trigger="keyup"
                        data-parsley-required-message="Please confirm your password." data-parsley-equalto-message="Passwords do not match.">
                <br>

                <input type="submit" value="Login" name="login_submit">
            </form>
        </div>

        <p>Don't have an account? <a href="login.php" style="color: dodgerblue;">Login</a></p>
    </body>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
    <script src="../assets/js/login_js.js"></script>
</html>