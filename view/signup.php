<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="../assets/css/signup_css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
    <style>
        .password-visibility {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-top: 0.5rem;
            width: 100%;
        }

        .password-visibility input[type="checkbox"] {
            width: auto;
            margin-right: 0.5rem;
        }

        .password-visibility label {
            margin-bottom: 0;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
<div class="left_div"> <!--left div-->
    <img src="../assets/images/sign_up_image.jpg" alt="Library" id="login_image_2">
</div>
<div class="right_div">
    <h1 style="font-weight: 300;">
        Welcome To
    </h1>
    <img src="../assets/images/quiz_quest_logo_black.png" alt="Welcome Logo" id="logo">
    <h2>
        Sign Up To Get Started
    </h2>
    <div class="form_container">
        <form method="post" action="../actions/register_user.php" data-parsley-validate>
            <div class="form_element" id="sub_div">
                <label for="fname" class="labels">First Name</label>
                <br>
                <input id="fname" type="text" name="first_name" required data-parsley-required="true" data-parsley-trigger="change"
                       data-parsley-required-message="First Name is required" data-parsley-message="Enter a valid first name" data-parsley-minlength="2">

            </div>
            <div class="form_element" id="sub_div">
                <label for="lname" class="labels">Last Name</label>
                <br>
                <input id="lname" type="text" name="last_name" required data-parsley-required="true" data-parsley-trigger="change"
                       data-parsley-required-message="Last Name is required" data-parsley-message="Enter a valid last name" data-parsley-minlength="2">
            </div>
            <div class="form_element" id="sub_div">
                <label for="useremail" class="labels">Email</label>
                <br>
                <input id="useremail" type="email" name="email" required data-parsley-required="true" data-parsley-type="email" data-parsley-trigger="change"
                       data-parsley-pattern="/^(([^<>()\[\]\\.,;:\s@]+(\.[^<>()\[\]\\.,;:\s@]+)*)|(.+))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/"
                       data-parsley-required-message="Email is required." data-parsley-type-message="Please enter a valid email address.">
            </div>

            <div class="form_element" id="sub_div">
                <label for="password" class="labels">Password</label>
                <br>
                <input id="password" type="password" name="password" required data-parsley-required="true" data-parsley-minlength="8"
                       data-parsley-pattern="/^(?=.*[A-Z])(?=.*\d.*\d.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/" data-parsley-trigger="keyup" data-parsley-required-message="Password is required." data-parsley-minlength-message="Password must be at least 8 characters long."
                       data-parsley-pattern-message="Password must contain at least one uppercase and one lowercase letter.">
                <div class="password-visibility">
                    <input type="checkbox" id="show_password" onclick="togglePasswordVisibility('password')">
                    <label for="show_password">Show Password</label>
                </div>
            </div>

            <div class="form_element" id="sub_div">
                <label for="confirm" class="labels">Confirm Password</label>
                <br>
                <input id="confirm" type="password" name="confirm" required data-parsley-required="true" data-parsley-equalto="#password" data-parsley-trigger="keyup"
                       data-parsley-required-message="Please confirm your password." data-parsley-equalto-message="Passwords do not match.">
                <div class="password-visibility">
                    <input type="checkbox" id="show_confirm_password"
                            onclick="togglePasswordVisibility('confirm')"
                    >
                    <label for="show_confirm_password">Show Password</label>
                </div>
            </div>

            <input id="loginBTN" type="submit" value="Sign Up" name="login_submit">
        </form>
    </div>
    <p>Don't have an account? <a href="login.php" style="color: dodgerblue;">Login</a></p>
</div>

<script>
    function togglePasswordVisibility(inputId) {
        var passwordInput = document.getElementById(inputId);
        var showPasswordCheckbox;

        if (inputId === "show_password") {){
            showPasswordCheckbox = document.getElementById('show_password');
        }
        else if (inputId === "show_confirm_password"){
            showPasswordCheckbox = document.getElementById('show_confirm_password');
        }

        if (showPasswordCheckbox.checked) {
            passwordInput.type = "text";
        } else {
            passwordInput.type = "password";
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Get the form element
        const form = document.getElementById('login-form');

        // Initialize Parsley on the form
        if (form) {
            form.parsley();
        }
    });
</script>
</body>
</html>