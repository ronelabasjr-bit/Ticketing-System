<?php
include "connection.php";

// Assuming you store the user's ID in session after login
$user_id = $_SESSION['user_id'] ?? null;
$message = "";

// Redirect if not logged in
if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Fetch departments for dropdown
$department_sql = "SELECT department_id, department_name FROM ticketing_db.tbl_department";
$department_result = $conn->execute_query($department_sql);

// Fetch user data
$user_sql = "SELECT * FROM tbl_user WHERE user_id = ?";
$user_result = $conn->execute_query($user_sql, [$user_id]);

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $last_name = $_POST["lastname"];
    $first_name = $_POST["firstname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $department = $_POST["department"];
    $user_type = $_POST["user_type"];

    if (empty($last_name) || empty($first_name) || empty($email) || empty($department) || empty($user_type)) {
        $message = "<p style='color: red;'>Please fill in all required fields.</p>";
    } else {
        $update_sql = "UPDATE tbl_user SET last_name = ?, first_name = ?, email = ?, department_id = ?, user_type = ?" . (!empty($password) ? ", password = ?" : "") . " WHERE user_id = ?";
        
        $params = [$last_name, $first_name, $email, $department, $user_type];
        if (!empty($password)) {
            $params[] = md5($password); // Use bcrypt in production!
        }
        $params[] = $user_id;

        if ($conn->execute_query($update_sql, $params)) {
            $message = "<p style='color: green;'>Profile updated successfully.</p>";
            // Refresh user data
            $user_result = $conn->execute_query($user_sql, [$user_id]);
            $user_data = $user_result->fetch_assoc();
        } else {
            $message = "<p style='color: red;'>Update failed. Please try again.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="col-md-6 offset-md-3 bg-light p-4 rounded">
        <h2 class="text-center">Edit Profile</h2>
        <?php echo $message; ?>
        <form action="" method="post">
            <div class="mb-3">
                <label for="id_number" class="form-label">ID Number:</label>
                <input type="text" class="form-control" value="<?php echo $user_data['id_number']; ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="lastname" class="form-label">Last Name:</label>
                <input type="text" class="form-control" name="lastname" value="<?php echo $user_data['last_name']; ?>">
            </div>
            <div class="mb-3">
                <label for="firstname" class="form-label">First Name:</label>
                <input type="text" class="form-control" name="firstname" value="<?php echo $user_data['first_name']; ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo $user_data['email']; ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password (leave blank to keep current):</label>
                <input type="password" class="form-control" name="password">
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">Department:</label>
                <select name="department" class="form-select">
                    <option value="">--Select--</option>
                    <?php
                    while ($row = $department_result->fetch_assoc()) {
                        $selected = $row["department_id"] == $user_data["department_id"] ? "selected" : "";
                        echo "<option value='{$row["department_id"]}' $selected>{$row["department_name"]}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="user_type" class="form-label">User Type:</label>
                <select name="user_type" class="form-select">
                    <option value="">-- Select --</option>
                    <option value="1" <?php echo $user_data['user_type'] == 1 ? "selected" : ""; ?>>Student</option>
                    <option value="2" <?php echo $user_data['user_type'] == 2 ? "selected" : ""; ?>>Faculty</option>
                    <option value="3" <?php echo $user_data['user_type'] == 3 ? "selected" : ""; ?>>Staff</option>
                </select>
            </div>
            <div class="text-center">
                <button name="submit" type="submit" class="btn btn-info">Update Profile</button>
                <a href="home.php" class="btn btn-warning">go to home</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
