<?php
include "connection.php";

$user_id = $_SESSION["user_id"];
$id_number = $_SESSION["id_number"];
$user_name = $_SESSION["lastname"];
$department = $_SESSION["department_name"];
$email = $_SESSION["email"];

// for department 
if (isset($_GET["program"])) {
    $program_id = $_GET["program"];
    if ($program_id == 0) { 
        $user_sql = "SELECT * 
            FROM tbl_user
            INNER JOIN tbl_department ON tbl_user.department_id = tbl_department.department_id
            WHERE tbl_user.user_type not in ('4')";
        $user_result = $conn->execute_query($user_sql);
    } else {
        $user_sql = "SELECT * 
            FROM tbl_user
            INNER JOIN tbl_department ON tbl_user.department_id = tbl_department.department_id
            WHERE tbl_department.department_id = ? 
            AND tbl_user.user_type not in ('4')";
        $user_result = $conn->execute_query($user_sql, [$program_id]);
    }
// for search
} elseif (isset($_GET["search"])) {
    $search_query = $_GET["search"];
    $sql = "SELECT * 
            FROM tbl_user
            INNER JOIN tbl_department ON tbl_user.department_id = tbl_department.department_id
            WHERE (tbl_user.first_name LIKE ? OR tbl_user.last_name LIKE ? OR tbl_user.user_id LIKE ?)
            AND tbl_user.user_type NOT IN ('4')";
    $user_result = $conn->execute_query($sql, ["%" . $search_query . "%", "%" . $search_query . "%", "%" . $search_query . "%"]);
} else {
    // Default: show all users
    $user_sql = "SELECT * 
            FROM tbl_user
            INNER JOIN tbl_department ON tbl_user.department_id = tbl_department.department_id
            WHERE tbl_user.user_type not in ('4')";
    $user_result = $conn->execute_query($user_sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .min-height {
        min-height: 100vh;
    }
</style>
<script>
function delete_button(user_id) {
    document.getElementById("hidden_user_id").value = user_id;
}

function form_delete_submit() {
    document.getElementById("form_delete").submit();
}
</script>
</head>
<body>
<?php include "layout/adminnavbar2.php" ?>
<div class="row">
    <div class="col-sm-2 p-3 text-primary-emphasis bg-warning-subtle border-primary-subtle rounded-0 min-height">
        <center>
            <img src="profile.jpg" alt="Profile Image" class="rounded-circle border border-dark-subtle" width="100" height="100">
            <h5><?php echo $user_name ?></h5>
            <a href="logout.php?id=<?php echo $user_id ?>" class="btn btn-danger">Logout</a>
        </center>
    </div>
    <div class="col-sm-9 ms-5">
        <h2 class="mt-3">Registered Users</h2>
        <table class="table table-striped-columns table-hover">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>University ID</th>
                    <th>Lastname</th>
                    <th>Firstname</th>
                    <th>Department</th>
                    <th>User Type</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $user_result->fetch_assoc()) { ?>
                <tr class="table-primary">
                    <td><?php echo $row["user_id"] ?></td>
                    <td><?php echo $row["id_number"] ?></td>
                    <td><?php echo htmlspecialchars($row["last_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["first_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["department_name"]); ?></td>
                    <td>
                        <?php
                        if ($row["user_type"] == 1) echo "Student";
                        elseif ($row["user_type"] == 2) echo "Faculty";
                        elseif ($row["user_type"] == 3) echo "Staff";
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row["email"]); ?></td>
                    <td>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal"
                            onclick="delete_button(<?php echo $row['user_id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="col-sm-2"></div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger"
                    onclick="form_delete_submit()">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form to submit deletion -->
<form action="deleteuser.php" method="post" id="form_delete" style="display:none;">
    <input type="hidden" name="hidden_user_id" id="hidden_user_id">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
