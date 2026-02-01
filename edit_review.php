<?php
include 'admin_guard.php';
include '../backend/connect.php';

$review_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$review_id) {
    header("Location: reviews.php");
    exit();
}

// Fetch review
$stmt = $conn->prepare("SELECT * FROM reviews WHERE review_id=?");
$stmt->bind_param("i", $review_id);
$stmt->execute();
$result = $stmt->get_result();
$review = $result->fetch_assoc();
$stmt->close();

if (!$review) {
    header("Location: reviews.php");
    exit();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating  = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    $stmt = $conn->prepare("UPDATE reviews SET rating=?, comment=? WHERE review_id=?");
    $stmt->bind_param("isi", $rating, $comment, $review_id);
    $stmt->execute();
    $stmt->close();

    header("Location: reviews.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Review</title>
<style>
body {font-family: Arial, sans-serif; background: #f4f6f9; margin:0; padding:20px;}
h2 {color:#023E8A; margin-bottom:20px;}
form {background:#fff; padding:20px; border-radius:10px; max-width:500px;}
label {display:block; margin-top:10px; font-weight:bold;}
input, textarea, select {width:100%; padding:8px; margin-top:5px; border-radius:6px; border:1px solid #ccc;}
button {margin-top:15px; padding:10px 15px; border:none; border-radius:6px; background:#023E8A; color:#fff; cursor:pointer;}
button:hover {background:#1565c0;}
a {display:inline-block; margin-top:10px; color:#023E8A; text-decoration:none;}
</style>
</head>
<body>

<h2>Edit Review #<?= $review['review_id'] ?></h2>

<form method="POST">
    <label>Rating</label>
    <select name="rating" required>
        <?php for($i=1;$i<=5;$i++): ?>
            <option value="<?= $i ?>" <?= $review['rating']==$i?'selected':'' ?>><?= $i ?></option>
        <?php endfor; ?>
    </select>

    <label>Comment</label>
    <textarea name="comment" rows="4" required><?= htmlspecialchars($review['comment']) ?></textarea>

    <button type="submit">Update Review</button>
</form>

<a href="reviews.php">← Back to Reviews</a>

</body>
</html>
