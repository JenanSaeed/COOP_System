<?php
session_start();
session_unset(); // حذف كل بيانات الجلسة
session_destroy(); // إنهاء الجلسة

// توجيه إلى الصفحة الرئيسية
header("Location: homepage.php");
exit();