<?php
include "connection.php";

$ticket = null;
$error = null;
$message = null;
$attachment_result = null;

// Create uploads directory if not exists
$upload_dir = "uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Resolve Ticket
    if (isset($_POST["resolve_ticket_id"])) {
        $ticket_id = $_POST["resolve_ticket_id"];
        $sql = "UPDATE tbl_anonym_ticket SET ticket_status = 'closed', closed_at = NOW() WHERE ticket_anonym_id = ?";
        $stmt = $conn->execute_query($sql, [$ticket_id]);
        if ($stmt) {
            $message = "Ticket marked as resolved.";
        } else {
            $error = "Failed to update the ticket.";
        }

    // Upload Attachment
    } elseif (isset($_POST["upload_ticket_id"])) {
        $ticket_id = $_POST["upload_ticket_id"];
        if (isset($_FILES["attachment"]) && $_FILES["attachment"]["error"] == 0) {
            $original_name = basename($_FILES["attachment"]["name"]);
            $file_name = time() . "_" . $original_name;
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
                $sql = "INSERT INTO tbl_anonym_attachment (ticket_anonym_id, file_path, uploaded_at) VALUES (?, ?, NOW())";
                $stmt = $conn->execute_query($sql, [$ticket_id, $file_name]);
                if ($stmt) {
                    $message = "Attachment uploaded successfully.";
                } else {
                    $error = "Failed to save attachment to database.";
                }
            } else {
                $error = "Failed to upload file.";
            }
        } else {
            $error = "No file uploaded or upload error.";
        }

    // Search Ticket by ID
    } elseif (isset($_POST["ticket_id"])) {
        $ticket_id = $_POST["ticket_id"];
    }

    // Refetch ticket and attachments
    if (!empty($ticket_id)) {
        $sql = "SELECT * FROM tbl_anonym_ticket WHERE ticket_anonym_id = ?";
        $stmt = $conn->execute_query($sql, [$ticket_id]);
        if ($stmt->num_rows > 0) {
            $ticket = $stmt->fetch_assoc();

            // Fetch attachments
            $sql_attach = "SELECT * FROM tbl_anonym_attachment WHERE ticket_anonym_id = ?";
            $attachment_result = $conn->execute_query($sql_attach, [$ticket_id]);
        } else {
            $error = "No ticket found with that ID.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Ticket Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="text-center mb-4">Check Ticket Status</h3>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" class="mb-4">
        <div class="input-group mb-3">
            <input type="text" name="ticket_id" class="form-control" placeholder="Enter your Ticket ID" required>
            <button type="submit" class="btn btn-info">Check</button>
        </div>
    </form>

    <?php if ($ticket): ?>
        <div class="card">
            <div class="card-header bg-success text-white">
                Ticket #<?php echo htmlspecialchars($ticket['ticket_anonym_id']); ?>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($ticket['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($ticket['email']); ?></p>
                <p><strong>Title:</strong> <?php echo htmlspecialchars($ticket['title']); ?></p>
                <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($ticket['ticket_description'])); ?></p>
                <p><strong>Created At:</strong> <?php echo htmlspecialchars($ticket['created_at']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($ticket['ticket_status']); ?></p>

                <?php if (!empty($ticket['admin_reply'])): ?>
                    <p><strong>Admin Reply:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($ticket['admin_reply'])); ?></p>

                    <?php if (empty($ticket['closed_at'])): ?>
                        <form method="post" class="mt-3">
                            <input type="hidden" name="resolve_ticket_id" value="<?php echo $ticket['ticket_anonym_id']; ?>">
                            <button type="submit" class="btn btn-success">Mark as Resolved</button>
                        </form>
                    <?php else: ?>
                        <p><strong>Closed At:</strong> <?php echo htmlspecialchars($ticket['closed_at']); ?></p>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Display Attachments -->
                <h5 class="mt-4">Attachments</h5>
                <?php if ($attachment_result && $attachment_result->num_rows > 0): ?>
                    <?php while ($attachment = $attachment_result->fetch_assoc()): ?>
                        <?php $file = $upload_dir . htmlspecialchars($attachment["file_path"]); ?>
                        <div class="mb-3">
                            <?php if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <a href="<?= $file ?>" target="_blank">
                                    <img src="<?= $file ?>" width="200" height="200" class="img-thumbnail">
                                </a>
                            <?php else: ?>
                                <a href="<?= $file ?>" target="_blank"><?= basename($file) ?></a>
                            <?php endif; ?>
                            <p><small>Uploaded: <?= htmlspecialchars($attachment["uploaded_at"]) ?></small></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No attachments.</p>
                <?php endif; ?>

                <!-- Upload Form -->
                <form method="post" enctype="multipart/form-data" class="mt-4">
                    <input type="hidden" name="upload_ticket_id" value="<?php echo $ticket['ticket_anonym_id']; ?>">
                    <div class="mb-3">
                        <label for="attachment" class="form-label">Upload New Attachment</label>
                        <input type="file" name="attachment" id="attachment" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
        <div>
            <center> <a href="loginpage.php" class="btn btn-warning mt-2">go to login page</a></center>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
