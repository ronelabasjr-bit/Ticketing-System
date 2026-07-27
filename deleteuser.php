<?php
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = (int)$_POST["hidden_user_id"];

    // Prevent self-deletion
    if ($user_id == $_SESSION["user_id"]) {
        echo "<script>alert('You cannot delete your own account!'); window.location.href='users.php';</script>";
        exit();
    }

    // 1. Delete comments made by the user
    $sql_delete_comments = "DELETE FROM tbl_comment WHERE comment_user_id = ?";
    $conn->execute_query($sql_delete_comments, [$user_id]);

    // 2. Fetch tickets created by the user
    $sql_get_tickets = "SELECT ticket_id FROM tbl_ticket WHERE user_id = ?";
    $result = $conn->execute_query($sql_get_tickets, [$user_id]);

    while ($ticket = $result->fetch_assoc()) {
        $ticket_id = $ticket['ticket_id'];

        // Delete comments on the user's tickets
        $sql_delete_ticket_comments = "DELETE FROM tbl_comment WHERE comment_ticket_id = ?";
        $conn->execute_query($sql_delete_ticket_comments, [$ticket_id]);

        // Delete attachments linked to the user's tickets
        $sql_get_attachments = "SELECT attachment_id, file_path FROM tbl_attachment WHERE ticket_id = ?";
        $attachments = $conn->execute_query($sql_get_attachments, [$ticket_id]);
        while ($attachment = $attachments->fetch_assoc()) {
            if (file_exists($attachment['file_path'])) {
                unlink($attachment['file_path']); // delete physical file
            }
        }
        $sql_delete_attachments = "DELETE FROM tbl_attachment WHERE ticket_id = ?";
        $conn->execute_query($sql_delete_attachments, [$ticket_id]);

        // Delete the ticket itself
        $sql_delete_ticket = "DELETE FROM tbl_ticket WHERE ticket_id = ?";
        $conn->execute_query($sql_delete_ticket, [$ticket_id]);
    }

    // 3. Delete the user
    $sql_delete_user = "DELETE FROM tbl_user WHERE user_id = ?";
    if ($conn->execute_query($sql_delete_user, [$user_id])) {
        header("Location: user.php");
        exit();
    } else {
        echo "<script>alert('Error deleting user.'); window.location.href='users.php';</script>";
    }
}
?>
