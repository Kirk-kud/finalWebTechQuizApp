<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="../assets/css/signup_css.css">
    <style>
        .form-control {
            margin-bottom: 10px;
            padding-bottom: 20px;
            position: relative;
        }

        .form-control.success input {
            border-color: #2ecc71;
        }

        .form-control.error input {
            border-color: #e74c3c;
        }

        .form-control small {
            color: #e74c3c;
            position: absolute;
            bottom: 0;
            left: 0;
            visibility: hidden;
        }

        .form-control.error small {
            visibility: visible;
        }

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
<div class="left_div">
    <img src="../assets/images/sign_up_image.jpg" alt="Library" id="login_image_2">
</div>
<div class="right_div">
<!--    <h1 style="font-weight: 300;">-->
<!--        Welcome To-->
<!--    </h1>-->
    <img src="../assets/images/quiz_quest_logo_black.png" alt="Welcome Logo" id="logo">
    <h2>
        Sign Up To Get Started
    </h2>
    <div class="form_container">
        <form id="signup-form" method="post" action="../actions/register_user.php">
            <div class="form_element form-control">
                <label for="fname" class="labels">First Name</label>
                <br>
                <input id="fname" type="text" name="first_name" required>
                <small></small>
            </div>

            <div class="form_element form-control">
                <label for="lname" class="labels">Last Name</label>
                <br>
                <input id="lname" type="text" name="last_name" required>
                <small></small>
            </div>

            <div class="form_element form-control">
                <label for="useremail" class="labels">Email</label>
                <br>
                <input id="useremail" type="email" name="email" required>
                <small></small>
            </div>

            <div class="form_element form-control">
                <label for="password" class="labels">Password</label>
                <br>
                <input id="password" type="password" name="password" required>
                <small></small>
                <div class="password-visibility">
                    <input  style="margin-bottom: 0.6rem;" type="checkbox" id="show_password" onclick="togglePasswordVisibility()">
                    <label style="margin-bottom: 0.6rem; margin-top: 0.2rem;" for="show_password">Show Password</label>
                </div>
            </div>

            <div class="form_element form-control">
                <label for="confirm" class="labels">Confirm Password</label>
                <br>
                <input id="confirm" type="password" name="confirm" required>
                <small></small>
                <div class="password-visibility">
                    <input type="checkbox" id="show_confirm_password" onclick="togglePasswordConfirmVisibility()">
                    <label for="show_confirm_password">Show Password</label>
                </div>
            </div>

            <input id="loginBTN" type="submit" value="Sign Up" name="login_submit">
        </form>
    </div>
    <p>Already have an account? <a href="login.php" style="color: dodgerblue;">Login</a></p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('signup-form');
        const firstNameInput = document.getElementById('fname');
        const lastNameInput = document.getElementById('lname');
        const emailInput = document.getElementById('useremail');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm');

        function showError(input, message) {
            const formControl = input.parentElement;
            formControl.className = 'form_element form-control error';
            const small = formControl.querySelector('small');
            small.innerText = message;
        }

        function showSuccess(input) {
            const formControl = input.parentElement;
            formControl.className = 'form_element form-control success';
        }

        function checkEmail(input) {
            const re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-z]{2,6}$/;
            return re.test(input.value.trim());
        }

        function checkPassword(input) {
            const re = /^(?=.*[A-Z])(?=(?:.*\d){3})(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/;
            return re.test(input.value);
        }

        function validateForm(e) {
            let isValid = true;

            // Clear all previous errors
            document.querySelectorAll('.form-control').forEach(control => {
                control.className = 'form_element form-control';
            });

            // Check for empty fields
            if (firstNameInput.value.trim() === '') {
                showError(firstNameInput, 'First name is required');
                isValid = false;
            } else if (firstNameInput.value.trim().length < 2) {
                showError(firstNameInput, 'First name must be at least 2 characters');
                isValid = false;
            } else {
                showSuccess(firstNameInput);
            }

            if (lastNameInput.value.trim() === '') {
                showError(lastNameInput, 'Last name is required');
                isValid = false;
            } else if (lastNameInput.value.trim().length < 2) {
                showError(lastNameInput, 'Last name must be at least 2 characters');
                isValid = false;
            } else {
                showSuccess(lastNameInput);
            }

            if (emailInput.value.trim() === '') {
                showError(emailInput, 'Email is required');
                isValid = false;
            } else if (!checkEmail(emailInput)) {
                showError(emailInput, 'Please enter a valid email address');
                isValid = false;
            } else {
                showSuccess(emailInput);
            }

            if (passwordInput.value === '') {
                showError(passwordInput, 'Password is required');
                isValid = false;
            } else if (!checkPassword(passwordInput)) {
                showError(passwordInput, 'Password must be at least 8 characters long, contain at least one uppercase letter, three digits, and one special character');
                isValid = false;
            } else {
                showSuccess(passwordInput);
            }

            if (confirmPasswordInput.value === '') {
                showError(confirmPasswordInput, 'Please confirm your password');
                isValid = false;
            } else if (passwordInput.value !== confirmPasswordInput.value) {
                showError(confirmPasswordInput, 'Passwords do not match');
                isValid = false;
            } else {
                showSuccess(confirmPasswordInput);
            }

            if (!isValid) {
                e.preventDefault();
            }

            return isValid;
        }

        // Add form submit event listener
        form.addEventListener('submit', validateForm);

        // Real-time validation
        emailInput.addEventListener('blur', function() {
            if (emailInput.value.trim() !== '' && !checkEmail(emailInput)) {
                showError(emailInput, 'Please enter a valid email address');
            } else if (emailInput.value.trim() !== '') {
                showSuccess(emailInput);
            }
        });

        passwordInput.addEventListener('blur', function() {
            if (passwordInput.value !== '' && !checkPassword(passwordInput)) {
                showError(passwordInput, 'Password must be at least 8 characters long, contain at least one uppercase letter, three digits, and one special character');
            } else if (passwordInput.value !== '') {
                showSuccess(passwordInput);
            }
        });

        confirmPasswordInput.addEventListener('blur', function() {
            if (confirmPasswordInput.value !== '' && passwordInput.value !== confirmPasswordInput.value) {
                showError(confirmPasswordInput, 'Passwords do not match');
            } else if (confirmPasswordInput.value !== '') {
                showSuccess(confirmPasswordInput);
            }
        });
    });

    function togglePasswordVisibility() {
        var passwordInput = document.getElementById("password");
        var showPasswordCheckbox = document.getElementById("show_password");
        passwordInput.type = showPasswordCheckbox.checked ? "text" : "password";
    }

    function togglePasswordConfirmVisibility() {
        var passwordInput = document.getElementById("confirm");
        var showPasswordCheckbox = document.getElementById("show_confirm_password");
        passwordInput.type = showPasswordCheckbox.checked ? "text" : "password";
    }
</script>
</body>
</html>