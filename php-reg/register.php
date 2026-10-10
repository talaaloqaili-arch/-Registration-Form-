<?php
require 'Registration.php';   // class must be loaded BEFORE session_start
require 'helpers.php';
session_start();

$governorates = ['Amman', 'Irbid', 'Aqaba', 'Zarqa'];
$tracks       = ['Full Stack', 'Frontend', 'Backend'];
$skillsList   = ['HTML', 'CSS', 'JavaScript'];

$errors = [];
$registered = null;

// Defaults (cookie pre-fills name and track on a repeat visit)
$old = [
    'name' => $_COOKIE['reg_name'] ?? '', 'email' => '', 'mobile' => '',
    'governorate' => '', 'track' => $_COOKIE['reg_track'] ?? '',
    'skills' => [], 'message' => '', 'agree' => false,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) collect + 2) clean with trim()
    $skillsPosted = is_array($_POST['skills'] ?? null) ? $_POST['skills'] : [];
    $old = [
        'name'        => trim($_POST['name'] ?? ''),
        'email'       => trim($_POST['email'] ?? ''),
        'mobile'      => trim($_POST['mobile'] ?? ''),
        'governorate' => trim($_POST['governorate'] ?? ''),
        'track'       => trim($_POST['track'] ?? ''),
        'skills'      => array_values(array_intersect($skillsList, array_map('trim', $skillsPosted))),
        'message'     => trim($_POST['message'] ?? ''),
        'agree'       => isset($_POST['agree']),
    ];

    // 3) validate in PHP
    if ($old['name'] === '') $errors['name'] = 'Full name is required.';
    elseif (mb_strlen($old['name']) < 3) $errors['name'] = 'Name must be at least 3 characters.';

    if ($old['email'] === '') $errors['email'] = 'Email is required.';
    elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email address.';

    $mobile = preg_replace('/[\s-]/', '', $old['mobile']);
    if ($old['mobile'] === '') $errors['mobile'] = 'Mobile is required.';
    elseif (!preg_match('/^(\+?962|0)?7[789][0-9]{7}$/', $mobile)) $errors['mobile'] = 'Use a Jordanian number, e.g. 0791234567.';

    if (!in_array($old['governorate'], $governorates, true)) $errors['governorate'] = 'Please select a governorate.';
    if (!in_array($old['track'], $tracks, true)) $errors['track'] = 'Please choose a track.';
    if (!$old['skills']) $errors['skills'] = 'Select at least one skill.';
    if (mb_strlen($old['message']) > 500) $errors['message'] = 'Message must be 500 characters or less.';
    if (!$old['agree']) $errors['agree'] = 'You must accept the academy terms.';

    // 4) valid: build the object, save to session + cookie
    if (!$errors) {
        $registered = new Registration(
            $old['name'], $old['email'], $mobile, $old['governorate'],
            $old['track'], $old['skills'], $old['message']
        );
        $_SESSION['registration'] = $registered;
        $expire = time() + 60 * 60 * 24 * 30;   // 30 days
        setcookie('reg_name',  $registered->name,  $expire, '/');
        setcookie('reg_track', $registered->track, $expire, '/');
    }
}

function err(array $errors, string $f): string {
    return isset($errors[$f]) ? '<span class="err">' . e($errors[$f]) . '</span>' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php if ($registered): ?>
  <div class="card">
    <h1>Registration successful</h1>
    <dl>
      <dt>Name</dt><dd><?= e($registered->name) ?></dd>
      <dt>Email</dt><dd><?= e($registered->email) ?></dd>
      <dt>Mobile</dt><dd><?= e($registered->mobile) ?></dd>
      <dt>Governorate</dt><dd><?= e($registered->governorate) ?></dd>
      <dt>Track</dt><dd><?= e($registered->track) ?></dd>
      <dt>Skills</dt><dd><?= e($registered->skillsText()) ?></dd>
      <dt>Message</dt><dd><?= $registered->message !== '' ? nl2br(e($registered->message)) : '-' ?></dd>
    </dl>
    <p><a class="btn" href="profile.php">View my profile (from session)</a>
       <a href="register.php">Register another</a></p>
  </div>
<?php else: ?>
   <form action="register.php" method="post" novalidate>

  <?php if (isset($_COOKIE['reg_name']) && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
    <p class="note">Welcome back, <?= e($_COOKIE['reg_name']) ?>! We filled in your name and track.</p>
  <?php endif; ?>

  <label for="name">Full Name</label>
  <input type="text" id="name" name="name" value="<?= e($old['name']) ?>">
  <?= err($errors, 'name') ?>

  <label for="email">Email</label>
  <input type="email" id="email" name="email" value="<?= e($old['email']) ?>">
  <?= err($errors, 'email') ?>

  <label for="mobile">Mobile</label>
  <input type="tel" id="mobile" name="mobile" placeholder="0791234567" value="<?= e($old['mobile']) ?>">
  <?= err($errors, 'mobile') ?>

  <label for="governorate">Governorate</label>
  <select id="governorate" name="governorate">
    <option value="">Choose a governorate</option>
    <?php foreach ($governorates as $g): ?>
      <option value="<?= e($g) ?>" <?= $old['governorate'] === $g ? 'selected' : '' ?>><?= e($g) ?></option>
    <?php endforeach; ?>
  </select>
  <?= err($errors, 'governorate') ?>

  <label>Track</label>
  <?php foreach ($tracks as $t): ?>
    <label class="inline">
      <input type="radio" name="track" value="<?= e($t) ?>" <?= $old['track'] === $t ? 'checked' : '' ?>>
      <?= e($t) ?>
    </label>
  <?php endforeach; ?>
  <?= err($errors, 'track') ?>

  <label>Skills you already have</label>
  <?php foreach ($skillsList as $s): ?>
    <label class="inline">
      <input type="checkbox" name="skills[]" value="<?= e($s) ?>" <?= in_array($s, $old['skills'], true) ? 'checked' : '' ?>>
      <?= e($s) ?>
    </label>
  <?php endforeach; ?>
  <?= err($errors, 'skills') ?>

  <label for="message">Why do you want to join? (optional)</label>
  <textarea id="message" name="message" rows="4"><?= e($old['message']) ?></textarea>
  <?= err($errors, 'message') ?>

  <label class="inline terms">
    <input type="checkbox" name="agree" <?= $old['agree'] ? 'checked' : '' ?>>
    I agree to the academy terms
  </label>
  <?= err($errors, 'agree') ?>

  <button type="submit">Register</button>

  <p class="links"><a href="search.php">Search</a> · <a href="profile.php">Profile</a></p>
</form>
<?php endif; ?>
<script src="/javascript/script.js"></script>
</body>
</html>
