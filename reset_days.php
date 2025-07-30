<?php
require_once("db_connect.php");

// نحدد ملف لتخزين سنة آخر تحديث
$reset_flag_file = "last_reset.txt";
$current_year = date('Y');

// إذا الملف ما وُجد أو كانت السنة فيه أقدم من السنة الحالية
if (!file_exists($reset_flag_file) || file_get_contents($reset_flag_file) !== $current_year) {
    
    // فقط في 1 يناير يتم التحديث
    if (date('m-d') === '01-01') {
        $sql = "UPDATE employee SET remaining_days = 30, used_days = 0";
        if ($conn->query($sql)) {
            file_put_contents($reset_flag_file, $current_year); // نخزن السنة الجديدة
        } else {
            error_log("فشل التحديث: " . $conn->error);
        }
    }
}
?>
