<?php
include 'admin_guard.php';
include '../backend/connect.php';

// Get user ID
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = (int)$_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name  = $conn->real_escape_string($_POST['last_name']);
    $email      = $conn->real_escape_string($_POST['email']);
    $password   = $_POST['password']; // Optional, plain text

    if (!empty($password)) {
        // Update with new password
        $sql = "UPDATE users SET first_name=?, last_name=?, email=?, password=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $first_name, $last_name, $email, $password, $user_id);
    } else {
        // Update without changing password
        $sql = "UPDATE users SET first_name=?, last_name=?, email=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $first_name, $last_name, $email, $user_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully!'); window.location='users.php';</script>";
        exit();
    } else {
        $error = "Error updating user: " . $stmt->error;
    }
}

// Fetch user info
$result = $conn->query("SELECT * FROM users WHERE user_id=$user_id");
if ($result->num_rows === 0) {
    die("User not found.");
}
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit User</title>
<style>
body {font-family: Arial, sans-serif; background:#f4f6f9; text-align:center; padding:20px;}
.logo-container {margin-bottom:20px;}
.logo-container img {width:120px;}
form {background:#fff; padding:20px; border-radius:10px; display:inline-block; text-align:left; min-width:300px;}
input {width:100%; padding:10px; margin-bottom:10px; border-radius:6px; border:1px solid #ccc;}
button {padding:10px 15px; border:none; border-radius:6px; background:#023E8A; color:#fff; cursor:pointer;}
button:hover {background:#1565c0;}
.error {color:red; margin-bottom:10px;}
</style>
</head>
<body>

<div class="logo-container">
    <img src="logo.png" alt="Logo">
</div>

<h2>Edit User</h2>

<?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST" action="">
    <label>First Name</label>
    <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>

    <label>Last Name</label>
    <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label>Password (leave blank to keep unchanged)</label>
    <input type="password" name="password">

    <button type="submit">Update User</button>
</form>

</body>
</html>
