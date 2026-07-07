<?php
declare(strict_types=1);
require __DIR__ . '/app/bootstrap.php';

$today = date('Y-m-d');
$soon = date('Y-m-d', strtotime('+7 days'));
$users = core_notification_users();

$inspections = all("SELECT 'Equipment' AS type, id, name, next_inspection_date AS due_date FROM equipment WHERE next_inspection_date BETWEEN ? AND ? UNION ALL SELECT 'Hut area' AS type, id, name, next_inspection_date AS due_date FROM hut_areas WHERE next_inspection_date BETWEEN ? AND ?", [$today, $soon, $today, $soon]);
foreach ($inspections as $inspection) {
    foreach ($users as $user) {
        send_email($user['email'], 'Inspection due: ' . $inspection['name'], email_html('Inspection due soon', '<p><strong>' . e($inspection['name']) . '</strong> (' . e($inspection['type']) . ') is due on ' . e($inspection['due_date']) . '.</p>', 'Open system', app_url('/')), 'inspection', (int)$inspection['id']);
    }
}

$overdue = all("SELECT * FROM tickets WHERE due_date < ? AND status NOT IN ('Resolved','Closed','Cancelled')", [$today]);
foreach ($overdue as $ticket) {
    foreach (core_notification_users() as $user) {
        send_email($user['email'], 'Overdue ticket: ' . $ticket['reference'], email_html('Ticket overdue', '<p><strong>' . e($ticket['reference']) . '</strong> is overdue.</p>', 'Open ticket', app_url('/tickets/' . $ticket['id'])), 'ticket', (int)$ticket['id']);
    }
}

echo "Reminder task complete.\n";
