<?php

include "connection.php";
// login module
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    // variables that holds the user's input 
    $email = $_POST["email"];
    $password = $_POST["password"];

    // sql query if the user's information exist in the database
    $sql = "SELECT * FROM tbl_user
            inner join tbl_department on tbl_user.department_id = tbl_department.department_id
            WHERE email = ? AND password = ?";
    $results = $conn->execute_query($sql,[$email, md5($password)]);

    // condition if the informaton exists
    if ($results->num_rows > 0){
        $record = $results->fetch_assoc();
        $_SESSION["id_number"] = $record['id_number'];
        $_SESSION["lastname"] = $record['last_name'];
        $_SESSION["firstname"] = $record['first_name'];
        $_SESSION["email"] = $record['email'];
        $_SESSION["user_id"] = $record['user_id']; 
        $_SESSION["department_name"] = $record['department_name'];

        // checking the user type and enter to different home module
        if ($record['user_type'] == 1 || $record['user_type'] == 2|| $record['user_type'] == 3) {
            header("location: home.php");
        }elseif ($record['user_type'] == 4) {
            header("location: adminhome.php");
        }

    // else the information doesn't exists it return's error message  
    }else{
        echo "<script type='text/javascript'>alert('Invalid email and password');</script>";
    }
}
?>

<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-image: url('ustp.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box {
            background-color: rgba(0, 0, 0, 0.2);
            color: white;
            padding: 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="row ">
        <div class="col-sm-4"></div>
        <div class="col-sm-4 mt-5 login-box">
            <div class="mx-auto text-center">
                <h1>Welcome to USTP helpdesk</h1>
            </div>
            <form action="" method="post">
                <div class="mt-5" style="text-align: right;" >
                <button type="submit" class="btn btn-info">Create Ticket</button>
                <br><br>
                <button type="submit" class="btn btn-info">Check Ticket Status</button>
                </div>
                <br>
                <div class="mx-auto text-center mt-5">
                    <button type="submit" class="btn btn-info">Login</button>
                </div>
               
            </form>
            <div class="mx-auto text-center mt-5"> 
                <p>Visitor | <a href="register.php">Sign in</a></p>
            </div>
        </div>
        <div class="col-sm-4"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-image: url('ustp.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box {
            background-color: rgba(0, 0, 0, 0.36);
            color: white;
            padding: 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 900px; /* Dako ang width */
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-10 mt-5 login-box">
            <div class="row">
                <!-- LEFT SIDE FOR DETAILS -->
                <div class="col-md-6">
                    <h2>Welcome to the SAS Help Desk!</h2>
                    <p>This platform helps you easily submit, track, and get updates on your concerns with the Office of Student Affairs and Services. Whether you’re a student or an anonymous sender, you can submit a ticket, check its status, and communicate with SAS staff — all in one organized place.</p>
                    <ul>
                        <li>Students may submit concerns here.</li>
                        <li>As well as anonymous senders.</li>
                    </ul>
                </div>

                <!-- RIGHT SIDE FOR FORM -->
                <div class="col-md-6">
                    <div class="text-center mb-4">
                        <h3>USTP-Main Helpdesk</h3>
                    </div>
                    <form action="" method="post">
                        <!-- Row for create/check buttons -->
                        <div class="d-flex justify-content-end mb-3 gap-2">
                            <a type="submit" name="create_ticket" class="btn btn-info" href="anon_createticket.php">Create Ticket</a>
                            <a type="submit" name="check_status" class="btn btn-info" href="anon_checking_ticket.php">Check Ticket Status</a>
                        </div>

                        <!-- Email and password inputs -->
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>

                        <!-- Login button center -->
                        <div class="text-center mt-4">
                            <button type="submit" name="login" class="btn btn-info w-50">Login</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p>Visitor | <a href="register.php" class="text-white">Register</a></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-1"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>