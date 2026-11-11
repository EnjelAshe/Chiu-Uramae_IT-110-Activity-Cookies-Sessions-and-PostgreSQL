<?php
session_start();
require 'db.php';
$_SESSION['visits'] = ($_SESSION['visits'] ?? 0) + 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author = trim($_POST['author'] ?? '');
    $body   = trim($_POST['body'] ?? '');

    if ($author !== '' && $body !== '') {
        $stmt = $pdo->prepare("INSERT INTO messages (author, body) VALUES (:author, :body)");
        $stmt->execute([':author' => $author, ':body' => $body]);
    }

    if (isset($_POST['remember'])) {
        setcookie('author', $author, time() + 60 * 60 * 24 * 30);
    } else {
        setcookie('author', '', time() - 3600);
    }

    header('Location: index.php');
    exit;
}

$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE body ILIKE :s ORDER BY created_at DESC");
    $stmt->execute([':s' => "%$search%"]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM messages ORDER BY created_at DESC");
    $stmt->execute();
}
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$savedName = $_COOKIE['author'] ?? '';
?>



<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Class Wall</title></head>
<body>
    <h1>Class Wall</h1>

    <p>You've visited <?= (int)$_SESSION['visits'] ?> times</p>

    <form method="POST">
        <input type="text" name="author" placeholder="Your name" value="<?= htmlspecialchars($savedName) ?>" required>
        <br>
        <textarea name="body" placeholder="Your message" required></textarea>
        <br>
        <label><input type="checkbox" name="remember" <?= $savedName ? 'checked' : '' ?>> Remember my name</label>
        <br>
        <button type="submit">Post</button>
    </form>

    <form method="GET">
        <input type="text" name="search" placeholder="Search messages"
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <hr>
    <?php foreach ($messages as $m): ?>
    <div>
        <strong><?= htmlspecialchars($m['author']) ?></strong>
        <small>(<?= htmlspecialchars($m['created_at']) ?>)</small>
        <p><?= htmlspecialchars($m['body']) ?></p>
    </div>
    <?php endforeach; ?>

</body>
</html>