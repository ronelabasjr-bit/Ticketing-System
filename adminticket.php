<?php

include "connection.php";

$ticket_id = $_GET["id"];
$user_id = $_SESSION["user_id"];
$name = $_SESSION["lastname"] . ", " . $_SESSION["firstname"];

$sql_mark_read="UPDATE tbl_comment 
                SET is_read = 1 
                WHERE comment_ticket_id = ? 
                AND is_read = 0 
                AND comment_user_id != ?;
                ";
$conn->execute_query($sql_mark_read, [$ticket_id, $user_id]);

$sql_comment = "SELECT * from ticketing_db.tbl_comment inner join ticketing_db.tbl_user on tbl_comment.comment_user_id = tbl_user.user_id where comment_ticket_id = ? order by created_at asc";
$comment_result = $conn->execute_query($sql_comment, [$ticket_id]);

$sql_attachment = "SELECT * from tbl_attachment WHERE ticket_id = ?";
$attachment_result = $conn->execute_query($sql_attachment, [$ticket_id]);

$sql = "SELECT * FROM tbl_ticket 
        INNER JOIN tbl_categories ON tbl_ticket.category = tbl_categories.category_id 
        inner join tbl_user on tbl_ticket.user_id = tbl_user.user_id
        inner join tbl_department on tbl_user.department_id = tbl_department.department_id
        WHERE ticket_id = ?";
$result = $conn->execute_query($sql, [$ticket_id]);
$record = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    $ticket_id = $_GET["id"];
    $user_id = $_SESSION["user_id"];
    $comment = $_POST["comment"];

    $sql = "INSERT INTO tbl_comment(comment_ticket_id, comment_user_id, content, created_at) VALUES(?, ?, ?, current_timestamp())";
    if ($conn->execute_query($sql, [$ticket_id, $user_id, $comment])){
        header("location: adminticket.php?id=". $ticket_id);
    }else{
        echo "Save error";
    }
}
$sql_status = "SELECT * FROM tbl_status";
$result_status = $conn->execute_query($sql_status);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    .min-height {
        min-height: 100vh;
    }
</style>

<body>
    <?php include "layout/adminnavbar.php" ?>
    <div class="row">
        <div class="col-sm-6 text-primary-emphasis bg-primary-subtle border border-primary-subtle rounded-0 max-height">
            <div class="mt-5 ms-5">
                <h1>Ticket Details</h1>
                <p><strong>Name:</strong> <?php echo $record["last_name"] . ', ' . $record["first_name"]; ?></p>
                <p><strong>ID Number:</strong> <?php echo $record["id_number"]; ?></p>
                <p><strong>User Type:</strong>
                    <?php
                    if ($record["user_type"] == 1) {
                        echo "Student";
                    } elseif ($record["user_type"] == 2) {
                        echo "Faculty";
                    } elseif ($record["user_type"] == 3) {
                        echo "Staff";
                    }
                    ?>
                </p>
                <p><strong>Department:</strong> <?php echo $record["department_name"]; ?></p>
                <strong>Ticket Number</strong> <?php echo $record["ticket_id"]; ?></p>
                <strong>Title:</strong> <?php echo $record["title"]; ?></p>
                <p><strong>Help Topic:</strong> <?php echo $record["category_name"]; ?></p>
                <p><strong>Description:</strong> <?php echo $record["ticket_description"]; ?></p>
                <p><strong>Date Created:</strong> <?php echo $record["created_at"]; ?></p>
                <p><strong>Uploaded file:</strong></p>
                <?php
                // for file display
                if ($attachment_result->num_rows > 0) {
                    while ($attachment = $attachment_result->fetch_assoc()) {
                        $uploaded_filename = $attachment["file_path"];
                        
                        if ($uploaded_filename) {
                            $file_extension = pathinfo($uploaded_filename, PATHINFO_EXTENSION);
                
                            if (in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                                echo "<div>";
                                echo "<a href='$uploaded_filename'>";
                                echo "<img src='$uploaded_filename' alt='' width='200' height='200'>";
                                echo "</a>";
                                echo "<p>" . "Date Uploaded: " . "<small>" . $attachment["uploaded_at"] . "</small>" ."</p>";
                                echo "</div>";
                            }
                            elseif (in_array(strtolower($file_extension), ['pdf', 'doc', 'docx'])) {
                                echo "<div>";
                                echo "<a href='$uploaded_filename' >".$uploaded_filename."</a>";
                                echo "</a>";
                                echo "<p>" . "Date Uploaded: " . "<small>" . $attachment["uploaded_at"] . "</small>" ."</p>";
                                echo "</div>";
                            }
                        } 
                    }
                }
                ?>

                <!--Admin can update the ticket status-->
                <form action="updatestatus.php" method="post" class="mt-auto">
                    <div class="">
                        <p><strong>Ticket Status:</strong> <?php echo $record["ticket_status"]; ?></p>
                        <input type="hidden" name="ticket_id_hidden" value="<?php echo $ticket_id ?>">
                        <label for="status"><strong>Update status</strong></label>
                        <select name="status" id="status">
                            <?php while ($row = $result_status->fetch_assoc()) {
                                if ($row["status"] == $record["ticket_status"]) {
                                    echo "<option value='" . $row["status"] . "' selected>" . $row["status"] . "</option>";
                                } else {
                                    echo "<option value='" . $row["status"] . "'>" . $row["status"] . "</option>";
                                }
                            } ?>
                            <br>
                        </select>
                        <p><strong>closed at:</strong> <?php echo $record["closed_at"]; ?></p>
                        <button type="submit" class="btn btn-outline-success">submit</button>
                        <!-- delete button -->
                        <button type="button" name="deleteButton" onclick="delete_button(<?php echo $record['ticket_id'] ?>)" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            Delete
                        </button>
                    </div>
                </form>

            </div>
        </div>
        <div class="col-sm-6 text-primary-emphasis border border-primary-subtle d-flex flex-column justify-content-between max-height min-height">
            <div class="mt-5">
                <center>
                    <h1>Chat</h1>
                </center>
            </div>
            <div>
                <?php
                if ($comment_result->num_rows > 0) {
                    while ($row = $comment_result->fetch_assoc()) {
                        echo '<div class="">';

                        $owner_id = $row["user_id"];
                        $current_user_id = $user_id;

                        if ($owner_id == $current_user_id) {
                            echo '<div class="me-5" style="text-align: right;">';
                            echo '<small class="text-success">' . $row["last_name"] . ", " . $row["first_name"] . " (You)</small>";
                            echo "<br>";
                            echo '<label class="text-primary-emphasis bg-primary-subtle border border-primary-subtle rounded-3"><strong>' . $row["content"] . "</strong></label>";
                            $formatted_time = date('F j, Y, g:i a', strtotime($row["created_at"]));
                            echo "<br><small class='text-muted'>" . $formatted_time . "</small>";
                            echo "<br><br>";
                            echo "</div>";
                        } else {
                            echo '<div class="ms-5" style="text-align: left;">';
                            echo '<small class="">' . $row["last_name"] . ", " . $row["first_name"] . "</small>";
                            echo "<br>";
                            echo '<label class="text-primary-emphasis bg-primary-subtle border border-primary-subtle rounded-3"><strong>' . $row["content"] . "</strong></label>";
                            $formatted_time = date('F j, Y, g:i a', strtotime($row["created_at"]));
                            echo "<br><small class='text-muted'>" . $formatted_time . "</small>";
                            echo "<br><br>";
                            echo "</div>";
                        }

                        echo "</div>";
                    }
                } else {
                    echo "<label></label>";
                }
                ?>
            </div>
            <form action="" method="post" class="mt-auto">
                <div class="d-flex justify-content-between">
                    <input type="text" class="form-control" placeholder="Add Chat" name="comment" id="comment">
                    <button type="submit" class="btn btn-outline-success">send</button>
                </div>
            </form>
        </div>
    </div>
    </div>
    </div>
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Record?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="admindeleteticket.php" method="post" style="display: none;" id="form_delete">
                        <input type="hidden" name="hidden_ticket_id" id="hidden_ticket_id">
                        <button type="submit" name="submit_hidden_form">Submit</button>
                    </form>
                    Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="form_delete_submit()">Delete</button>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        function delete_button(ticket_id) {
            let ticket_id_input = document.getElementById("hidden_ticket_id");
            ticket_id_input.value = ticket_id;
        }

        function form_delete_submit() {
            let delete_form = document.getElementById("form_delete");
            delete_form.submit()
        }
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>