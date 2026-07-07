<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';

function column_exists(string $table, string $column): bool
{
    return (bool)one('SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?', [$table, $column]);
}

$sql = file_get_contents(__DIR__ . '/../database/schema.sql');
if ($sql === false) throw new RuntimeException('Could not read schema.sql');
foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
    try { db()->exec($statement); }
    catch (PDOException $exception) {
        $message = strtolower($exception->getMessage());
        if (!str_contains($message, 'duplicate key name') && !str_contains($message, 'already exists')) throw $exception;
    }
}
if (column_exists('hut_areas', 'current_condition')) db()->exec('ALTER TABLE hut_areas DROP COLUMN current_condition');
if (!column_exists('hut_bookings', 'whole_site')) db()->exec('ALTER TABLE hut_bookings ADD COLUMN whole_site TINYINT(1) NOT NULL DEFAULT 0 AFTER hut_area_id');
db()->exec("CREATE TABLE IF NOT EXISTS hut_booking_areas (hut_booking_id INT UNSIGNED NOT NULL, hut_area_id INT UNSIGNED NOT NULL, PRIMARY KEY (hut_booking_id,hut_area_id), CONSTRAINT fk_hut_booking_areas_booking FOREIGN KEY (hut_booking_id) REFERENCES hut_bookings(id) ON DELETE CASCADE, CONSTRAINT fk_hut_booking_areas_area FOREIGN KEY (hut_area_id) REFERENCES hut_areas(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$roles = [
 ['Admin','admin','Full access to the whole system.',['*'],1],
 ['Group Scout Leader','gsl','May approve equipment bookings and manage tickets.',['tickets.manage','tickets.close','equipment.approve'],1],
 ['Chairperson','chairperson','May approve equipment bookings and manage tickets.',['tickets.manage','tickets.close','equipment.approve'],1],
 ['Quartermaster','qm','May approve equipment bookings and manage tickets.',['tickets.manage','tickets.close','equipment.approve'],1],
 ['Scout User','scout_user','Authenticated Scout volunteer or member.',['bookings.request','equipment.request','tickets.report'],1],
 ['External User','external_user','Approved external hirer or contractor.',['bookings.request','equipment.request','tickets.report'],1],
];
foreach($roles as [$name,$slug,$description,$permissions,$system]) q('INSERT INTO roles (name,slug,description,permissions,is_system) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name),description=VALUES(description),permissions=VALUES(permissions),is_system=VALUES(is_system)',[$name,$slug,$description,json_encode($permissions),$system]);
$settings=['group_name'=>env('APP_NAME','1st Sedbury & Tidenham Scouts'),'whole_site_enabled'=>'1','smtp_host'=>'','smtp_port'=>'587','smtp_encryption'=>'tls','smtp_username'=>'','smtp_password'=>'','mail_from_address'=>env('MAIL_FROM_ADDRESS','no-reply@example.org'),'mail_from_name'=>env('MAIL_FROM_NAME','1st Sedbury & Tidenham Scouts'),'mail_reply_to'=>''];
foreach($settings as $key=>$value) q('INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_key=setting_key',[$key,$value]);
echo "Migrations and default roles completed.\n";
