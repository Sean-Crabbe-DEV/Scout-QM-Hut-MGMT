<?php
declare(strict_types=1);

const APP_ROOT = __DIR__ . '/..';
const STORAGE_PATH = APP_ROOT . '/storage';
const UPLOAD_PATH = STORAGE_PATH . '/uploads';

function load_env(string $path): void
{
    if (!is_file($path)) {
        return;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        $_ENV[$key] = $value;
        putenv($key . '=' . $value);
    }
}

load_env(APP_ROOT . '/.env');

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/London');

if (session_status() === PHP_SESSION_NONE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_name('scout_hut_mgmt');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $https,
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function env(string $key, ?string $default = null): ?string
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $host = env('DB_HOST', '127.0.0.1');
    $port = env('DB_PORT', '3306');
    $name = env('DB_DATABASE', 'scout_hut_mgmt');
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $pdo = new PDO($dsn, env('DB_USERNAME', 'root'), env('DB_PASSWORD', ''), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function q(string $sql, array $params = []): PDOStatement
{
    $statement = db()->prepare($sql);
    $statement->execute($params);
    return $statement;
}

function one(string $sql, array $params = []): ?array
{
    $row = q($sql, $params)->fetch();
    return $row ?: null;
}

function all(string $sql, array $params = []): array
{
    return q($sql, $params)->fetchAll();
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_url(string $path = ''): string
{
    $base = rtrim(env('APP_URL', ''), '/');
    return $base . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . app_url($path));
    exit;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
    $result = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $result;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function validate_csrf(): void
{
    $token = $_POST['_csrf'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Your form expired. Please go back and try again.');
    }
}

function current_user(bool $refresh = false): ?array
{
    static $cached = null;
    if (!$refresh && is_array($cached)) {
        return $cached;
    }
    $id = $_SESSION['user_id'] ?? null;
    if (!$id) {
        return null;
    }
    $user = one('SELECT * FROM users WHERE id = ? AND is_active = 1', [(int)$id]);
    if (!$user) {
        unset($_SESSION['user_id']);
        return null;
    }
    $roles = all('SELECT r.* FROM roles r INNER JOIN user_roles ur ON ur.role_id = r.id WHERE ur.user_id = ? ORDER BY r.name', [(int)$id]);
    $user['roles'] = $roles;
    $user['role_slugs'] = array_column($roles, 'slug');
    $cached = $user;
    return $cached;
}

function logged_in(): bool
{
    return current_user() !== null;
}

function user_has_role(array|string $roles): bool
{
    $user = current_user();
    if (!$user) {
        return false;
    }
    $needed = (array)$roles;
    return (bool)array_intersect($needed, $user['role_slugs']);
}

function is_admin(): bool
{
    return user_has_role('admin');
}

/**
 * People allowed to manage operational hut, equipment and booking records.
 * Keep both legacy GSL and the newer GLV role so existing user accounts
 * continue to work while groups move to the updated Scouts terminology.
 */
function operational_manager_roles(): array
{
    return ['gsl', 'glv', 'chairperson', 'qm'];
}

function is_operational_manager(): bool
{
    return is_admin() || user_has_role(operational_manager_roles());
}

function is_external_user(): bool
{
    return user_has_role('external_user') && !is_operational_manager();
}

function can_manage_tickets(?int $ticketId = null): bool
{
    if (is_operational_manager()) {
        return true;
    }
    if (!$ticketId || !logged_in()) {
        return false;
    }
    return (bool)one('SELECT 1 FROM ticket_assignees WHERE ticket_id = ? AND user_id = ?', [$ticketId, current_user()['id']]);
}

function can_close_tickets(): bool
{
    return is_operational_manager();
}

function can_approve_equipment(): bool
{
    return is_operational_manager();
}

function can_manage_equipment(): bool
{
    return is_operational_manager();
}

function can_delete_equipment(): bool
{
    return is_operational_manager();
}

function equipment_status_options(): array
{
    return ['Available', 'Booked', 'Damaged', 'In repair', 'Disposed of'];
}

function can_manage_hut_bookings(): bool
{
    return is_operational_manager();
}

function can_delete_bookings(): bool
{
    return is_operational_manager();
}

function booking_blocks_space(string $status): bool
{
    return in_array($status, ['Approved', 'Confirmed'], true);
}

function require_login(): void
{
    if (!logged_in()) {
        flash('error', 'Please log in to continue.');
        redirect('/login');
    }
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        exit('You do not have permission to access this page.');
    }
}

function can_view_ticket(array $ticket): bool
{
    if (is_operational_manager()) {
        return true;
    }
    $user = current_user();
    if (!$user) {
        return false;
    }
    if ((int)$ticket['reporter_user_id'] === (int)$user['id']) {
        return true;
    }
    return (bool)one('SELECT 1 FROM ticket_assignees WHERE ticket_id = ? AND user_id = ?', [$ticket['id'], $user['id']]);
}

function audit(string $action, ?string $relatedType = null, ?int $relatedId = null, array $details = []): void
{
    $user = current_user();
    q('INSERT INTO audit_logs (user_id, action, related_type, related_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)', [
        $user['id'] ?? null,
        $action,
        $relatedType,
        $relatedId,
        $details ? json_encode($details, JSON_UNESCAPED_UNICODE) : null,
        $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
}

function setting(string $key, ?string $default = null): ?string
{
    static $cache = [];
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }
    $row = one('SELECT setting_value FROM settings WHERE setting_key = ?', [$key]);
    $cache[$key] = $row['setting_value'] ?? $default;
    return $cache[$key];
}

function save_setting(string $key, string $value): void
{
    q('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)', [$key, $value]);
}

function crypt_value(string $value): string
{
    $key = hash('sha256', (string)env('APP_KEY'), true);
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($value, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return 'enc:' . base64_encode($iv . $encrypted);
}

function decrypt_value(?string $value): string
{
    if (!$value || !str_starts_with($value, 'enc:')) {
        return (string)$value;
    }
    $payload = base64_decode(substr($value, 4), true);
    if ($payload === false || strlen($payload) < 17) {
        return '';
    }
    $key = hash('sha256', (string)env('APP_KEY'), true);
    $iv = substr($payload, 0, 16);
    $cipher = substr($payload, 16);
    return (string)openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

function reference(string $prefix): string
{
    return sprintf('%s-%s-%04d', $prefix, date('Y'), random_int(1, 9999));
}

function unique_reference(string $prefix, string $table): string
{
    do {
        $ref = reference($prefix);
    } while (one("SELECT 1 FROM {$table} WHERE reference = ?", [$ref]));
    return $ref;
}

function file_extension(string $filename): string
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function store_ticket_upload(array $file, int $ticketId, ?int $updateId = null): ?int
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The file could not be uploaded.');
    }
    $max = ((int)env('UPLOAD_MAX_MB', '8')) * 1024 * 1024;
    if (($file['size'] ?? 0) > $max) {
        throw new RuntimeException('Uploads must be no larger than ' . (int)env('UPLOAD_MAX_MB', '8') . ' MB.');
    }
    $allowed = [
        'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp',
        'application/pdf' => 'pdf', 'text/plain' => 'txt',
    ];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG, WebP, PDF and TXT files are accepted.');
    }
    if (!is_dir(UPLOAD_PATH) && !mkdir(UPLOAD_PATH, 0750, true) && !is_dir(UPLOAD_PATH)) {
        throw new RuntimeException('Upload storage is not available.');
    }
    $stored = bin2hex(random_bytes(20)) . '.' . $allowed[$mime];
    $target = UPLOAD_PATH . '/' . $stored;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('The uploaded file could not be saved.');
    }
    q('INSERT INTO attachments (ticket_id, ticket_update_id, original_name, storage_name, mime_type, size_bytes) VALUES (?, ?, ?, ?, ?, ?)', [
        $ticketId, $updateId, mb_substr((string)$file['name'], 0, 255), $stored, $mime, (int)$file['size'],
    ]);
    return (int)db()->lastInsertId();
}

function normalise_uploaded_files(array $files): array
{
    if (!isset($files['name'])) {
        return [];
    }
    if (!is_array($files['name'])) {
        return [$files];
    }
    $normalised = [];
    foreach ($files['name'] as $index => $name) {
        $normalised[] = [
            'name' => $name,
            'type' => $files['type'][$index] ?? '',
            'tmp_name' => $files['tmp_name'][$index] ?? '',
            'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
            'size' => $files['size'][$index] ?? 0,
        ];
    }
    return $normalised;
}

function store_ticket_uploads(array $files, int $ticketId, ?int $updateId = null): array
{
    $attachmentIds = [];
    foreach (normalise_uploaded_files($files) as $file) {
        $attachmentId = store_ticket_upload($file, $ticketId, $updateId);
        if ($attachmentId !== null) {
            $attachmentIds[] = $attachmentId;
        }
    }
    return $attachmentIds;
}

function store_equipment_uploads(array $files, int $equipmentId): array
{
    $storedIds = [];
    $allowed = [
        'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp',
        'application/pdf' => 'pdf', 'text/plain' => 'txt',
    ];
    $max = ((int)env('UPLOAD_MAX_MB', '8')) * 1024 * 1024;
    if (!is_dir(UPLOAD_PATH) && !mkdir(UPLOAD_PATH, 0750, true) && !is_dir(UPLOAD_PATH)) {
        throw new RuntimeException('Upload storage is not available.');
    }
    foreach (normalise_uploaded_files($files) as $file) {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('One or more files could not be uploaded.');
        }
        if (($file['size'] ?? 0) > $max) {
            throw new RuntimeException('Uploads must be no larger than ' . (int)env('UPLOAD_MAX_MB', '8') . ' MB.');
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            throw new RuntimeException('Only JPG, PNG, WebP, PDF and TXT files are accepted.');
        }
        $storageName = 'equipment_' . bin2hex(random_bytes(20)) . '.' . $allowed[$mime];
        if (!move_uploaded_file($file['tmp_name'], UPLOAD_PATH . '/' . $storageName)) {
            throw new RuntimeException('An uploaded equipment file could not be saved.');
        }
        q('INSERT INTO equipment_attachments (equipment_id, original_name, storage_name, mime_type, size_bytes) VALUES (?, ?, ?, ?, ?)', [
            $equipmentId,
            mb_substr((string)$file['name'], 0, 255),
            $storageName,
            $mime,
            (int)$file['size'],
        ]);
        $storedIds[] = (int)db()->lastInsertId();
    }
    return $storedIds;
}

function store_asset_photo(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The photo could not be uploaded.');
    }
    if (($file['size'] ?? 0) > ((int)env('UPLOAD_MAX_MB', '8') * 1024 * 1024)) {
        throw new RuntimeException('Photo upload is too large.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime])) {
        throw new RuntimeException('Only JPG, PNG and WebP images are accepted.');
    }
    if (!is_dir(UPLOAD_PATH) && !mkdir(UPLOAD_PATH, 0750, true) && !is_dir(UPLOAD_PATH)) {
        throw new RuntimeException('Upload storage is not available.');
    }
    $stored = 'asset_' . bin2hex(random_bytes(20)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_PATH . '/' . $stored)) {
        throw new RuntimeException('The uploaded photo could not be saved.');
    }
    return $stored;
}

function email_html(string $heading, string $content, ?string $buttonLabel = null, ?string $buttonUrl = null): string
{
    $brand = e(setting('group_name', env('APP_NAME', 'Scout Hut Management')));
    $button = ($buttonLabel && $buttonUrl) ? '<p><a href="' . e($buttonUrl) . '" style="display:inline-block;padding:12px 18px;background:#ED3F23;color:#fff;text-decoration:none;border-radius:6px;font-weight:700">' . e($buttonLabel) . '</a></p>' : '';
    return '<!doctype html><html><body style="font-family:Arial,sans-serif;background:#f5f5f5;padding:24px;color:#1d1d1b"><div style="max-width:650px;margin:auto;background:#fff;border-top:6px solid #7413DC;padding:28px"><p style="font-weight:800;color:#ED3F23;margin:0 0 8px">' . $brand . '</p><h1 style="margin:0 0 18px;font-size:26px">' . e($heading) . '</h1>' . $content . $button . '<hr style="border:none;border-top:1px solid #ddd;margin:24px 0"><p style="font-size:12px;color:#555">This is an automated message from the Hut Management System.</p></div></body></html>';
}

function send_email(string $to, string $subject, string $html, ?string $relatedType = null, ?int $relatedId = null): bool
{
    $status = 'Skipped';
    $error = null;
    try {
        $host = setting('smtp_host', '');
        if (!$host) {
            throw new RuntimeException('SMTP is not configured.');
        }
        $status = 'Failed';
        $autoload = APP_ROOT . '/vendor/autoload.php';
        if (!is_file($autoload)) {
            throw new RuntimeException('Composer dependencies are missing.');
        }
        require_once $autoload;
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = (int)(setting('smtp_port', '587') ?: 587);
        $encryption = setting('smtp_encryption', 'tls');
        if ($encryption === 'ssl') {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'tls') {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->SMTPAuth = setting('smtp_username', '') !== '';
        $mail->Username = (string)setting('smtp_username', '');
        $mail->Password = decrypt_value(setting('smtp_password', ''));
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        $mail->setFrom((string)(setting('mail_from_address', env('MAIL_FROM_ADDRESS', 'no-reply@example.org'))), (string)(setting('mail_from_name', env('MAIL_FROM_NAME', 'Scout Hut Management'))));
        $reply = setting('mail_reply_to', '');
        if ($reply) {
            $mail->addReplyTo($reply);
        }
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $html;
        $mail->AltBody = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html)));
        $mail->send();
        $status = 'Sent';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
        $status = $status === 'Skipped' ? 'Skipped' : 'Failed';
    }
    q('INSERT INTO email_logs (related_type, related_id, recipient_email, subject_line, status, error_message) VALUES (?, ?, ?, ?, ?, ?)', [$relatedType, $relatedId, $to, $subject, $status, $error]);
    return $status === 'Sent';
}

function core_notification_users(): array
{
    return all("SELECT DISTINCT u.* FROM users u JOIN user_roles ur ON ur.user_id=u.id JOIN roles r ON r.id=ur.role_id WHERE u.is_active=1 AND r.slug IN ('admin','gsl','glv','chairperson','qm')");
}

function notify_ticket_created(array $ticket): void
{
    $track = app_url('/track/' . $ticket['public_token']);
    send_email($ticket['reporter_email'], 'Ticket ' . $ticket['reference'] . ' received', email_html('We have received your report', '<p>Your reference is <strong>' . e($ticket['reference']) . '</strong>.</p><p>We will review: ' . e($ticket['title']) . '.</p>', 'View your report', $track), 'ticket', (int)$ticket['id']);
    foreach (core_notification_users() as $user) {
        send_email($user['email'], 'New ' . $ticket['priority'] . ' ticket: ' . $ticket['reference'], email_html('New ticket reported', '<p><strong>' . e($ticket['reference']) . '</strong> — ' . e($ticket['title']) . '</p><p>' . nl2br(e($ticket['description'])) . '</p>', 'Open ticket', app_url('/tickets/' . $ticket['id'])), 'ticket', (int)$ticket['id']);
    }
}

function notify_ticket_update(array $ticket, string $message, bool $isInternal, ?int $actorUserId = null): void
{
    if (!$isInternal) {
        send_email($ticket['reporter_email'], 'Update on ticket ' . $ticket['reference'], email_html('Your ticket has been updated', '<p><strong>' . e($ticket['reference']) . '</strong> — ' . e($ticket['title']) . '</p><p>' . nl2br(e($message)) . '</p>', 'View ticket', app_url('/track/' . $ticket['public_token'])), 'ticket', (int)$ticket['id']);
    }
    $recipients = all("SELECT DISTINCT u.* FROM users u JOIN ticket_assignees ta ON ta.user_id=u.id WHERE ta.ticket_id=?", [$ticket['id']]);
    foreach ($recipients as $user) {
        if ((int)$user['id'] === $actorUserId) {
            continue;
        }
        send_email($user['email'], 'Ticket update: ' . $ticket['reference'], email_html('Ticket update', '<p>' . nl2br(e($message)) . '</p>', 'Open ticket', app_url('/tickets/' . $ticket['id'])), 'ticket', (int)$ticket['id']);
    }
}

function ticket_total_cost(int $ticketId): float
{
    return (float)(one('SELECT COALESCE(SUM(total_cost),0) AS total FROM maintenance_records WHERE ticket_id = ?', [$ticketId])['total'] ?? 0);
}
