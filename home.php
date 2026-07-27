<?php

include "connection.php";

$user_id = $_SESSION["user_id"];
$id_number = $_SESSION["id_number"];
$user_name = $_SESSION["firstname"].' '.$_SESSION["lastname"];
$department = $_SESSION["department_name"];
$email = $_SESSION["email"];

// query for user type
$user_sql = "SELECT * from tbl_user WHERE user_id = ?";
$user_result = $conn -> execute_query($user_sql, [$user_id]);
$user_record = $user_result->fetch_assoc();

// search and sort by status
if (isset($_GET["status"])) {
    $status = $_GET["status"];  
    $sql = "SELECT * FROM tbl_ticket
      INNER JOIN tbl_categories ON tbl_ticket.category = tbl_categories.category_id 
      INNER JOIN tbl_user ON tbl_ticket.user_id = tbl_user.user_id 
      WHERE tbl_ticket.user_id = ? AND ticket_status = ?";
    $result = $conn->execute_query($sql, [$user_id, $status]);
}elseif (isset($_GET["search"])) {
    $search_query = $_GET["search"];
    $sql = "SELECT * FROM tbl_ticket 
      INNER JOIN tbl_categories ON tbl_ticket.category = tbl_categories.category_id
      INNER JOIN tbl_user ON tbl_ticket.user_id = tbl_user.user_id 
      WHERE tbl_ticket.user_id = ? and (title LIKE ? or category_name like ?)";
    $result = $conn->execute_query($sql, [$user_id, "%" . $search_query . "%", "%". $search_query ."%"]);
} else {
    $sql = "SELECT * FROM tbl_ticket 
      INNER JOIN tbl_categories ON tbl_ticket.category = tbl_categories.category_id 
      INNER JOIN tbl_user ON tbl_ticket.user_id = tbl_user.user_id 
      WHERE tbl_ticket.user_id = ?";
      $result = $conn->execute_query($sql, [$user_id]);
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
        .min-height {
            min-height: 100vh;
        }
        .notification-dot {
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
<body>
    <?php include "layout/navbar.php"?>
    <div class="row">
        <div class="col-sm-2 p-3 text-primary-emphasis bg-primary-subtle border border-primary-subtle rounded-0 min-height">
            <center>
                <img src="profile.jpg" alt="Profile Image" class="rounded-circle" width="100" height="100">
                <h5><?php echo $user_name ?></h5>
                <h6><?php echo $id_number ?></h6>
                <h6><?php echo $department ?></h6>
                 <?php
                 
                        if ($user_record) {
                            if ($user_record["user_type"] == 1) {
                                echo "<h6>"."Student"."<h6>";
                            } elseif ($user_record["user_type"] == 2) {
                                echo "<h6>"."Faculty"."<h6>";
                            } elseif ($user_record["user_type"] == 3) {
                                echo "<h6>"."Staff"."<h6>";
                            }
                        }
                        
                        
                ?>
            </center>
            <center>
            <a href="edit_profile.php?id=<?php echo $user_id ?>">edit</a>
            </center>
            <br>
            <center>
            <a href="logout.php?id=<?php echo $user_id ?>" class="btn btn-danger">Logout</a>
            </center>
        </div>
        <div class="col-sm-8 ms-5">
            <table class="table table-striped-columns table-hover">
                <thead>
                    <h2 class="mt-3">Tickets:</h2>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Status</th>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        $ticket_id = $row['ticket_id'];

                        // Check if there are unread comments for this ticket
                        $sql_unread_comments = "SELECT COUNT(*) as unread_count FROM tbl_comment WHERE comment_ticket_id = ? AND is_read = 0 AND comment_user_id != ?";
                        $unread_result = $conn->execute_query($sql_unread_comments, [$ticket_id, $user_id]);
                        $unread_count = $unread_result->fetch_assoc()['unread_count'];

                        if ($row["ticket_status"] == "open") {   
                    ?>
                        <tr class="table-primary">
                            <td style="position: relative;">
                                <a href="ticket.php?id=<?php echo $row['ticket_id'] ?>" class="">
                                    <?php echo $row["title"] ?>
                                    <?php if ($unread_count > 0){ ?>
                                        <span class="notification-dot"></span>
                                    <?php } ?>
                                </a>
                            </td>
                            
                            <td><?php echo $row["category_name"] ?></td>
                            <td><?php echo $row["ticket_description"] ?></td>
                            <td><?php echo $row["ticket_status"] ?></td>
                        </tr>

                    <?php
                        }elseif ($row["ticket_status"] == "in progress") {
                    ?>
                        <tr class="table-success">
                            <td style="position: relative;">
                                    <a href="ticket.php?id=<?php echo $row['ticket_id'] ?>" class="">
                                        <?php echo $row["title"] ?>
                                        <?php if ($unread_count > 0){ ?>
                                            <span class="notification-dot"></span>
                                        <?php } ?>
                                    </a>
                            </td>
                            <td><?php echo $row["category_name"] ?></td>
                            <td><?php echo $row["ticket_description"] ?></td>
                            <td><?php echo $row["ticket_status"] ?></td>
                        </tr>
                    <?php      
                        }elseif ($row["ticket_status"] == "closed") {
                    ?>
                        <tr class="table-danger">
                            <td style="position: relative;">
                                <a href="ticket.php?id=<?php echo $row['ticket_id'] ?>" class="">
                                    <?php echo $row["title"] ?>
                                    <?php if ($unread_count > 0){ ?>
                                        <span class="notification-dot"></span>
                                    <?php } ?>
                                </a>
                            </td>
                            <td><?php echo $row["category_name"] ?></td>
                            <td><?php echo $row["ticket_description"] ?></td>
                            <td><?php echo $row["ticket_status"] ?></td>
                        </tr>
                    <?php
                        }else{
                    ?>
                           
                    <?php
                        }      
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm-2"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>