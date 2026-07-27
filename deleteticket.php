<?php
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ticket_id = (int)$_POST["hidden_ticket_id"];
        $usertype = (int)$_POST["hidden_usertype"];

        $attachment_sql = "SELECT * FROM tbl_attachment where ticket_id = ?";
        $attachmentresult = $conn->execute_query($attachment_sql, [$ticket_id]);
        $attachment = $attachmentresult->fetch_assoc();

        if($attachment) {
            unlink($attachment["file_path"]);
        }
        
        $deleteCommentsSql = "DELETE FROM tbl_comment WHERE comment_ticket_id = ?";
        $deleteCommentsResult = $conn->execute_query($deleteCommentsSql, [$ticket_id]);

        

        $deleteattachmentSql = "DELETE FROM tbl_attachment WHERE ticket_id = ?";
        $deleteattachmentResult = $conn->execute_query($deleteattachmentSql, [$ticket_id]);

        if ($deleteCommentsResult) {
            $sql = "DELETE FROM tbl_ticket WHERE ticket_id = ?";
            if ($conn->execute_query($sql, [$ticket_id])) {
                header("location: home.php");
            }else{
                echo "<script type='text/javascript'>alert('Deleting of ticket encountered an error. Please try again!')</script>";
            }
        }
}elseif (isset($_GET['id']) && isset($_GET['file_path'])) {
    $attachment_id = (int)$_GET['id'];
    $file_path = urldecode($_GET['file_path']);

    if (file_exists($file_path)) {
        unlink($file_path);
    }

    $sql = "DELETE FROM tbl_attachment WHERE attachment_id = ?";
    if ($conn->execute_query($sql, [$attachment_id])) {
        header("Location: ticket.php?id=" . $_GET['ticket_id']);   
    }
}
  
?>