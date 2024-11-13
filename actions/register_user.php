<?php
global $conn; // check this
include "../db/config.php";
    $response = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fname = $_POST['first_name'];
        $lname = $_POST['last_name'];
        $email = $_POST["email"];
        $dob = $_POST['dob'];
        $password = $_POST["password"];
        $confirmpassword = $_POST['confirm'];

        if (empty($fname) ){
            $response["error"] = "Invalid first name";
        }
        elseif (empty($lname)){
            $response["error"] = "Invalid last name";
        }
        elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
            $response["error"] = "Invalid email";
        }
        elseif (empty(dob)){
            $response["error"] = "Invalid date of birth";
        }
        elseif (empty($password) || strlen($password) < 6) {
            $response["error"] = "Password is required";
        }
        elseif ($password !== $confirmpassword) {
            $response['error'] = 'Passwords do not match.';
        }

        if (!isset($response['error'])){
            try{
                $stmt = $conn->prepare("SELECT id from users where email = ?");
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
                    $timestamp = time("Y-m-d H:i:s"); // timestamp for creation

                    $statement = $conn->prepare("INSERT INTO users (first_name, last_name, email, dob, password, role, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?)");
                    $statement->bind_param("sssssiss", $fname, $lname, $email, $dob, $hash, $role, $timestamp);

                    if ($statement->execute()) {
                        $response["error"] = "Registration successful.";
                        header("Location: view/login.php");
                        exit();
                    }
                    else{
                        $response["error"] = "Registration failed.";
                    }
                }
            } catch (Exception $e){
                $response["error"] = "Error: " . $e->getMessage();
            }
        }

    }
    $conn->close();
?>