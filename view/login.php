<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Arial Rounded MT Bold", Arial, sans-serif;
            min-height: 100vh;
            overflow: hidden;
        }

        .container {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        .left_div {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            transform: scale(0.85);
        }

        .login-content {
            width: 100%;
            max-width: 450px;
        }

        .top_bar {
            text-align: center;
            margin-bottom: 2rem;
        }

        #logo {
            max-width: 200px;
            height: auto;
        }

        .welcome-text {
            font-size: 2.5rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 1.5rem;
            font-weight: 400;
            text-align: center;
            margin-bottom: 2rem;
            color: #666;
        }

        .form_container {
            width: 100%;
        }

        .login_form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form_elements {
            margin-bottom: 1.5rem;
            width: 100%;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #333;
        }

        input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }

        .submit-btn {
            width: 50%;
            padding: 1rem;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 0 auto;
            display: block;
        }

        .submit-btn:hover {
            background-color: #357abd;
        }

        .signup-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            width: 100%;
        }

        .signup-link a {
            color: #4a90e2;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .right_div {
            width: 50%;
            height: 100vh;
            overflow: hidden;
        }

        #login_image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left_div {
                width: 100%;
                height: auto;
                padding: 1.5rem;
                order: 2;
            }

            .right_div {
                width: 100%;
                height: 30vh;
                order: 1;
            }

            .welcome-text {
                font-size: 2rem;
            }

            .subtitle {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="left_div">
        <div class="login-content">
            <div class="top_bar">
                <img src="../assets/images/quiz_quest_logo_black.png" alt="Welcome Logo" id="logo">
            </div>

            <h1 class="welcome-text">Welcome Back,</h1>
            <h2 class="subtitle">take a seat!</h2>

            <div class="form_container">
                <form class="login_form" method="post" action="../actions/login_user.php" data-parsley-validate>
                    <div class="form_elements">
                        <label for="useremail">Email</label>
                        <input
                                type="email"
                                name="user_email"
                                id="useremail"
                                placeholder="Enter Email"
                                required
                                data-parsley-required="true"
                                data-parsley-type="email"
                                data-parsley-trigger="change"
                                data-parsley-pattern="/^(([^<>()\[\]\\.,;:\s@]+(\.[^<>()\[\]\\.,;:\s@]+)*)|(.+))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/"
                                data-parsley-required-message="Email is required."
                                data-parsley-type-message="Please enter a valid email address."
                        >
                    </div>

                    <div class="form_elements">
                        <label for="password">Password</label>
                        <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="Enter Password"
                                required
                                data-parsley-required="true"
                                data-parsley-minlength="8"
                                data-parsley-pattern="/^(?=.*[A-Z])(?=.*\d.*\d.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/"
                                data-parsley-trigger="keyup"
                                data-parsley-required-message="Password is required."
                                data-parsley-minlength-message="Password must be at least 8 characters long."
                                data-parsley-pattern-message="Password must contain at least one uppercase and one lowercase letter."
                        >
                    </div>

                    <button type="submit" name="login_submit" class="submit-btn">Login</button>

                    <p class="signup-link">
                        Don't have an account? <a href="signup.php">Sign Up</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <div class="right_div">
        <img src="../assets/images/welcome_back.jpg" alt="Login Image" id="login_image">
    </div>
</div>

<script src="../assets/js/sign_up_js.js"></script>
</body>
</html>