<?php
require 'Registration.php';//يستدعي الملف الحاوي على الكلاس Registration لكي يتعرف PHP على نوع البيانات المسجلة.
require 'helpers.php';//ستدعي الملف الذي يحتوي على دالة الحماية e() (المختصرة لـ htmlspecialchars).
session_start();//بدأ أو يستأنف جلسة المستخدم الحالية للقراءة منها أو التعديل عليها.

// Clear session + cookies
if (isset($_GET['clear'])) {//: يفحص ما إذا كان هناك بارامتر في الرابط اسمه clear
    $_SESSION = [];//يفرغ مصفوفة الجلسة تماماً من أية بيانات مخزنة.
    session_destroy(); //يدمر ملف الجلسة المخزن على السيرفر.
    setcookie('reg_name', '', time() - 3600, '/');//حذف الكوكيز
    setcookie('reg_track', '', time() - 3600, '/');
    header('Location: profile.php');//يعيد توجيه المستخدم لصفحة الملف الشخصي بدون ?clear=1 للبدء بحالة نظيفة.
    exit;//ينهي تنفيذ الاسكريبت فوراً لمنع قراءة باقي الملف.
}
$r = $_SESSION['registration'] ?? null;//يحاول قراءة كائن التسجيل المخزن في الجلسة $_SESSION['registration']. إذا لم يكن موجوداً، يضع القيمة الافتراضية null.
?>
<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Profile</title><link rel="stylesheet" href="css/style.css">
</head><body>
<main class="card">
  <h1>My registration</h1>
  //عرض بيانات الجلسة
  <?php if ($r instanceof Registration): ?>//يتأكد أن الكائن المأخوذ من الجلسة هو فعلياً مأخوذ من كلاس Registration.
    <dl> //تطبع قيم بعد حمايتها عن طريق e()
      <dt>Name</dt><dd><?php e($r->name) ?></dd>
      <dt>Email</dt><dd><?php e($r->email) ?></dd>
      <dt>Mobile</dt><dd><?php e($r->mobile) ?></dd>
      <dt>Governorate</dt><dd><?php e($r->governorate) ?></dd>
      <dt>Track</dt><dd><?php e($r->track) ?></dd>
      <dt>Skills</dt><dd><?php e($r->skillsText()) ?></dd>//يستدعي دالة من الكلاس لتحويل مصفوفة المهارات إلى نص مطبوع.
      //حمي نص الرسالة من ثغرات XSS أولاً عبر e()، ثم يحول أسطر الإدخال الجديدة (New lines) إلى وسوم <br> لتنسيق الفقرات.
      <dt>Message</dt><dd><?php $r->message !== '' ? nl2br(e($r->message)) : '-' ?></dd>
    </dl>
    <p class="note">Data above comes from the <strong>session</strong>.</p>
  <?php else: ?>
    <p>No registration in this session yet. <a href="register.php">Register now</a>.</p>
  <?php endif; ?>

  <?php if (isset($_COOKIE['reg_name'])): ?>
    <p class="note">Cookie: <?= e($_COOKIE['reg_name']) ?> / <?= e($_COOKIE['reg_track'] ?? '-') ?></p>
  <?php endif; ?>
  <p class="links"><a href="register.php">Register</a>
   · <a href="search.php">Search</a>
    · <a href="profile.php?clear=1">Clear session &amp; cookies</a></p>
<script src="/javascript/script.js"></script>
</body>
</html>
