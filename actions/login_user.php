<?php
    session_start();

    require_once "../db/config.php";
    $response = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = htmlspecialchars($_POST["user_email"]);
        $password = htmlspecialchars($_POST["password"]);

        if ((empty($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
            $response['error'] = 'A valid email is needed';
        }
        else if (empty($password) || strlen($password) < 6){
            $response['error'] = 'Password required';
        }
        else{
            try{
                $stmt = $conn->prepare("SELECT user_id, fname, lname, password_hash, role FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if (stmt->num_rows > 0) {
                    stmt->bind_result($user_id, $fname, $lname, $hash, $role);
                    stmt->fetch();

                    if (password_verify($password, $hash)) {
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['fname'] = $fname;
                        $_SESSION['lname'] = $lname;
                        $_SESSION['role'] = $role;

                        // Checking for the role of the user
                        if ($role == 1) {
                            // this is a super admin
                        } elseif ($role == 2) {
                            // this is a regular admin
                        } elseif ($role == 3) {
                            // this is a user
                        }

                        $response['success'] = 'Login successful';
                        header("Location: dashboard.php");
                    } else {
                        $response['error'] = 'Invalid email, try again';
                    }
                }
            }
            catch(Exception $e){
                $response['error'] = $e->getMessage();
            }
        }
    }
?>
