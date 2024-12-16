<?php
    session_start();

    require_once "../db/config.php";
    $response = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = htmlspecialchars($_POST["user_email"]);
        $password = htmlspecialchars($_POST["password"]);

        if ((empty($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
            $response['error'] = 'A valid email is needed';
            echo "<h1>A valid email is needed.</h1>";
            echo '<meta http-equiv="refresh" content="3;url=../view/login.php">'; // taking the user back to the login page
        }
        else if (empty($password) || strlen($password) < 6){
            $response['error'] = 'Password required';
            echo "<h1>Password required and should be at least 6 characters.</h1>";
            echo '<meta http-equiv="refresh" content="3;url=../view/login.php">'; // taking the user back to the login page
        }
        else{
            try{
                $stmt = $conn->prepare("SELECT user_id, fname, lname, password_hash, role FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($user_id, $fname, $lname, $hash, $role);
                    $stmt->fetch();

                    if (password_verify($password, $hash)) {
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['fname'] = $fname;
                        $_SESSION['lname'] = $lname;
                        $_SESSION['role'] = $role;

                        // Checking for the role of the user
                        if ($role == 1) {
                            // this is a super admin
                            $response['success'] = 'Login successful';
                            header("Location: ../view/admin/dashboard.php");
                        } elseif ($role == 2) {
                            // this is a regular user
                            $response['success'] = 'Login successful';
                            header("Location: ../index.php");
                        } elseif ($role == 3) {
                            // this is a user
                        }


                    } else {
                        $response['error'] = 'Invalid email or password, try again';
                        echo "<h1 style='text-align: center;'>Invalid email or password, try again.</h1>";
                        echo '<meta http-equiv="refresh" content="2;url=../view/login.php">'; // taking the user back to the login page
                    }
                }
            }
            catch(Exception $e){
                $response['error'] = $e->getMessage();
                header("Location: ../view/login.php?error");
            }
        }
    }
?>
