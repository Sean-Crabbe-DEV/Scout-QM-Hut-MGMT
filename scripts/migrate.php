<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';

$sql = file_get_contents(__DIR__ . '/../database/schema.sql');
if ($sql === false) {
    throw new RuntimeException('Could not read schema.sql');
}
foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
    db()->exec($statement);
}

$roles = [
    ['Admin', 'admin', 'Full access to the whole system.', ['*'], 1],
    ['Group Scout Leader', 'gsl', 'May approve equipment bookings and manage tickets.', ['tickets.manage', 'tickets.close', 'equipment.approve'], 1],
    ['Chairperson', 'chairperson', 'May approve equipment bookings and manage tickets.', ['tickets.manage', 'tickets.close', 'equipment.approve'], 1],
    ['Quartermaster', 'qm', 'May approve equipment bookings and manage tickets.', ['tickets.manage', 'tickets.close', 'equipment.approve'], 1],
    ['Scout User', 'scout_user', 'Authenticated Scout volunteer or member.', ['bookings.request', 'equipment.request', 'tickets.report'], 1],
    ['External User', 'external_user', 'Approved external hirer or contractor.', ['bookings.request', 'equipment.request', 'tickets.report'], 1],
];
foreach ($roles as [$name, $slug, $description, $permissions, $system]) {
    q('INSERT INTO roles (name, slug, description, permissions, is_system) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description), permissions=VALUES(permissions), is_system=VALUES(is_system)', [$name, $slug, $description, json_encode($permissions), $system]);
}

$settings = [
    'group_name' => env('APP_NAME', '1st Sedbury & Tidenham Hut Management'),
    'smtp_host' => '', 'smtp_port' => '587', 'smtp_encryption' => 'tls', 'smtp_username' => '', 'smtp_password' => '',
    'mail_from_address' => env('MAIL_FROM_ADDRESS', 'no-reply@example.org'),
    'mail_from_name' => env('MAIL_FROM_NAME', '1st Sedbury & Tidenham Scouts'),
    'mail_reply_to' => '',
];
foreach ($settings as $key => $value) {
    q('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_key=setting_key', [$key, $value]);
}

echo "Migrations and default roles completed.\n";
