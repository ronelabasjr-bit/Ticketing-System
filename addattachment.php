<?php

include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $ticket_id = $_POST["id"];
    $file = $_FILES['attachment'];

    // Extract file details
    $fileName = $_FILES['attachment']['name'];
    $fileTmpName = $_FILES['attachment']['tmp_name'];
    $fileSize = $_FILES['attachment']['size'];
    $fileError = $_FILES['attachment']['error'];
    $fileType = $_FILES['attachment']['type'];

    // Validate file extension
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));
    $allowed = array('jpg', 'jpeg', 'png', 'pdf');

    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 30000000) { // File size limit is 28.61MB
                // Generate unique file name and define destination
                $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                $fileDestination = 'uploads/' . $fileNameNew;

                // Move uploaded file to the destination
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Insert file information into the database
                    $sql_attachment = "INSERT INTO tbl_attachment(ticket_id, file_path, uploaded_at) VALUES(?, ?, ?)";
                    if ($conn->execute_query($sql_attachment, [$ticket_id, $fileDestination, date(format: "Y-m-d H:i:s")])) {
                        header("Location: ticket.php?id=" . $ticket_id);
                        exit();
                    } else {
                        echo "Database save error.";
                    }
                } else {
                    echo "Failed to move uploaded file.";
                }
            } else {
                echo "<big style='color: red'>"."Your file is too big!"."</big>";
            }
        } else {
            echo "<big style='color: red'>"."There was an error uploading your file!"."</big>";
        }
    } else {
        echo "<big style='color: red'>"."You cannot upload files of this type!"."</big>";
    }
}

?>
