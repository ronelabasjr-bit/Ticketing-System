<?php

include "connection.php";

$category_sql = "SELECT category_id, category_name FROM ticketing_db.tbl_categories";
$category_result = $conn->execute_query($category_sql);

// if ($_SERVER["REQUEST_METHOD"]=="POST"){
//     $title = $_POST["title"];
//     $category = $_POST["category"];
//     $description = $_POST["description"];
//     $date = $_POST["date"];
//     $user_id = $_SESSION["user_id"];


//     $sql = "INSERT INTO tbl_ticket(title, category, ticket_status, ticket_description, created_at,  user_id) VALUES(?, ?, 'Open', ?, ?, ?)";
//     if ($conn->execute_query($sql, [$title, $category, $description, $date, $user_id])){
//         header("location: home.php");
//     }else{
//         echo "save error";
//     }
// }


if ($_SERVER["REQUEST_METHOD"]=="POST"){
    $name = $_POST["name"];
    $email = $_POST["email"];
    $title = $_POST["title"];
    $category = $_POST["category"];
    $description = $_POST["description"];
    // $date = $_POST["date"];
    // $user_id = $_SESSION["user_id"];

    if (empty($title) || empty($category) || empty($description)) {
        $message = "<p style='color: red;'>Please fill out all the information.</p>";
    } else {
        $sql = "INSERT INTO tbl_anonym_ticket(name, email, title, category_id, ticket_description, created_at) VALUES(?, ?, ?, ?, ?, now())";
        if ($conn->execute_query($sql, [$name, $email, $title, $category, $description])){
            header("location: tstfornew.php");
        }else{
            echo "save error";
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
<body>
    <?php //include "layout/navbar2.php"; <!--?>
    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4 mt-5">
            <div class="mx-auto text-center">
                <h1>Create Ticket</h1>
            </div>
            <form action="" method="post">
                <?php
                if(isset($_POST['submit'])){
                    if (empty($title) || empty($category) || empty($description) || empty($date)) {
                        echo $message;
                    }
                }
                ?><label for="title">Name(optional):</label>
                <input type="text" class="form-control" name="name" id="name">
                <br><br>
                <label for="title">Email(optonal):</label>
                <input type="text" class="form-control" name="email" id="email">
                <br><br>
                <label for="title">Title:</label>
                <input type="text" class="form-control" name="title" id="title">
                <br><br>
                <label for="category">Help Topic:</label>
                <select class="form-select" name="category" id="category">
                    <option value="">--Select--</option>
                    <?php
                        while ($row = $category_result->fetch_assoc()){
                            echo "<option value=".$row["category_id"].">".$row["category_name"]."</option>";
                        }
                    ?>
                </select>
                <br><br>
                <label for="descripton">Description:</label>
                <textarea class="form-control" id="description" name="description" ></textarea>
                <!-- <br><br>
                <label for="date">Date:</label>
                <input type="date" class="form-control" name="date" id="date"> -->
                <br><br>
                <div class="mx-auto text-center mt-3">
                    <button name="submit" type="submit" class="btn btn-info">Submit</button>
                </div>
            </form>
            
        </div>
        <div class="col-sm-4"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>