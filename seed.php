<?php
require_once 'db_connect.php';

// Load placeholder signature image (e.g., 2 KB PNG)
$signature_path = __DIR__ . '/signatures/placeholder-signature.png';
$signature_blob = file_exists($signature_path) ? file_get_contents($signature_path) : null;

// Employees data
$employees = [
    [
        'emp_id' => '333',
        'name' => 'جواهر الغامدي',
        'password' => 'Mgr_333',
        'role' => 'manager',
        'last_vac' => '0000-00-00',
        'used_days' => 0,
        'remaining_days' => 0,
    ],
    [
        'emp_id' => '222',
        'name' => 'محمد القحطاني',
        'password' => 'Fin_222',
        'role' => 'finance',
        'last_vac' => '0000-00-00',
        'used_days' => 0,
        'remaining_days' => 0,
    ],
    [
        'emp_id' => '111',
        'name' => 'مشاعل الخالدي',
        'password' => 'Emp_111',
        'role' => 'employee',
        'last_vac' => '2024-07-01',
        'used_days' => 0,
        'remaining_days' => 30,
    ]
];

// Insert employees
foreach ($employees as $emp) {
    $check = $conn->prepare("SELECT emp_id FROM employee WHERE emp_id = ?");
    $check->bind_param("s", $emp['emp_id']);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $insert = $conn->prepare("
            INSERT INTO employee (emp_id, name, password, role, signature, last_vac, used_days, remaining_days)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insert->bind_param(
            "ssssbsii",
            $emp['emp_id'],
            $emp['name'],
            $emp['password'],
            $emp['role'],
            $null,  // temporary, we’ll send BLOB next
            $emp['last_vac'],
            $emp['used_days'],
            $emp['remaining_days']
        );
        $insert->send_long_data(4, $signature_blob);
        $insert->execute();
    }
}

// Vacation entry
$vac_id = 5;
$check = $conn->prepare("SELECT vac_id FROM vacation WHERE vac_id = ?");
$check->bind_param("i", $vac_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    $insert = $conn->prepare("
        INSERT INTO vacation (
            vac_id, emp_id, type, days, start_date, end_date, application_date,
            assigned_emp, fin_approval, man_approval
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $vac = [
        5,
        '111',
        'مرضية',
        9,
        '2025-08-18',
        '2025-08-26',
        '2025-07-09',
        'أحمد محسن',
        'مقبول',
        'معلق'
    ];
    $insert->bind_param("ississssss", ...$vac);
    $insert->execute();
}

echo "<div style='padding:20px;font-family:Arial;color:green;'>✔ تم إدخال البيانات بنجاح (أو كانت موجودة بالفعل)</div>";

$conn->close();
