<?php

include "connection.php";

$ticket_id = $_GET["id"];

$ticket_sql = "SELECT * FROM tbl_ticket INNER JOIN tbl_categories ON tbl_ticket.category = tbl_categories.category_id WHERE ticket_id = ?";
$ticket_result = $conn->execute_query($ticket_sql, [$ticket_id]);
$ticket_record = $ticket_result->fetch_assoc();

$category_sql = "SELECT * from tbl_categories";
$category_result = $conn->execute_query($category_sql);

$sql_ticket ="SELECT * from tbl_ticket";
$sql_ticket_result = $conn->execute_query($sql_ticket);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $category = $_POST["category"];
    $ticket_status = $_POST["ticket_status"];
    $description = $_POST["description"];
    $date = $_POST["date"];
    $user_id = $_SESSION["user_id"];

    $sql = "UPDATE tbl_ticket SET title = ?, ticket_description = ?, category = ?, ticket_status = 'open', created_at = ?, user_id = ? WHERE ticket_id = ?";
    if ($conn->execute_query($sql, [$title, $description, $category, $date, $user_id, $ticket_id])){
        header("location: ticket.php?id=".$ticket_id);
    }else{
        echo "<script type='text/javascript'>alert('Save Error')</script>";
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
    <?php include "layout/navbar.php"; ?>
    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4 mt-5">
            <div class="mx-auto text-center">
                <h1>Create Ticket</h1>
            </div>
            <form action="" method="post">
                <label for="title">Title:</label>
                <input type="text" class="form-control" name="title" id="title" value="<?php echo $ticket_record["title"]?>">
                <br><br>
                <label for="category">Help Topic:</label>
                <select class="form-select" name="category" id="category">
                    <option value="">--Select--</option>
                    <?php
                        while ($row = $category_result->fetch_assoc()){
                            if ($ticket_record['category'] == $row['category_id']) {
                                echo "<option value=".$row["category_id"]." selected>".$row["category_name"]."</option>";
                            }else{
                                echo "<option value=".$row["category_id"].">".$row["category_name"]."</option>";
                            }
                            
                        }
                    ?>
                </select>
                <br><br>
                <label for="descripton">Description:</label>
                <?php
                    while ($row = $sql_ticket_result->fetch_assoc()) {
                        if ($ticket_record['ticket_description'] == $row['ticket_description']) {
                            echo '<textarea class="form-control" id="description" name="description">' . $row["ticket_description"] . '</textarea>';
                        }
                    }
                ?>
                <br><br>
                <label for="date">Date:</label>
                <input type="date" class="form-control" name="date" id="date" value="<?php echo date('Y-m-d', strtotime($ticket_record['created_at'])); ?>">
                <br><br>
                <div class="mx-auto text-center mt-3">
                    <button type="submit" class="btn btn-info">Submit</button>
                </div>
            </form>
            
        </div>
        <div class="col-sm-4"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>