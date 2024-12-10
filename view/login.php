<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login Page</title>
       <link rel="stylesheet" href="../assets/css/login_css.css">
    </head>
    <body>
    <div class="container">
        <div class="left_div">
            <div class="top_bar">

                    <img src="../assets/images/quiz_quest_logo_black.png" alt="Welcome Logo" id="logo">

            </div>

            <div class="form_container">
                <form class=login_form" method="post" action="../actions/login_user.php" data-parsley-validate>
                   <!-- <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter Username"> -->
                    <div class="form_elements" id="sub_div">
                        <label for="useremail">Email</label>
                        <input type="email" name="user_email" id="useremail" placeholder="Enter Email" required data-parsley-required="true" data-parsley-type="email" data-parsley-trigger="change"
                               data-parsley-pattern="/^(([^<>()\[\]\\.,;:\s@]+(\.[^<>()\[\]\\.,;:\s@]+)*)|(.+))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/"
                               data-parsley-required-message="Email is required." data-parsley-type-message="Please enter a valid email address.">
                        <br>
                    </div>

                    <div class="form_elements" id="sub_div">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Enter Password" required data-parsley-required="true" data-parsley-minlength="8"
                               data-parsley-pattern="/^(?=.*[A-Z])(?=.*\d.*\d.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/" data-parsley-trigger="keyup" data-parsley-required-message="Password is required." data-parsley-minlength-message="Password must be at least 8 characters long."
                               data-parsley-pattern-message="Password must contain at least one uppercase and one lowercase letter.">
                        <br>
                    </div>

                    <!-- Add a remember me -->
                    <div id="sub_div">
                        <button type="submit" name="login_submit">Submit</button>
                    </div>


                    <p id="already">Have an account already? <a style="color: dodgerblue" href="signup.php">Sign Up</a></p>
                </form>
            </div>
        </div>
        <div class="right_div">
            <img src="../assets/images/login_image_2.jpg" alt="Login Image" id="login_image">
        </div>


        <script src="../assets/js/sign_up_js.js"></script>
    </body>
</html>