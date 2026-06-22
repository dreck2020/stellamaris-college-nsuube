<?php
// admin/export-inquiries.php - Export admission inquiries to Excel/CSV
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Fetch all inquiries ordered by newest first
$stmt = $conn->query("SELECT * FROM inquiries ORDER BY created_at DESC");
$inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set CSV headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="admission_inquiries_' . date('Y-m-d') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Define column headers
$headers = [
    'ID',
    'Submission Date',
    'Student Name',
    'Parent/Guardian Name',
    'Email',
    'Phone',
    'Applying for Grade',
    'Academic Year',
    'Previous School',
    'Message',
    'Status',
    'Admission Status',
    'Admission Letter'
];

// Write headers to CSV
fputcsv($output, $headers);

// Write each inquiry row
foreach($inquiries as $inquiry) {
    $row = [
        $inquiry['id'],
        date('Y-m-d H:i:s', strtotime($inquiry['created_at'])),
        $inquiry['student_name'],
        $inquiry['parent_name'] ?? '',
        $inquiry['email'],
        $inquiry['phone'],
        $inquiry['grade_applying'],
        $inquiry['academic_year'] ?? '',
        $inquiry['previous_school'] ?? '',
        $inquiry['message'] ?? '',
        $inquiry['status'] ?? 'pending',
        $inquiry['admission_status'] ?? 'pending',
        $inquiry['admission_letter_path'] ?? ''
    ];
    fputcsv($output, $row);
}

fclose($output);
exit();
?>