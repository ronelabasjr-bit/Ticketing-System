<?php 
include "connection.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST["status"];
    $ticket_id = $_POST["ticket_id_hidden"];

    if ($status == 'closed') {
        $sql = "UPDATE tbl_ticket SET ticket_status = ?, closed_at = ? WHERE ticket_id = ?";
        // echo "<script type='text/javascript'>alert('".$sql."')</script>";

        if ($conn->execute_query($sql, [$status, date("Y-m-d H:i:s"),$ticket_id])) {
            header("location: adminticket.php?id=" . $ticket_id);
            echo $status;
        } else {
            // echo "<script type='text/javascript'>alert('Save Error')</script>";
            echo $conn->error;
        }
    }else{
        $sql = "UPDATE tbl_ticket SET ticket_status = ? WHERE ticket_id = ?";
        // echo "<script type='text/javascript'>alert('".$sql."')</script>";

        if ($conn->execute_query($sql, [$status, $ticket_id])) {
            header("location: adminticket.php?id=" . $ticket_id);
            echo $status;
        } else {
            echo "<script type='text/javascript'>alert('Save Error')</script>";
            echo $conn->error;
        }
    }
}


?>