<?php
require 'helpers.php';
$q = trim($_GET['q'] ?? '');   // read the GET value
?>
<!doctype html>
<html lang="en">f-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
<title>Search</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="card">
  <h1>Search</h1>
  <form method="GET" action="search.php" class="row">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="e.g. javascript">
    <button type="submit">Search</button>
  </form>
  <?php if ($q !== ''): ?>
    <p class="result">Search result for: <strong><?= e($q) ?></strong></p>
  <?php elseif (isset($_GET['q'])): ?>
    <p class="err">Please type something to search.</p>
  <?php endif; ?>
  <p class="links"><a href="register.php">Register</a> · <a href="profile.php">Profile</a></p>
</main>
</body>
</html>
