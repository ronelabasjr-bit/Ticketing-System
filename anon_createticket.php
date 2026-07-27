<?php
include "connection.php";

$category_sql = "SELECT category_id, category_name FROM ticketing_db.tbl_categories";
$category_result = $conn->execute_query($category_sql);

// Initialize message and ticket ID
$message = "";
$ticket_id_display = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $title = $_POST["title"];
    $category = $_POST["category"];
    $description = $_POST["description"];

    if (empty($title) || empty($category) || empty($description)) {
        $message = "<p style='color: red;'>Please fill out all the required information.</p>";
    } else {
        $sql = "INSERT INTO tbl_anonym_ticket(name, email, title, category_id, ticket_description, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        if ($conn->execute_query($sql, [$name, $email, $title, $category, $description])) {
            $ticket_id = $conn->insert_id;
            $ticket_id_display = $ticket_id;

            // File upload handling
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
                $file = $_FILES['attachment'];
                $fileName = $file['name'];
                $fileTmpName = $file['tmp_name'];
                $fileSize = $file['size'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

                if (in_array($fileExt, $allowed)) {
                    if ($fileSize < 30000000) { // 30MB
                        $fileNameNew = uniqid('anon_', true) . "." . $fileExt;
                        $fileDestination = 'uploads/' . $fileNameNew;

                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            $sql_attachment = "INSERT INTO tbl_anonym_attachment(ticket_anonym_id, file_path, uploaded_at) 
                                               VALUES (?, ?, NOW())";
                            $conn->execute_query($sql_attachment, [$ticket_id, $fileDestination]);
                        } else {
                            $message .= "<p style='color: red;'>Failed to move uploaded file.</p>";
                        }
                    } else {
                        $message .= "<p style='color: red;'>File too large (max 30MB).</p>";
                    }
                } else {
                    $message .= "<p style='color: red;'>Invalid file type. Only JPG, PNG, or PDF allowed.</p>";
                }
            }

            if (empty($message)) {
                $message = "<p style='color: green;'>Ticket submitted successfully. Your ticket ID is: <strong>$ticket_id</strong></p>";
            }
        } else {
            $message = "<p style='color: red;'>Save error. Please try again.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-4 mt-5">
        <div class="mx-auto text-center">
            <h1>Create Ticket</h1>
        </div>
        <?php if (!empty($message)) echo $message; ?>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="name">Name (optional):</label>
            <input type="text" class="form-control" name="name" id="name"><br>

            <label for="email">Email (optional):</label>
            <input type="text" class="form-control" name="email" id="email"><br>

            <label for="title">Title:</label>
            <input type="text" class="form-control" name="title" id="title" required><br>

            <label for="category">Help Topic:</label>
            <select class="form-select" name="category" id="category" required>
                <option value="">--Select--</option>
                <?php
                    while ($row = $category_result->fetch_assoc()) {
                        echo "<option value='{$row["category_id"]}'>{$row["category_name"]}</option>";
                    }
                ?>
            </select><br>

            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" required></textarea><br>

            <label for="attachment">Attachment (optional):</label>
            <input type="file" class="form-control" name="attachment" id="attachment"><br>

            <div class="mx-auto text-center mt-3">
                <button name="submit" type="submit" class="btn btn-info">Submit</button>
            </div>
            <div class="mx-auto text-center mt-3">
                <center> <a href="loginpage.php" class="btn btn-warning mt-2">go to login page</a></center>
            </div>
            
        </form>

        <?php if (!empty($ticket_id_display)): ?>
            <div class="alert alert-success text-center mt-4">
                <strong>Success!</strong> Your ticket ID is <strong><?= $ticket_id_display ?></strong>.
                Please save this ID to check your ticket status later.
            </div>
        <?php endif; ?>
    </div>
    <div class="col-sm-4"></div>
</div>
</body>
</html>
