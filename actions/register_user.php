<?php
global $conn; // check this
include "../db/config.php";
    $response = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fname = $_POST['first_name'];
        $lname = $_POST['last_name'];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $confirmpassword = $_POST['confirm'];

        if (empty($fname) ){
            $response["error"] = "Invalid first name";
            echo "<h1>First name is required.</h1>";
            echo '<meta http-equiv="refresh" content="3;url=../view/signup.php">';
        }
        elseif (empty($lname)){
            $response["error"] = "Invalid last name";
            echo "<h1>Last name is required.</h1>";
            echo '<meta http-equiv="refresh" content="3;url=../view/signup.php">';
        }
        elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
            $response["error"] = "Invalid email";
            echo "<h1>Invalid email.</h1>";
            echo '<meta http-equiv="refresh" content="3;url=../view/signup.php">';
        }
        elseif (empty($password) || strlen($password) < 6) {
            $response["error"] = "Password is required";
            echo "<h1>Password is required and should be at least 6 characters long.</h1>";
            // taking the user back to the sign-up page
            echo '<meta http-equiv="refresh" content="3;url=../view/signup.php">';
        }
        elseif ($password !== $confirmpassword) {
            $response['error'] = 'Passwords do not match.';
            echo "<h1>Passwords do not match.</h1>";
            echo '<meta http-equiv="refresh" content="3;url=../view/signup.php">';
        }

        if (!isset($response['error'])){
            try{
                $stmt = $conn->prepare("SELECT user_id from users where email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $response["error"] = "This email is already taken.";
                }
                else{
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    // using bcrypt

                    $role = 2; // common user
                    date_default_timezone_set('UTC');
                    $timestamp = date("Y-m-d H:i:s"); // timestamp for creation

                    $statement = $conn->prepare("INSERT INTO users (email, password_hash, fname, lname, role, registration_date) values (?, ?, ?, ?, ?, ?)");
                    $statement->bind_param("ssssis", $email, $hash, $fname, $lname, $role, $timestamp);

                    if ($statement->execute()) {
                        $response["error"] = "Registration successful.";
                        header("Location: ../view/login.php");
                        exit();
                    }
                    else{
                        $response["error"] = "Registration failed.";
                        var_dump($statement->error);
                        var_dump($response);
                        header("Location: ../view/signup.php");
                    }
                }
            } catch (Exception $e){
                $response["error"] = "Error: " . $e->getMessage();
                var_dump($response);
                header("Location: ../view/signup.php");
            }
        }
    }
    $conn->close();
?>