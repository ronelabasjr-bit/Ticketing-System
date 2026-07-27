<?php
include "connection.php";

// Fetch categories for the dropdown
$category_sql = "SELECT category_id, category_name FROM ticketing_db.tbl_categories";
$category_result = $conn->execute_query($category_sql);

// Initialize message
$message = "";

// Ticket and attachment submission logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $title = trim($_POST["title"]);
    $category = $_POST["category"];
    $description = trim($_POST["description"]);
    $user_id = $_SESSION["user_id"];

    if (empty($title) || empty($category) || empty($description)) {
        $message = "<p style='color: red;'>Please fill out all the information.</p>";
    } else {
        // Insert ticket into database
        $sql = "INSERT INTO tbl_ticket(title, category, ticket_status, ticket_description, created_at, user_id) VALUES (?, ?, 'Open', ?, NOW(), ?)";
        if ($conn->execute_query($sql, [$title, $category, $description, $user_id])) {
            // Get the inserted ticket ID
            $ticket_id = $conn->insert_id;

            // Handle file upload if any
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                $file = $_FILES['attachment'];
                $fileName = $file['name'];
                $fileTmpName = $file['tmp_name'];
                $fileSize = $file['size'];
                $fileError = $file['error'];

                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = array('jpg', 'jpeg', 'png', 'pdf');

                if (in_array($fileExt, $allowed)) {
                    if ($fileSize < 30000000) {
                        $fileNameNew = uniqid('', true) . "." . $fileExt;
                        $fileDestination = 'uploads/' . $fileNameNew;

                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            $sql_attachment = "INSERT INTO tbl_attachment(ticket_id, file_path, uploaded_at) VALUES (?, ?, NOW())";
                            $conn->execute_query($sql_attachment, [$ticket_id, $fileDestination]);
                        } else {
                            $message .= "<p style='color: red;'>Failed to move uploaded file.</p>";
                        }
                    } else {
                        $message .= "<p style='color: red;'>Your file is too big (max 28MB).</p>";
                    }
                } else {
                    $message .= "<p style='color: red;'>Invalid file type. Only JPG, PNG, or PDF allowed.</p>";
                }
            }

            // Redirect if no error
            if (empty($message)) {
                header("Location: home.php");
                exit();
            }
        } else {
            $message = "<p style='color: red;'>Failed to save ticket to the database.</p>";
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
<div class="container mt-5">
    <div class="col-md-6 offset-md-3">
        <h2 class="text-center">Create Ticket</h2>
        <?php if (!empty($message)) echo $message; ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title:</label>
                <input type="text" name="title" id="title" class="form-control">
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Help Topic:</label>
                <select name="category" id="category" class="form-select">
                    <option value="">--Select--</option>
                    <?php
                    while ($row = $category_result->fetch_assoc()) {
                        echo "<option value='{$row["category_id"]}'>{$row["category_name"]}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="attachment" class="form-label">Attachment (optional):</label>
                <input type="file" name="attachment" id="attachment" class="form-control">
            </div>
            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-info">Submit</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
