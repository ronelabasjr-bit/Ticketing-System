<?php
include "connection.php";

$user_id = $_SESSION["user_id"];
$id_number = $_SESSION["id_number"];
$user_name = $_SESSION["lastname"];
$department = $_SESSION["department_name"];
$email = $_SESSION["email"];

$sql = "SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN ticket_status = 'Open' THEN 1 ELSE 0 END) AS open_tickets,
            SUM(CASE WHEN ticket_status = 'In Progress' THEN 1 ELSE 0 END) AS in_progress_tickets,
            SUM(CASE WHEN ticket_status = 'Closed' THEN 1 ELSE 0 END) AS closed_tickets
        FROM tbl_anonym_ticket";

$result = $conn->execute_query($sql);
$data = $result->fetch_assoc();

$total = $data['total'] ?? 0;
$open = $data['open_tickets'] ?? 0;
$inProgress = $data['in_progress_tickets'] ?? 0;
$closed = $data['closed_tickets'] ?? 0;

// For counts
$status_sql = "SELECT 
    COUNT(*) AS total_tickets,
    SUM(CASE WHEN ticket_status = 'Open' THEN 1 ELSE 0 END) AS open_tickets,
    SUM(CASE WHEN ticket_status = 'In Progress' THEN 1 ELSE 0 END) AS in_progress_tickets,
    SUM(CASE WHEN ticket_status = 'Closed' THEN 1 ELSE 0 END) AS closed_tickets
FROM tbl_ticket";

$status_result = $conn->execute_query($status_sql);
$status_counts = $status_result->fetch_assoc();

// Query for anonymous tickets
$sql_anon = "SELECT * FROM tbl_anonym_ticket 
             INNER JOIN tbl_categories ON tbl_anonym_ticket.category_id = tbl_categories.category_id";
$anon_result = $conn->execute_query($sql_anon);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .ticket-stats-container {
        padding: 10px;
        font-family: Arial, sans-serif;
        width: 100%;
        }

        .ticket-stats-group {
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 0 8px rgba(0,0,0,0.05);
        font-size: 0.85rem; /* smaller font */
        }

        .ticket-stats-group h4 {
        margin-bottom: 10px;
        font-size: 0.95rem;
        border-bottom: 1px solid #007bff;
        padding-bottom: 5px;
        color: #333;
    }

    @media (max-width: 576px) {
        .ticket-stats-group {
            font-size: 0.75rem;
        }

        .ticket-stats-group h4 {
            font-size: 0.85rem;
        }
    }





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
</head>
<body>

<?php include "layout/adminnavbar.php"; ?>

<div class="row">
    <!-- profile -->
    <div class="col-sm-2 p-3 text-primary-emphasis bg-warning-subtle border-primary-subtle rounded-0 min-height">
        <center>
            <img src="profile.jpg" alt="Profile Image" class="rounded-circle border border-dark-subtle" width="100" height="100">
            <h5><?php echo htmlspecialchars($user_name); ?></h5>
            <div class="ticket-stats-container">
                <!-- Registered User Tickets -->
                <div class="ticket-stats-group">
                    <h4>Registered User Tickets</h4>
                    <div class="row mb-1">
                        <div class="col-7 text-wrap">Number of tickets:</div>
                        <div class="col-5 text-end"><?php echo htmlspecialchars($status_counts['total_tickets']); ?></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-7 text-primary text-wrap">Open Tickets:</div>
                        <div class="col-5 text-primary text-end"><?php echo htmlspecialchars($status_counts['open_tickets']); ?></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-7 text-success text-wrap">In Progress Tickets:</div>
                        <div class="col-5 text-success text-end"><?php echo htmlspecialchars($status_counts['in_progress_tickets']); ?></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-7 text-danger text-wrap">Closed Tickets:</div>
                        <div class="col-5 text-danger text-end"><?php echo htmlspecialchars($status_counts['closed_tickets']); ?></div>
                    </div>
                </div>

                <!-- Anonymous User Tickets -->
                <div class="ticket-stats-group">
                    <h4>Anonymous User Tickets</h4>
                    <div class="row mb-1">
                        <div class="col-7 text-wrap">Number of tickets:</div>
                        <div class="col-5 text-end"><?php echo htmlspecialchars($total); ?></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-7 text-primary text-wrap">Open Tickets:</div>
                        <div class="col-5 text-primary text-end"><?php echo htmlspecialchars($open); ?></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-7 text-success text-wrap">In Progress Tickets:</div>
                        <div class="col-5 text-success text-end"><?php echo htmlspecialchars($inProgress); ?></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-7 text-danger text-wrap">Closed Tickets:</div>
                        <div class="col-5 text-danger text-end"><?php echo htmlspecialchars($closed); ?></div>
                    </div>
                </div>
            </div>

            <a href="logout.php?id=<?php echo $user_id ?>" class="btn btn-danger">Logout</a>
        </center>
    </div>

    <!-- content -->
    <div class="col-sm-9 ms-5">
        <!-- Non-anonymous tickets table -->
        <h2 class="mt-3">Tickets:</h2>
        <table class="table table-striped-columns table-hover">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Priority level</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    $ticket_id = $row['ticket_id'];

                    // Get unread comment count
                    $sql_unread_comments = "SELECT COUNT(*) as unread_count 
                                            FROM tbl_comment 
                                            WHERE comment_ticket_id = ? 
                                            AND is_read = 0 
                                            AND comment_user_id != ?";
                    $unread_result = $conn->execute_query($sql_unread_comments, [$ticket_id, $user_id]);
                    $unread_count = $unread_result->fetch_assoc()['unread_count'];

                    // Row color class
                    $row_class = match($row["ticket_status"]) {
                        "open" => "table-primary",
                        "in progress" => "table-success",
                        "closed" => "table-danger",
                        default => ""
                    };
                ?>
                <tr class="<?php echo $row_class; ?>">
                    <td style="position: relative;">
                        <a href="adminticket.php?id=<?php echo $ticket_id ?>">
                            <?php echo htmlspecialchars($row["title"]); ?>
                            <?php if ($unread_count > 0) { ?>
                                <span class="notification-dot"></span>
                            <?php } ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($row["category_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["ticket_description"]); ?></td>
                    <td><?php echo htmlspecialchars($row["category_priority"]); ?></td>
                    <td><?php echo htmlspecialchars($row["ticket_status"]); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Anonymous tickets table -->
        <h2 class="mt-3">From Anonymous Tickets:</h2>
        <table class="table table-striped-columns table-hover">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Priority level</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $anon_result->fetch_assoc()) {
                    $row_class = match($row["ticket_status"]) {
                        "open" => "table-primary",
                        "in progress" => "table-success",
                        "closed" => "table-danger",
                        default => ""
                    };
                ?>
                <tr class="<?php echo $row_class; ?>">
                    <td style="position: relative;">
                        <a href="anonymous_file/adminchecking_anonticket.php?id=<?php echo $row['ticket_anonym_id'] ?>">
                            <?php echo htmlspecialchars($row["title"]); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($row["category_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["ticket_description"]); ?></td>
                    <td><?php echo htmlspecialchars($row["category_priority"]); ?></td>
                    <td><?php echo htmlspecialchars($row["ticket_status"]); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="col-sm-1"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>