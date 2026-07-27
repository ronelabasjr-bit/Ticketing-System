<?php

include "connection.php";

$department_sql = "SELECT department_id, department_name FROM ticketing_db.tbl_department";
$department_result = $conn->execute_query($department_sql);

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    $id_number = $_POST["id_number"];
    $last_name = $_POST["lastname"];
    $first_name = $_POST["firstname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $department = $_POST["department"];
    $user_type = $_POST["user_type"];

    if (empty($id_number) || empty($last_name) || empty($first_name) || empty($email) || empty($password) || empty($department) || empty($user_type)){  
        $message = "<p style='color: red;'>please fill in all the information.</p>";
    } else {
        $sql = "INSERT INTO tbl_user(id_number, last_name, first_name, email, password, department_id, user_type) VALUES(?, ?, ?, ?, ?, ?,?)";
        if ($conn->execute_query($sql, [$id_number, $last_name, $first_name, $email, md5($password), $department, $user_type])){
            header("location: loginpage.php");
        }else{
            echo "Save error";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    body {
        background-image: url('regImg.jpg');
        background-size: cover;
        background-position: center;
        min-height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        }
    .login-box {
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 40px;
        border-radius: 8px;
        width: 100%;
        max-width: 600px;
    }
    </style>
<body>
    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4 mt-5 login-box">
            <div class="mx-auto text-center">
                <h1>Register Account</h1>
            </div>
            <form action="" method="post">
            <?php
                if(isset($_POST['submit'])){
                    if (empty($id_number) || empty($last_name) || empty($first_name) || empty($email) || empty($password) || empty($department) || empty($user_type)){  
                        echo $message;
                    }
                }
            ?>
                <label for="id_number">ID Number:</label>
                <input type="number" class="form-control" name="id_number" id="id_number">
                <br><br>
                <label for="lastname">Lastname:</label>
                <input type="text" class="form-control" name="lastname" id="lastname">
                <br><br>
                <label for="firstname">Firstname:</label>
                <input type="text" class="form-control" name="firstname" id="firstname">
                <br><br>
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" id="email">
                <br><br>
                <label for="password">Password:</label>
                <input type="password" class="form-control" name="password" id="password">
                <br><br>
                <label for="user_type">Department:</label>
                <select class="form-select" name="department" id="department">
                    <option value="">--Select--</option>
                    <?php
                        while ($row = $department_result->fetch_assoc()){
                            echo "<option value=".$row["department_id"].">".$row["department_name"]."</option>";
                        }
                    ?>
                </select>
                <br><br>
                <label for="user_type">User Type:</label>
                <select class="form-select" id="user_type" name="user_type">
                <option value="">-- Select --</option>
                <option value="1">Student</option>
                <option value="2">Faculty</option>
                <option value="3">Staff</option>
                </select>
                <br><br>
                <div class="mx-auto text-center mt-3">
                    <button name="submit" type="submit" class="btn btn-info">Register</button>
                </div>
                
            </form>
            <div class="mx-auto text-center mt-5"> 
                <p>Already have an accounnt? <a href="loginpage.php">Login</a></p>
            </div>
        </div>
        <div class="col-sm-4"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>