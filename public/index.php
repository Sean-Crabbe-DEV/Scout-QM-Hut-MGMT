<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';

function page_start(string $title, bool $public = false): void
{
    $user = current_user();
    $group = setting('group_name', env('APP_NAME', 'Scout Hut Management'));
    $success = flash('success');
    $error = flash('error');
    $logoSvg = is_file(__DIR__ . '/assets/brand/group-logo-red.svg') ? '/assets/brand/group-logo-red.svg' : (is_file(__DIR__ . '/assets/brand/group-logo-red.png') ? '/assets/brand/group-logo-red.png' : null);
    ?><!doctype html>
    <html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($title) ?> · <?= e($group) ?></title><link rel="stylesheet" href="/assets/css/app.css">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,400;6..12,600;6..12,700;6..12,800;6..12,900&display=swap" rel="stylesheet"></head><body>
    <header class="topbar"><div class="brand"><?php if ($logoSvg): ?><img src="<?= e($logoSvg) ?>" alt="<?= e($group) ?>"><?php else: ?><span class="brand-mark">⚜</span><?php endif; ?><div><strong><?= e($group) ?></strong><span>Hut Management</span></div></div>
    <div class="top-actions"><?php if ($user): ?><span class="muted">Signed in as <?= e($user['name']) ?></span><form method="post" action="/logout" class="inline"><?= csrf_field() ?><button class="link-button">Sign out</button></form><?php else: ?><a href="/login">Log in</a><?php endif; ?></div></header>
    <?php if (!$public && $user): ?>
    <aside class="sidebar"><nav>
      <a href="/dashboard">Dashboard</a><a href="/bookings">Bookings</a><a href="/tickets">Tickets</a><a href="/hut">Hut</a><a href="/equipment">Equipment</a><a href="/equipment-bookings">Equipment bookings</a>
      <?php if (is_admin()): ?><hr><a href="/users">Users</a><a href="/roles">Groups &amp; roles</a><a href="/settings">System settings</a><?php endif; ?>
    </nav></aside><main class="content">
    <?php else: ?><main class="public-content"><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <?php
}

function page_end(bool $public = false): void
{
    ?></main><footer class="footer">1st Sedbury &amp; Tidenham Scouts · Hut Management System · Part of Scouts in Wales</footer></body></html><?php
}

function heading(string $title, string $intro = '', ?string $actionLabel = null, ?string $actionHref = null): void
{
    ?><div class="page-heading"><div><h1><?= e($title) ?></h1><?php if ($intro): ?><p><?= e($intro) ?></p><?php endif; ?></div><?php if ($actionLabel && $actionHref): ?><a class="button primary" href="<?= e($actionHref) ?>"><?= e($actionLabel) ?></a><?php endif; ?></div><?php
}

function status_badge(string $status): string
{
    $class = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $status));
    return '<span class="badge badge-' . e(trim($class, '-')) . '">' . e($status) . '</span>';
}

function is_setup_available(): bool
{
    return (int)(one('SELECT COUNT(*) AS count FROM users')['count'] ?? 0) === 0;
}

function parse_route(): array
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return [rtrim($path, '/') ?: '/', strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET')];
}

function created_source(): string
{
    if (!logged_in()) return 'Public';
    return is_admin() ? 'Admin' : (user_has_role('external_user') ? 'External user' : 'Scout user');
}

function ticket_form(array $areas, array $equipment, bool $public = false): void
{
    ?><form method="post" enctype="multipart/form-data" class="card form-grid">
      <?= csrf_field() ?>
      <?php if ($public): ?><input type="text" name="website" class="honeypot" tabindex="-1" autocomplete="off"><div><label>Your name <span>*</span></label><input name="reporter_name" required maxlength="150"></div><div><label>Email address <span>*</span></label><input type="email" name="reporter_email" required maxlength="190"></div><?php endif; ?>
      <div><label>What is the problem? <span>*</span></label><input name="title" required maxlength="200" placeholder="For example: Kitchen tap leaking"></div>
      <div><label>Nature of problem <span>*</span></label><select name="category" required><option value="">Choose one</option><?php foreach (['Hut building','Toilets','Kitchen','Heating or hot water','Electrical issue','Lighting','Doors, windows or locks','Water leak or plumbing','Fire safety equipment','Security concern','Equipment fault','Grounds or outside area','Cleaning or hygiene','Other'] as $category): ?><option><?= e($category) ?></option><?php endforeach; ?></select></div>
      <div><label>Related to</label><select name="linked_type" id="linked_type"><option value="Other">Other / not sure</option><option value="Hut">Hut</option><option value="Equipment">Equipment</option></select></div>
      <div><label>Hut area</label><select name="hut_area_id"><option value="">Not applicable / not sure</option><?php foreach ($areas as $area): ?><option value="<?= $area['id'] ?>"><?= e($area['name']) ?></option><?php endforeach; ?></select></div>
      <div><label>Equipment item</label><select name="equipment_id"><option value="">Not applicable / not sure</option><?php foreach ($equipment as $item): ?><option value="<?= $item['id'] ?>"><?= e($item['asset_id'] . ' — ' . $item['name']) ?></option><?php endforeach; ?></select></div>
      <div><label>Location details</label><input name="location_text" maxlength="255" placeholder="For example: Back wall beside the kitchen door"></div>
      <div><label>Urgency <span>*</span></label><select name="priority" required><?php foreach (['Low','Normal','High','Urgent','Emergency'] as $priority): ?><option <?= $priority === 'Normal' ? 'selected' : '' ?>><?= $priority ?></option><?php endforeach; ?></select></div>
      <div class="full"><label>Description <span>*</span></label><textarea name="description" rows="6" required placeholder="Tell us what has happened, where it is and anything that may help us fix it."></textarea></div>
      <div class="full"><label>Add photo or file</label><input type="file" name="attachment" accept="image/jpeg,image/png,image/webp,application/pdf,text/plain"><small>JPG, PNG, WebP, PDF or TXT. Maximum <?= e(env('UPLOAD_MAX_MB', '8')) ?> MB.</small></div>
      <div class="full"><button class="button primary" type="submit">Submit report</button></div>
    </form><?php
}

function create_ticket_from_request(bool $public): void
{
    validate_csrf();
    if ($public && !empty($_POST['website'])) {
        http_response_code(422); exit('Unable to submit this report.');
    }
    $user = current_user();
    $reporterName = trim((string)($_POST['reporter_name'] ?? $user['name'] ?? ''));
    $reporterEmail = strtolower(trim((string)($_POST['reporter_email'] ?? $user['email'] ?? '')));
    $title = trim((string)($_POST['title'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    if ($reporterName === '' || !filter_var($reporterEmail, FILTER_VALIDATE_EMAIL) || $title === '' || $description === '') {
        throw new RuntimeException('Please complete your name, email, title and description.');
    }
    $area = (int)($_POST['hut_area_id'] ?? 0) ?: null;
    $equipment = (int)($_POST['equipment_id'] ?? 0) ?: null;
    $linked = $equipment ? 'Equipment' : ($area ? 'Hut' : 'Other');
    $reference = unique_reference('HUT', 'tickets');
    $token = bin2hex(random_bytes(32));
    q('INSERT INTO tickets (reference,title,source,reporter_name,reporter_email,reporter_user_id,category,linked_type,hut_area_id,equipment_id,location_text,priority,description,public_token) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [
        $reference, $title, created_source(), $reporterName, $reporterEmail, $user['id'] ?? null, trim((string)$_POST['category']), $linked, $area, $equipment, trim((string)($_POST['location_text'] ?? '')) ?: null, $_POST['priority'] ?? 'Normal', $description, $token
    ]);
    $id = (int)db()->lastInsertId();
    if (!empty($_FILES['attachment'])) {
        store_ticket_upload($_FILES['attachment'], $id);
    }
    q('INSERT INTO ticket_updates (ticket_id,user_id,body,is_internal,new_status) VALUES (?,?,?,?,?)', [$id, $user['id'] ?? null, 'Ticket reported.', 0, 'New']);
    $ticket = one('SELECT * FROM tickets WHERE id=?', [$id]);
    audit('Ticket created', 'ticket', $id, ['reference' => $reference, 'source' => $ticket['source']]);
    notify_ticket_created($ticket);
    if ($public) {
        flash('success', 'Your problem has been reported. Your reference is ' . $reference . '. Check your email for a secure tracking link.');
        redirect('/report-problem');
    }
    flash('success', 'Ticket ' . $reference . ' was created.');
    redirect('/tickets/' . $id);
}

function render_ticket_table(array $tickets): void
{
    ?><div class="table-wrap"><table><thead><tr><th>Reference</th><th>Issue</th><th>Related to</th><th>Priority</th><th>Status</th><th>Assigned</th><th>Cost</th><th>Updated</th></tr></thead><tbody>
    <?php foreach ($tickets as $ticket): ?><tr><td><a href="/tickets/<?= $ticket['id'] ?>"><strong><?= e($ticket['reference']) ?></strong></a></td><td><?= e($ticket['title']) ?><small><?= e($ticket['category']) ?></small></td><td><?= e($ticket['area_name'] ?: ($ticket['equipment_name'] ?: $ticket['linked_type'])) ?></td><td><?= status_badge($ticket['priority']) ?></td><td><?= status_badge($ticket['status']) ?></td><td><?= e($ticket['assignees'] ?: 'Unassigned') ?></td><td>£<?= number_format((float)$ticket['total_cost'], 2) ?></td><td><?= e(date('d M Y H:i', strtotime($ticket['updated_at']))) ?></td></tr><?php endforeach; ?>
    <?php if (!$tickets): ?><tr><td colspan="8" class="muted">No tickets found.</td></tr><?php endif; ?></tbody></table></div><?php
}

[$path, $method] = parse_route();
try {
    if ($path === '/' && $method === 'GET') {
        if (logged_in()) redirect('/dashboard');
        page_start('Welcome', true); ?>
        <section class="hero"><p class="eyebrow">1st Sedbury &amp; Tidenham Scouts</p><h1>Hut Management</h1><p>Bookings, maintenance, equipment and a simple way to report problems.</p><div class="actions"><a class="button primary" href="/report-problem">Report a problem</a><a class="button secondary" href="/login">Log in</a></div></section>
        <section class="card-grid"><article class="card"><h2>Report a problem</h2><p>Let us know about faults, damage, safety concerns or equipment issues. Photos help.</p></article><article class="card"><h2>Hut bookings</h2><p>Authorised users can request spaces and track upcoming bookings.</p></article><article class="card"><h2>Equipment</h2><p>Keep kit condition, history and approvals in one place.</p></article></section>
        <?php if (is_setup_available()): ?><div class="alert info">This is a new installation. <a href="/setup">Set up the first Admin account</a>.</div><?php endif; ?>
        <?php page_end(true); exit;
    }

    if ($path === '/setup') {
        if (!is_setup_available()) { flash('error', 'Initial setup has already been completed.'); redirect('/login'); }
        if ($method === 'POST') {
            validate_csrf();
            $name = trim((string)($_POST['name'] ?? '')); $email = strtolower(trim((string)($_POST['email'] ?? ''))); $password = (string)($_POST['password'] ?? '');
            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) throw new RuntimeException('Enter your name, a valid email address and a password of at least 12 characters.');
            q('INSERT INTO users (name,email,password_hash) VALUES (?,?,?)', [$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
            $userId = (int)db()->lastInsertId(); $adminRole = one("SELECT id FROM roles WHERE slug='admin'");
            q('INSERT INTO user_roles (user_id,role_id) VALUES (?,?)', [$userId,$adminRole['id']]);
            $_SESSION['user_id'] = $userId; audit('First Admin account created','user',$userId); flash('success','Admin account created. Configure your system settings and SMTP email next.'); redirect('/dashboard');
        }
        page_start('Set up first Admin', true); heading('Set up the first Admin', 'This option disappears as soon as the first account is created.'); ?>
        <form method="post" class="card form-grid narrow"><?= csrf_field() ?><div><label>Full name</label><input name="name" required></div><div><label>Email address</label><input name="email" type="email" required></div><div><label>Create a strong password</label><input name="password" type="password" minlength="12" required><small>Use at least 12 characters.</small></div><button class="button primary">Create Admin account</button></form><?php page_end(true); exit;
    }

    if ($path === '/login') {
        if ($method === 'POST') {
            validate_csrf(); $email = strtolower(trim((string)($_POST['email'] ?? ''))); $password = (string)($_POST['password'] ?? '');
            $user = one('SELECT * FROM users WHERE email=? AND is_active=1', [$email]);
            if (!$user || !password_verify($password,$user['password_hash'])) { audit('Failed login'); flash('error','Email or password was not recognised.'); redirect('/login'); }
            session_regenerate_id(true); $_SESSION['user_id'] = $user['id']; q('UPDATE users SET last_login_at=NOW() WHERE id=?',[$user['id']]); audit('Logged in','user',$user['id']); redirect('/dashboard');
        }
        page_start('Log in', true); heading('Log in', 'Use your authorised Hut Management account.'); ?>
        <form method="post" class="card form-grid narrow"><?= csrf_field() ?><div><label>Email address</label><input type="email" name="email" required autocomplete="email"></div><div><label>Password</label><input type="password" name="password" required autocomplete="current-password"></div><button class="button primary">Log in</button></form><p class="muted">Need an account? Ask an Admin to invite you.</p><?php page_end(true); exit;
    }

    if ($path === '/logout' && $method === 'POST') { validate_csrf(); audit('Logged out'); $_SESSION = []; session_destroy(); redirect('/'); }

    if ($path === '/report-problem') {
        if ($method === 'POST') { create_ticket_from_request(true); }
        page_start('Report a problem', true); heading('Report a problem at the Scout Hut', 'Help us keep the hut safe, clean and ready for everyone.'); ?>
        <div class="alert warning"><strong>Emergency or immediate danger?</strong> Call 999 first for fire, serious injury, major flooding, a suspected gas leak or serious electrical danger. Do not rely on this form for emergency response.</div>
        <?php ticket_form(all('SELECT * FROM hut_areas ORDER BY name'), all('SELECT * FROM equipment WHERE current_status NOT IN ("Disposed","Lost") ORDER BY name'), true); page_end(true); exit;
    }

    if (preg_match('#^/track/([a-f0-9]{64})$#', $path, $m) && $method === 'GET') {
        $ticket = one('SELECT * FROM tickets WHERE public_token=?', [$m[1]]); if (!$ticket) { http_response_code(404); exit('Ticket not found.'); }
        $updates = all('SELECT tu.*,u.name FROM ticket_updates tu LEFT JOIN users u ON u.id=tu.user_id WHERE tu.ticket_id=? AND tu.is_internal=0 ORDER BY tu.created_at ASC',[$ticket['id']]);
        page_start('Track ' . $ticket['reference'], true); heading('Ticket ' . $ticket['reference'], 'You can use this private link to see updates on your report.'); ?>
        <div class="card detail-grid"><div><span class="label">Issue</span><strong><?= e($ticket['title']) ?></strong></div><div><span class="label">Status</span><?= status_badge($ticket['status']) ?></div><div><span class="label">Priority</span><?= status_badge($ticket['priority']) ?></div><div><span class="label">Reported</span><?= e(date('d M Y H:i',strtotime($ticket['created_at']))) ?></div><div class="full"><span class="label">Description</span><p><?= nl2br(e($ticket['description'])) ?></p></div></div>
        <h2>Updates</h2><div class="timeline"><?php foreach ($updates as $update): ?><article><strong><?= e($update['name'] ?: 'Hut Management') ?></strong><time><?= e(date('d M Y H:i',strtotime($update['created_at']))) ?></time><p><?= nl2br(e($update['body'])) ?></p></article><?php endforeach; ?></div><?php page_end(true); exit;
    }

    require_login();

    if ($path === '/dashboard' && $method === 'GET') {
        $open = one("SELECT COUNT(*) AS total FROM tickets WHERE status NOT IN ('Resolved','Closed','Cancelled')")['total'];
        $urgent = one("SELECT COUNT(*) AS total FROM tickets WHERE priority IN ('Urgent','Emergency') AND status NOT IN ('Resolved','Closed','Cancelled')")['total'];
        $equipmentPending = one("SELECT COUNT(*) AS total FROM equipment_bookings WHERE status IN ('Requested','Awaiting approval')")['total'];
        $bookingCount = one("SELECT COUNT(*) AS total FROM hut_bookings WHERE starts_at >= NOW() AND status IN ('Approved','Confirmed')")['total'];
        page_start('Dashboard'); heading('Dashboard', 'A quick view of the hut, equipment and jobs that need attention.'); ?>
        <section class="metrics"><a class="metric purple" href="/tickets"><span>Open tickets</span><strong><?= e((string)$open) ?></strong><small>View tickets</small></a><a class="metric red" href="/tickets"><span>Urgent issues</span><strong><?= e((string)$urgent) ?></strong><small>Needs action</small></a><a class="metric orange" href="/equipment-bookings"><span>Equipment requests</span><strong><?= e((string)$equipmentPending) ?></strong><small><?= can_approve_equipment() ? 'Awaiting approval' : 'View requests' ?></small></a><a class="metric navy" href="/bookings"><span>Upcoming bookings</span><strong><?= e((string)$bookingCount) ?></strong><small>Next 30 days</small></a></section>
        <section class="two-col"><article class="card"><h2>Quick actions</h2><div class="stack"><a class="button primary" href="/tickets/new">Report an internal issue</a><a class="button secondary" href="/bookings/new">Request hut booking</a><?php if (is_admin()): ?><a class="button secondary" href="/equipment/new">Add equipment</a><?php endif; ?></div></article><article class="card"><h2>Permission summary</h2><p><?= e(implode(', ', array_column(current_user()['roles'], 'name')) ?: 'No roles assigned') ?></p><p class="muted"><?= can_manage_tickets() ? 'You can manage tickets.' : 'You can report and view your own tickets.' ?> <?= can_approve_equipment() ? 'You can approve equipment bookings.' : '' ?></p></article></section><?php page_end(); exit;
    }

    if ($path === '/tickets/new') {
        if ($method === 'POST') create_ticket_from_request(false);
        page_start('New ticket'); heading('Report an issue', 'Create a ticket for a hut or equipment problem.'); ticket_form(all('SELECT * FROM hut_areas ORDER BY name'),all('SELECT * FROM equipment WHERE current_status NOT IN ("Disposed","Lost") ORDER BY name')); page_end(); exit;
    }

    if ($path === '/tickets' && $method === 'GET') {
        $where=''; $params=[];
        if (!(is_admin() || user_has_role(['gsl','chairperson','qm']))) { $where='WHERE (t.reporter_user_id=? OR EXISTS(SELECT 1 FROM ticket_assignees ta WHERE ta.ticket_id=t.id AND ta.user_id=?))'; $params=[current_user()['id'],current_user()['id']]; }
        $tickets = all("SELECT t.*,ha.name area_name,e.name equipment_name,COALESCE(GROUP_CONCAT(DISTINCT u.name SEPARATOR ', '),'') assignees, COALESCE(SUM(m.total_cost),0) total_cost FROM tickets t LEFT JOIN hut_areas ha ON ha.id=t.hut_area_id LEFT JOIN equipment e ON e.id=t.equipment_id LEFT JOIN ticket_assignees ta ON ta.ticket_id=t.id LEFT JOIN users u ON u.id=ta.user_id LEFT JOIN maintenance_records m ON m.ticket_id=t.id {$where} GROUP BY t.id ORDER BY FIELD(t.status,'New','Awaiting review','Assigned','In progress','Waiting for parts','Waiting for contractor','Waiting for approval','Resolved','Closed','Cancelled'), FIELD(t.priority,'Emergency','Urgent','High','Normal','Low'),t.updated_at DESC",$params);
        page_start('Tickets'); heading('Tickets', 'Reported faults, repairs, updates and costs.', 'Report an issue','/tickets/new'); render_ticket_table($tickets); page_end(); exit;
    }

    if (preg_match('#^/tickets/(\d+)$#',$path,$m) && $method === 'GET') {
        $ticket=one('SELECT t.*,ha.name area_name,e.name equipment_name,e.asset_id FROM tickets t LEFT JOIN hut_areas ha ON ha.id=t.hut_area_id LEFT JOIN equipment e ON e.id=t.equipment_id WHERE t.id=?',[(int)$m[1]]); if(!$ticket || !can_view_ticket($ticket)){http_response_code(403);exit('You do not have permission to view this ticket.');}
        $updates=all('SELECT tu.*,u.name FROM ticket_updates tu LEFT JOIN users u ON u.id=tu.user_id WHERE tu.ticket_id=? ORDER BY tu.created_at ASC',[$ticket['id']]); $attachments=all('SELECT * FROM attachments WHERE ticket_id=? ORDER BY created_at DESC',[$ticket['id']]); $assignees=all('SELECT u.* FROM users u JOIN ticket_assignees ta ON ta.user_id=u.id WHERE ta.ticket_id=? ORDER BY u.name',[$ticket['id']]); $maintenance=all('SELECT m.*,u.name recorded_by FROM maintenance_records m LEFT JOIN users u ON u.id=m.recorded_by_user_id WHERE m.ticket_id=? ORDER BY work_date DESC',[$ticket['id']]);
        page_start('Ticket '.$ticket['reference']); heading($ticket['reference'], $ticket['title']); ?>
        <section class="detail-grid card"><div><span class="label">Status</span><?=status_badge($ticket['status'])?></div><div><span class="label">Priority</span><?=status_badge($ticket['priority'])?></div><div><span class="label">Related to</span><strong><?=e($ticket['area_name'] ?: ($ticket['equipment_name'] ? $ticket['asset_id'].' — '.$ticket['equipment_name'] : $ticket['linked_type']))?></strong></div><div><span class="label">Reported by</span><strong><?=e($ticket['reporter_name'])?></strong><small><?=e($ticket['reporter_email'])?></small></div><div><span class="label">Assigned to</span><strong><?=e(implode(', ',array_column($assignees,'name')) ?: 'Unassigned')?></strong></div><div><span class="label">Cost recorded</span><strong>£<?=number_format(ticket_total_cost((int)$ticket['id']),2)?></strong></div><div class="full"><span class="label">Description</span><p><?=nl2br(e($ticket['description']))?></p></div></section>
        <?php if($attachments): ?><section class="card"><h2>Attachments</h2><div class="attachment-list"><?php foreach($attachments as $attachment): ?><a href="/attachment/<?=$attachment['id']?>">📎 <?=e($attachment['original_name'])?></a><?php endforeach; ?></div></section><?php endif; ?>
        <?php if(can_manage_tickets((int)$ticket['id'])): $users=all('SELECT * FROM users WHERE is_active=1 ORDER BY name'); ?><section class="two-col"><article class="card"><h2>Update ticket</h2><form method="post" action="/tickets/<?=$ticket['id']?>/update" enctype="multipart/form-data" class="form-grid"><?=csrf_field()?><div class="full"><label>Update <span>*</span></label><textarea name="body" required rows="4" placeholder="What has been done or what is needed next?"></textarea></div><div><label>Status</label><select name="status"><?php foreach(['New','Awaiting review','Assigned','In progress','Waiting for parts','Waiting for contractor','Waiting for approval','Resolved','Closed','Cancelled'] as $status): if(in_array($status,['Closed','Cancelled'],true)&&!can_close_tickets())continue; ?><option <?=$status===$ticket['status']?'selected':''?>><?=$status?></option><?php endforeach; ?></select></div><div><label>Due date</label><input type="date" name="due_date" value="<?=e($ticket['due_date'])?>"></div><?php if(is_admin()||user_has_role(['gsl','chairperson','qm'])): ?><div class="full checkbox"><input id="internal" type="checkbox" name="is_internal" value="1"><label for="internal">Internal-only update — not sent to the reporter</label></div><?php endif; ?><div class="full"><label>Photo or file</label><input type="file" name="attachment" accept="image/jpeg,image/png,image/webp,application/pdf,text/plain"></div><button class="button primary">Save update</button></form></article>
        <article class="card"><h2>Assign people</h2><?php if(can_close_tickets()): ?><form method="post" action="/tickets/<?=$ticket['id']?>/assign" class="form-grid"><?=csrf_field()?><div class="full"><label>Assigned users</label><select name="assignees[]" multiple size="7"><?php $assignedIds=array_map(fn($a)=>(int)$a['id'],$assignees);foreach($users as $u): ?><option value="<?=$u['id']?>" <?=in_array((int)$u['id'],$assignedIds,true)?'selected':''?>><?=e($u['name'].' — '.$u['email'])?></option><?php endforeach;?></select><small>Hold Ctrl/Cmd to select more than one person.</small></div><button class="button secondary">Save assignments</button></form><?php else: ?><p>Only Admins, GSL, Chairperson and QM can change assignments.</p><?php endif;?></article></section>
        <section class="card"><h2>Log maintenance or repair cost</h2><form method="post" action="/tickets/<?=$ticket['id']?>/maintenance" class="form-grid"><?=csrf_field()?><div><label>Date of work</label><input type="date" name="work_date" value="<?=date('Y-m-d')?>" required></div><div><label>Contractor / supplier</label><input name="contractor_name"></div><div class="full"><label>Work completed <span>*</span></label><textarea name="description" rows="3" required></textarea></div><?php foreach(['labour_cost'=>'Labour','parts_cost'=>'Parts','contractor_cost'=>'Contractor','delivery_cost'=>'Delivery','other_cost'=>'Other'] as $field=>$label): ?><div><label><?=$label?> cost (£)</label><input type="number" step="0.01" min="0" name="<?=$field?>" value="0"></div><?php endforeach;?><div><label>Next review due</label><input type="date" name="next_due_date"></div><div class="full checkbox"><input id="completed" type="checkbox" name="completed" value="1"><label for="completed">Work completed and safe to return to normal use</label></div><button class="button primary">Record cost</button></form></section><?php endif; ?>
        <section class="card"><h2>Maintenance history</h2><div class="table-wrap"><table><thead><tr><th>Date</th><th>Work</th><th>Contractor</th><th>Cost</th><th>By</th></tr></thead><tbody><?php foreach($maintenance as $record):?><tr><td><?=e(date('d M Y',strtotime($record['work_date'])))?></td><td><?=e($record['description'])?></td><td><?=e($record['contractor_name'] ?: '—')?></td><td>£<?=number_format((float)$record['total_cost'],2)?></td><td><?=e($record['recorded_by'] ?: '—')?></td></tr><?php endforeach;?><?php if(!$maintenance):?><tr><td colspan="5" class="muted">No costs or maintenance records yet.</td></tr><?php endif;?></tbody></table></div></section>
        <section class="card"><h2>Ticket timeline</h2><div class="timeline"><?php foreach($updates as $update):?><article class="<?=$update['is_internal']?'internal':''?>"><strong><?=e($update['name'] ?: 'System')?></strong><?php if($update['is_internal']):?><span class="badge badge-internal">Internal</span><?php endif;?><time><?=e(date('d M Y H:i',strtotime($update['created_at'])))?></time><p><?=nl2br(e($update['body']))?></p><?php if($update['new_status']):?><small>Status: <?=e($update['old_status'] ?: '—')?> → <?=e($update['new_status'])?></small><?php endif;?></article><?php endforeach;?></div></section><?php page_end();exit;
    }

    if (preg_match('#^/tickets/(\d+)/assign$#',$path,$m) && $method==='POST') {
        validate_csrf(); if(!can_close_tickets()){http_response_code(403);exit('Only Admins, GSL, Chairperson and QM can assign tickets.');} $ticketId=(int)$m[1]; q('DELETE FROM ticket_assignees WHERE ticket_id=?',[$ticketId]); foreach(array_unique(array_map('intval',$_POST['assignees']??[])) as $userId){q('INSERT IGNORE INTO ticket_assignees(ticket_id,user_id) VALUES(?,?)',[$ticketId,$userId]);} q("UPDATE tickets SET status=CASE WHEN status='New' THEN 'Assigned' ELSE status END WHERE id=?",[$ticketId]);audit('Ticket assignments changed','ticket',$ticketId);flash('success','Ticket assignments saved.');redirect('/tickets/'.$ticketId);
    }

    if (preg_match('#^/tickets/(\d+)/update$#',$path,$m) && $method==='POST') {
        validate_csrf(); $ticketId=(int)$m[1];$ticket=one('SELECT * FROM tickets WHERE id=?',[$ticketId]); if(!$ticket||!can_manage_tickets($ticketId)){http_response_code(403);exit('You cannot update this ticket.');} $body=trim((string)($_POST['body']??'')); if($body==='')throw new RuntimeException('Enter an update.');$newStatus=(string)($_POST['status']??$ticket['status']); if(in_array($newStatus,['Closed','Cancelled'],true)&&!can_close_tickets()){http_response_code(403);exit('Only Admins, GSL, Chairperson and QM can close or cancel tickets.');}$internal=(is_admin()||user_has_role(['gsl','chairperson','qm']))&&!empty($_POST['is_internal']);q('INSERT INTO ticket_updates(ticket_id,user_id,body,is_internal,old_status,new_status) VALUES(?,?,?,?,?,?)',[$ticketId,current_user()['id'],$body,$internal,$ticket['status'],$newStatus]);$updateId=(int)db()->lastInsertId();if(!empty($_FILES['attachment']))store_ticket_upload($_FILES['attachment'],$ticketId,$updateId);q('UPDATE tickets SET status=?,due_date=?,closed_at=? WHERE id=?',[$newStatus,trim((string)($_POST['due_date']??''))?:null,in_array($newStatus,['Closed','Cancelled'],true)?date('Y-m-d H:i:s'):null,$ticketId]);$fresh=one('SELECT * FROM tickets WHERE id=?',[$ticketId]);notify_ticket_update($fresh,$body,$internal,(int)current_user()['id']);audit('Ticket updated','ticket',$ticketId,['status'=>$newStatus]);flash('success','Ticket update saved.');redirect('/tickets/'.$ticketId);
    }

    if (preg_match('#^/tickets/(\d+)/maintenance$#',$path,$m) && $method==='POST') {
        validate_csrf();$ticketId=(int)$m[1];$ticket=one('SELECT * FROM tickets WHERE id=?',[$ticketId]);if(!$ticket||!can_manage_tickets($ticketId)){http_response_code(403);exit('You cannot record a cost for this ticket.');}$costs=[];foreach(['labour_cost','parts_cost','contractor_cost','delivery_cost','other_cost'] as $key){$costs[$key]=max(0,(float)($_POST[$key]??0));}$total=array_sum($costs);$description=trim((string)($_POST['description']??''));if($description==='')throw new RuntimeException('Describe the work completed.');q('INSERT INTO maintenance_records(ticket_id,hut_area_id,equipment_id,recorded_by_user_id,contractor_name,work_date,description,labour_cost,parts_cost,contractor_cost,delivery_cost,other_cost,total_cost,completed,next_due_date) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',[$ticketId,$ticket['hut_area_id'],$ticket['equipment_id'],current_user()['id'],trim((string)($_POST['contractor_name']??''))?:null,$_POST['work_date'],$description,$costs['labour_cost'],$costs['parts_cost'],$costs['contractor_cost'],$costs['delivery_cost'],$costs['other_cost'],$total,!empty($_POST['completed'])?1:0,trim((string)($_POST['next_due_date']??''))?:null]);q('INSERT INTO ticket_updates(ticket_id,user_id,body,is_internal) VALUES(?,?,?,1)',[$ticketId,current_user()['id'],'Maintenance record added: '.$description.' Total cost: £'.number_format($total,2)]);audit('Maintenance cost recorded','ticket',$ticketId,['total_cost'=>$total]);flash('success','Maintenance and cost record added.');redirect('/tickets/'.$ticketId);
    }

    if (preg_match('#^/attachment/(\d+)$#',$path,$m) && $method==='GET') {
        $attachment=one('SELECT a.*,t.* FROM attachments a JOIN tickets t ON t.id=a.ticket_id WHERE a.id=?',[(int)$m[1]]);if(!$attachment||!can_view_ticket($attachment)){http_response_code(403);exit('You cannot access this attachment.');}$file=UPLOAD_PATH.'/'.$attachment['storage_name'];if(!is_file($file)){http_response_code(404);exit('File not found.');}header('Content-Type: '.$attachment['mime_type']);header('Content-Length: '.filesize($file));header('Content-Disposition: inline; filename="'.rawurlencode($attachment['original_name']).'"');readfile($file);exit;
    }

    if ($path === '/hut' && $method === 'GET') {
        $areas=all("SELECT h.*,COUNT(DISTINCT t.id) open_tickets,COALESCE(SUM(m.total_cost),0) maintenance_cost FROM hut_areas h LEFT JOIN tickets t ON t.hut_area_id=h.id AND t.status NOT IN ('Resolved','Closed','Cancelled') LEFT JOIN maintenance_records m ON m.hut_area_id=h.id GROUP BY h.id ORDER BY h.name");page_start('Hut');heading('Hut','Rooms, fixed facilities, inspections and building maintenance.',is_admin()?'Add hut area':null,is_admin()?'/hut/new':null);?><div class="card-grid"><?php foreach($areas as $area):?><a class="area-card" href="/hut/<?=$area['id']?>"><h2><?=e($area['name'])?></h2><p><?=e($area['area_type'])?></p><?=status_badge($area['current_condition'])?><dl><div><dt>Open tickets</dt><dd><?=e((string)$area['open_tickets'])?></dd></div><div><dt>Maintenance spend</dt><dd>£<?=number_format((float)$area['maintenance_cost'],2)?></dd></div><div><dt>Next inspection</dt><dd><?=e($area['next_inspection_date']?date('d M Y',strtotime($area['next_inspection_date'])):'Not set')?></dd></div></dl></a><?php endforeach;?><?php if(!$areas):?><article class="card"><p>No hut areas have been added yet.</p></article><?php endif;?></div><?php page_end();exit;
    }

    if ($path === '/hut/new') {
        require_admin();if($method==='POST'){validate_csrf();$name=trim((string)$_POST['name']);if($name==='')throw new RuntimeException('Enter a hut area name.');q('INSERT INTO hut_areas(name,area_type,description,current_condition,booking_enabled,capacity,last_inspection_date,next_inspection_date) VALUES(?,?,?,?,?,?,?,?)',[$name,trim((string)$_POST['area_type'])?:'Area',trim((string)$_POST['description'])?:null,$_POST['current_condition']??'Good',!empty($_POST['booking_enabled'])?1:0,(int)$_POST['capacity']?:null,trim((string)$_POST['last_inspection_date'])?:null,trim((string)$_POST['next_inspection_date'])?:null]);audit('Hut area created','hut_area',(int)db()->lastInsertId());flash('success','Hut area added.');redirect('/hut');}page_start('Add hut area');heading('Add hut area');?><form method="post" class="card form-grid"><?=csrf_field()?><div><label>Name</label><input name="name" required></div><div><label>Area type</label><input name="area_type" placeholder="For example: Room, Outside area, Fixed facility"></div><div><label>Current condition</label><select name="current_condition"><?php foreach(['Excellent','Good','Fair','Needs attention','Unsafe'] as $x):?><option><?=$x?></option><?php endforeach;?></select></div><div><label>Capacity</label><input type="number" min="0" name="capacity"></div><div><label>Last inspection</label><input type="date" name="last_inspection_date"></div><div><label>Next inspection</label><input type="date" name="next_inspection_date"></div><div class="full checkbox"><input type="checkbox" id="booking_enabled" name="booking_enabled" value="1"><label for="booking_enabled">This area can be booked</label></div><div class="full"><label>Description</label><textarea name="description" rows="5"></textarea></div><button class="button primary">Add hut area</button></form><?php page_end();exit;
    }

    if (preg_match('#^/hut/(\d+)$#',$path,$m)&&$method==='GET') {
        $area=one('SELECT * FROM hut_areas WHERE id=?',[(int)$m[1]]);if(!$area){http_response_code(404);exit('Hut area not found.');}$tickets=all("SELECT * FROM tickets WHERE hut_area_id=? ORDER BY updated_at DESC",[$area['id']]);$maintenance=all('SELECT * FROM maintenance_records WHERE hut_area_id=? ORDER BY work_date DESC',[$area['id']]);$bookings=all("SELECT * FROM hut_bookings WHERE hut_area_id=? AND ends_at>=NOW() ORDER BY starts_at ASC LIMIT 10",[$area['id']]);page_start($area['name']);heading($area['name'],$area['area_type']);?><section class="detail-grid card"><div><span class="label">Condition</span><?=status_badge($area['current_condition'])?></div><div><span class="label">Booking availability</span><strong><?=$area['booking_enabled']?'Bookable':'Not bookable'?></strong></div><div><span class="label">Capacity</span><strong><?=e((string)($area['capacity']?:'—'))?></strong></div><div><span class="label">Next inspection</span><strong><?=e($area['next_inspection_date']?:'Not set')?></strong></div><div class="full"><span class="label">Description</span><p><?=nl2br(e($area['description']))?></p></div></section><section class="two-col"><article class="card"><h2>Open and recent tickets</h2><ul class="clean-list"><?php foreach($tickets as $ticket):?><li><a href="/tickets/<?=$ticket['id']?>"><?=e($ticket['reference'].' — '.$ticket['title'])?></a> <?=status_badge($ticket['status'])?></li><?php endforeach;?><?php if(!$tickets):?><li class="muted">No tickets linked to this area.</li><?php endif;?></ul></article><article class="card"><h2>Upcoming bookings</h2><ul class="clean-list"><?php foreach($bookings as $booking):?><li><strong><?=e($booking['title'])?></strong><small><?=e(date('d M Y H:i',strtotime($booking['starts_at'])))?></small></li><?php endforeach;?><?php if(!$bookings):?><li class="muted">No upcoming bookings.</li><?php endif;?></ul></article></section><section class="card"><h2>Maintenance history</h2><div class="table-wrap"><table><thead><tr><th>Date</th><th>Work</th><th>Cost</th></tr></thead><tbody><?php foreach($maintenance as $record):?><tr><td><?=e($record['work_date'])?></td><td><?=e($record['description'])?></td><td>£<?=number_format((float)$record['total_cost'],2)?></td></tr><?php endforeach;?><?php if(!$maintenance):?><tr><td colspan="3" class="muted">No maintenance history yet.</td></tr><?php endif;?></tbody></table></div></section><?php page_end();exit;
    }

    if ($path==='/equipment' && $method==='GET') {
        $items=all("SELECT e.*,COUNT(DISTINCT t.id) open_tickets,COALESCE(SUM(m.total_cost),0) maintenance_cost FROM equipment e LEFT JOIN tickets t ON t.equipment_id=e.id AND t.status NOT IN ('Resolved','Closed','Cancelled') LEFT JOIN maintenance_records m ON m.equipment_id=e.id GROUP BY e.id ORDER BY e.name");page_start('Equipment');heading('Equipment database','Track condition, purchasing, loans, bookings and repair history.',is_admin()?'Add equipment':null,is_admin()?'/equipment/new':null);?><div class="equipment-grid"><?php foreach($items as $item):?><a class="equipment-card" href="/equipment/<?=$item['id']?>"><div class="equipment-photo"><?php if($item['photo_path']&&is_file(UPLOAD_PATH.'/'.$item['photo_path'])):?><img src="/asset-photo/<?=e($item['photo_path'])?>" alt="<?=e($item['name'])?>"><?php else:?><span>Equipment</span><?php endif;?></div><div><p class="asset-id"><?=e($item['asset_id'])?></p><h2><?=e($item['name'])?></h2><?=status_badge($item['current_condition'])?><p><?=e($item['storage_location']?:'Location not set')?></p><small>Open tickets: <?=$item['open_tickets']?> · Repairs: £<?=number_format((float)$item['maintenance_cost'],2)?></small></div></a><?php endforeach;?><?php if(!$items):?><article class="card"><p>No equipment has been added yet.</p></article><?php endif;?></div><?php page_end();exit;
    }

    if ($path==='/equipment/new') {
        require_admin();if($method==='POST'){validate_csrf();$asset=trim((string)$_POST['asset_id']);$name=trim((string)$_POST['name']);if($asset===''||$name==='')throw new RuntimeException('Asset ID and name are required.');$photo=store_asset_photo($_FILES['photo']??[]);q('INSERT INTO equipment(asset_id,name,category,description,manufacturer,model,serial_number,quantity_owned,quantity_available,storage_location,current_status,current_condition,purchase_date,purchase_price,purchase_place,supplier_contact,estimated_value,warranty_expiry,photo_path,last_inspection_date,next_inspection_date) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',[$asset,$name,trim((string)$_POST['category'])?:'Other',trim((string)$_POST['description'])?:null,trim((string)$_POST['manufacturer'])?:null,trim((string)$_POST['model'])?:null,trim((string)$_POST['serial_number'])?:null,max(1,(int)$_POST['quantity_owned']),max(0,(int)$_POST['quantity_available']),trim((string)$_POST['storage_location'])?:null,$_POST['current_status']??'Available',$_POST['current_condition']??'Good',trim((string)$_POST['purchase_date'])?:null,trim((string)$_POST['purchase_price'])?:null,trim((string)$_POST['purchase_place'])?:null,trim((string)$_POST['supplier_contact'])?:null,trim((string)$_POST['estimated_value'])?:null,trim((string)$_POST['warranty_expiry'])?:null,$photo,trim((string)$_POST['last_inspection_date'])?:null,trim((string)$_POST['next_inspection_date'])?:null]);$id=(int)db()->lastInsertId();audit('Equipment item created','equipment',$id,['asset_id'=>$asset]);flash('success','Equipment item added.');redirect('/equipment/'.$id);}page_start('Add equipment');heading('Add equipment','Create a detailed asset record, including photo and purchasing information.');?><form method="post" enctype="multipart/form-data" class="card form-grid"><?=csrf_field()?><div><label>Asset ID <span>*</span></label><input name="asset_id" required placeholder="For example: CAMP-TP-014"></div><div><label>Item name <span>*</span></label><input name="name" required></div><div><label>Category</label><input name="category" placeholder="For example: Camping equipment"></div><div><label>Storage location</label><input name="storage_location"></div><div><label>Quantity owned</label><input type="number" min="1" name="quantity_owned" value="1"></div><div><label>Quantity available</label><input type="number" min="0" name="quantity_available" value="1"></div><div><label>Current status</label><select name="current_status"><?php foreach(['Available','Reserved','Checked out','Under maintenance','Unsafe — do not use','Out of service','Lost','Disposed'] as $x):?><option><?=$x?></option><?php endforeach;?></select></div><div><label>Current condition</label><select name="current_condition"><?php foreach(['Excellent','Good','Fair','Needs attention','Damaged','Unsafe — do not use','Out of service','Lost','Disposed'] as $x):?><option><?=$x?></option><?php endforeach;?></select></div><div><label>Manufacturer</label><input name="manufacturer"></div><div><label>Model</label><input name="model"></div><div><label>Serial number</label><input name="serial_number"></div><div><label>Item photo</label><input type="file" name="photo" accept="image/jpeg,image/png,image/webp"></div><div><label>Purchase date</label><input type="date" name="purchase_date"></div><div><label>Purchase price (£)</label><input type="number" step="0.01" min="0" name="purchase_price"></div><div><label>Place purchased from</label><input name="purchase_place"></div><div><label>Supplier contact</label><input name="supplier_contact"></div><div><label>Estimated current value (£)</label><input type="number" step="0.01" min="0" name="estimated_value"></div><div><label>Warranty expiry</label><input type="date" name="warranty_expiry"></div><div><label>Last inspection</label><input type="date" name="last_inspection_date"></div><div><label>Next inspection</label><input type="date" name="next_inspection_date"></div><div class="full"><label>Description</label><textarea name="description" rows="5"></textarea></div><button class="button primary">Add equipment</button></form><?php page_end();exit;
    }

    if (preg_match('#^/asset-photo/([a-zA-Z0-9_.-]+)$#',$path,$m)&&$method==='GET') {
        $file=UPLOAD_PATH.'/'.$m[1];if(!is_file($file)){http_response_code(404);exit;} $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file);if(!str_starts_with($mime,'image/')){http_response_code(403);exit;}header('Content-Type: '.$mime);header('Cache-Control: private, max-age=86400');readfile($file);exit;
    }

    if (preg_match('#^/equipment/(\d+)$#',$path,$m)&&$method==='GET') {
        $item=one('SELECT * FROM equipment WHERE id=?',[(int)$m[1]]);if(!$item){http_response_code(404);exit('Equipment item not found.');}$tickets=all('SELECT * FROM tickets WHERE equipment_id=? ORDER BY updated_at DESC',[$item['id']]);$maintenance=all('SELECT * FROM maintenance_records WHERE equipment_id=? ORDER BY work_date DESC',[$item['id']]);$history=all('SELECT eb.*,ebi.quantity_requested,ebi.quantity_approved,ebi.quantity_issued,ebi.quantity_returned FROM equipment_booking_items ebi JOIN equipment_bookings eb ON eb.id=ebi.equipment_booking_id WHERE ebi.equipment_id=? ORDER BY eb.starts_at DESC',[$item['id']]);page_start($item['name']);heading($item['asset_id'].' — '.$item['name'],$item['category'],'Request equipment','/equipment/'.$item['id'].'/book');?><section class="two-col"><article class="card"><?php if($item['photo_path']&&is_file(UPLOAD_PATH.'/'.$item['photo_path'])):?><img class="detail-photo" src="/asset-photo/<?=e($item['photo_path'])?>" alt="<?=e($item['name'])?>"><?php endif;?><p><?=nl2br(e($item['description']))?></p><div class="detail-grid"><div><span class="label">Condition</span><?=status_badge($item['current_condition'])?></div><div><span class="label">Status</span><?=status_badge($item['current_status'])?></div><div><span class="label">Storage</span><strong><?=e($item['storage_location']?:'Not set')?></strong></div><div><span class="label">Quantity</span><strong><?=e($item['quantity_available'].' of '.$item['quantity_owned'])?></strong></div></div></article><article class="card"><h2>Purchase record</h2><dl class="vertical-dl"><div><dt>Purchase date</dt><dd><?=e($item['purchase_date']?:'Not recorded')?></dd></div><div><dt>Purchase price</dt><dd><?= $item['purchase_price']!==null?'£'.number_format((float)$item['purchase_price'],2):'Not recorded'?></dd></div><div><dt>Place purchased from</dt><dd><?=e($item['purchase_place']?:'Not recorded')?></dd></div><div><dt>Supplier</dt><dd><?=e($item['supplier_contact']?:'Not recorded')?></dd></div><div><dt>Lifetime repair cost</dt><dd>£<?=number_format((float)(one('SELECT COALESCE(SUM(total_cost),0) total FROM maintenance_records WHERE equipment_id=?',[$item['id']])['total']??0),2)?></dd></div></dl></article></section>
        <section class="card"><h2>Request this equipment</h2><form method="post" action="/equipment/<?=$item['id']?>/book" class="form-grid"><?=csrf_field()?><div><label>Booking title <span>*</span></label><input name="title" required placeholder="For example: Scout camp"></div><div><label>Quantity needed</label><input type="number" name="quantity" min="1" max="<?=$item['quantity_available']?>" value="1" required></div><div><label>From <span>*</span></label><input type="datetime-local" name="starts_at" required></div><div><label>Until <span>*</span></label><input type="datetime-local" name="ends_at" required></div><button class="button primary">Request equipment</button></form><p class="muted">Only GSL, Chairperson, QM or an Admin can approve equipment bookings.</p></section>
        <section class="two-col"><article class="card"><h2>Ticket history</h2><ul class="clean-list"><?php foreach($tickets as $ticket):?><li><a href="/tickets/<?=$ticket['id']?>"><?=e($ticket['reference'].' — '.$ticket['title'])?></a> <?=status_badge($ticket['status'])?></li><?php endforeach;?><?php if(!$tickets):?><li class="muted">No tickets linked to this item.</li><?php endif;?></ul></article><article class="card"><h2>Booking and loan history</h2><ul class="clean-list"><?php foreach($history as $booking):?><li><strong><?=e($booking['reference'].' — '.$booking['title'])?></strong><small><?=e(date('d M Y',strtotime($booking['starts_at'])).' · '.$booking['status'])?></small></li><?php endforeach;?><?php if(!$history):?><li class="muted">No bookings yet.</li><?php endif;?></ul></article></section><section class="card"><h2>Maintenance and repair history</h2><div class="table-wrap"><table><thead><tr><th>Date</th><th>Work</th><th>Cost</th><th>Completed</th></tr></thead><tbody><?php foreach($maintenance as $record):?><tr><td><?=e($record['work_date'])?></td><td><?=e($record['description'])?></td><td>£<?=number_format((float)$record['total_cost'],2)?></td><td><?=$record['completed']?'Yes':'No'?></td></tr><?php endforeach;?><?php if(!$maintenance):?><tr><td colspan="4" class="muted">No repairs recorded yet.</td></tr><?php endif;?></tbody></table></div></section><?php page_end();exit;
    }

    if (preg_match('#^/equipment/(\d+)/book$#',$path,$m)&&$method==='POST') {
        validate_csrf();$item=one('SELECT * FROM equipment WHERE id=?',[(int)$m[1]]);if(!$item){http_response_code(404);exit('Equipment item not found.');}$quantity=max(1,(int)($_POST['quantity']??1));if($quantity>$item['quantity_available'])throw new RuntimeException('The requested quantity is not currently available.');$starts=(string)($_POST['starts_at']??'');$ends=(string)($_POST['ends_at']??'');if(!$starts||!$ends||strtotime($starts)>=strtotime($ends))throw new RuntimeException('Enter a valid collection and return time.');$reference=unique_reference('EQP','equipment_bookings');$user=current_user();q('INSERT INTO equipment_bookings(reference,requester_user_id,requester_name,requester_email,title,starts_at,ends_at,status) VALUES(?,?,?,?,?,?,?,?)',[$reference,$user['id'],$user['name'],$user['email'],trim((string)$_POST['title']),date('Y-m-d H:i:s',strtotime($starts)),date('Y-m-d H:i:s',strtotime($ends)),'Requested']);$bookingId=(int)db()->lastInsertId();q('INSERT INTO equipment_booking_items(equipment_booking_id,equipment_id,quantity_requested) VALUES(?,?,?)',[$bookingId,$item['id'],$quantity]);audit('Equipment booking requested','equipment_booking',$bookingId,['item'=>$item['asset_id']]);foreach(core_notification_users() as $manager){send_email($manager['email'],'Equipment booking awaiting approval: '.$reference,email_html('Equipment booking requested','<p><strong>'.e($item['name']).'</strong> has been requested by '.e($user['name']).'.</p>','Review booking',app_url('/equipment-bookings')),'equipment_booking',$bookingId);}flash('success','Equipment booking '.$reference.' requested. GSL, Chairperson, QM or an Admin will review it.');redirect('/equipment/'.$item['id']);
    }

    if ($path==='/equipment-bookings' && $method==='GET') {
        $filter=can_approve_equipment()?'':'WHERE eb.requester_user_id=?';$params=can_approve_equipment()?[]:[current_user()['id']];$bookings=all("SELECT eb.*,GROUP_CONCAT(CONCAT(e.asset_id,' — ',e.name,' ×',ebi.quantity_requested) SEPARATOR '; ') items FROM equipment_bookings eb JOIN equipment_booking_items ebi ON ebi.equipment_booking_id=eb.id JOIN equipment e ON e.id=ebi.equipment_id {$filter} GROUP BY eb.id ORDER BY eb.created_at DESC",$params);page_start('Equipment bookings');heading('Equipment bookings','Requests, approvals, issue and return records.');?><div class="table-wrap"><table><thead><tr><th>Reference</th><th>Requester</th><th>Items</th><th>Dates</th><th>Status</th><th>Action</th></tr></thead><tbody><?php foreach($bookings as $booking):?><tr><td><?=e($booking['reference'])?></td><td><?=e($booking['requester_name'])?></td><td><?=e($booking['items'])?></td><td><?=e(date('d M Y H:i',strtotime($booking['starts_at'])))?><br><?=e(date('d M Y H:i',strtotime($booking['ends_at'])))?></td><td><?=status_badge($booking['status'])?></td><td><?php if(can_approve_equipment() && in_array($booking['status'],['Requested','Awaiting approval'],true)):?><form method="post" action="/equipment-bookings/<?=$booking['id']?>/approve" class="inline"><?=csrf_field()?><input type="hidden" name="decision" value="Approved"><button class="button small primary">Approve</button></form><form method="post" action="/equipment-bookings/<?=$booking['id']?>/approve" class="inline"><?=csrf_field()?><input type="hidden" name="decision" value="Declined"><button class="button small danger">Decline</button></form><?php else:?>—<?php endif;?></td></tr><?php endforeach;?><?php if(!$bookings):?><tr><td colspan="6" class="muted">No equipment booking requests found.</td></tr><?php endif;?></tbody></table></div><?php page_end();exit;
    }

    if (preg_match('#^/equipment-bookings/(\d+)/approve$#',$path,$m)&&$method==='POST') {
        validate_csrf();if(!can_approve_equipment()){http_response_code(403);exit('Only GSL, Chairperson, QM and Admins can approve equipment bookings.');}$booking=one('SELECT * FROM equipment_bookings WHERE id=?',[(int)$m[1]]);if(!$booking){http_response_code(404);exit('Booking not found.');}$decision=($_POST['decision']??'')==='Declined'?'Declined':'Approved';q('UPDATE equipment_bookings SET status=?,approved_by_user_id=?,approved_at=NOW() WHERE id=?',[$decision,current_user()['id'],$booking['id']]);audit('Equipment booking '.$decision,'equipment_booking',$booking['id']);send_email($booking['requester_email'],'Equipment booking '.$decision.': '.$booking['reference'],email_html('Equipment booking '.$decision,'<p>Your request <strong>'.e($booking['reference']).'</strong> has been '.strtolower($decision).'.</p>','View bookings',app_url('/equipment-bookings')),'equipment_booking',$booking['id']);flash('success','Equipment booking '.$decision.'.');redirect('/equipment-bookings');
    }

    if ($path==='/bookings' && $method==='GET') {
        $where=is_admin()?'':'WHERE b.requester_user_id=?';$params=is_admin()?[]:[current_user()['id']];$bookings=all("SELECT b.*,h.name area_name FROM hut_bookings b LEFT JOIN hut_areas h ON h.id=b.hut_area_id {$where} ORDER BY b.starts_at DESC",$params);page_start('Hut bookings');heading('Hut bookings','Requests and confirmed use of hut spaces.','Request booking','/bookings/new');?><div class="table-wrap"><table><thead><tr><th>Reference</th><th>Booking</th><th>Area</th><th>Dates</th><th>Status</th></tr></thead><tbody><?php foreach($bookings as $booking):?><tr><td><?=e($booking['reference'])?></td><td><strong><?=e($booking['title'])?></strong><small><?=e($booking['requester_name'])?></small></td><td><?=e($booking['area_name']?:'Whole hut / not set')?></td><td><?=e(date('d M Y H:i',strtotime($booking['starts_at'])))?><br><?=e(date('d M Y H:i',strtotime($booking['ends_at'])))?></td><td><?=status_badge($booking['status'])?></td></tr><?php endforeach;?><?php if(!$bookings):?><tr><td colspan="5" class="muted">No hut booking requests found.</td></tr><?php endif;?></tbody></table></div><?php page_end();exit;
    }

    if ($path==='/bookings/new') {
        if($method==='POST'){validate_csrf();$user=current_user();$starts=(string)$_POST['starts_at'];$ends=(string)$_POST['ends_at'];if(!trim((string)$_POST['title'])||strtotime($starts)>=strtotime($ends))throw new RuntimeException('Enter a title and valid booking times.');$reference=unique_reference('BKG','hut_bookings');q('INSERT INTO hut_bookings(reference,requester_user_id,requester_name,requester_email,organisation_name,title,hut_area_id,booking_type,attendee_count,starts_at,ends_at,status,notes) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)',[$reference,$user['id'],$user['name'],$user['email'],trim((string)$_POST['organisation_name'])?:null,trim((string)$_POST['title']),(int)$_POST['hut_area_id']?:null,trim((string)$_POST['booking_type'])?:'Hut booking',(int)$_POST['attendee_count']?:null,date('Y-m-d H:i:s',strtotime($starts)),date('Y-m-d H:i:s',strtotime($ends)),'Requested',trim((string)$_POST['notes'])?:null]);$id=(int)db()->lastInsertId();audit('Hut booking requested','hut_booking',$id);foreach(core_notification_users() as $manager){send_email($manager['email'],'New hut booking request: '.$reference,email_html('New hut booking request','<p>'.e($_POST['title']).' has been requested by '.e($user['name']).'.</p>','Open bookings',app_url('/bookings')),'hut_booking',$id);}flash('success','Booking request '.$reference.' submitted.');redirect('/bookings');}page_start('Request hut booking');heading('Request a hut booking');?><form method="post" class="card form-grid"><?=csrf_field()?><div><label>Booking title <span>*</span></label><input name="title" required></div><div><label>Organisation / section</label><input name="organisation_name"></div><div><label>Hut area</label><select name="hut_area_id"><option value="">Whole hut / not sure</option><?php foreach(all('SELECT * FROM hut_areas WHERE booking_enabled=1 ORDER BY name') as $area):?><option value="<?=$area['id']?>"><?=e($area['name'])?></option><?php endforeach;?></select></div><div><label>Booking type</label><input name="booking_type" value="Hut booking"></div><div><label>Number attending</label><input type="number" min="1" name="attendee_count"></div><div><label>From <span>*</span></label><input type="datetime-local" name="starts_at" required></div><div><label>Until <span>*</span></label><input type="datetime-local" name="ends_at" required></div><div class="full"><label>Notes</label><textarea name="notes" rows="4"></textarea></div><button class="button primary">Submit booking request</button></form><?php page_end();exit;
    }

    if ($path==='/users') {
        require_admin();if($method==='POST'){validate_csrf();$name=trim((string)$_POST['name']);$email=strtolower(trim((string)$_POST['email']));$password=(string)$_POST['password'];$roles=array_unique(array_map('intval',$_POST['roles']??[]));if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<12)throw new RuntimeException('Enter a name, email and password of at least 12 characters.');q('INSERT INTO users(name,email,password_hash,is_active) VALUES(?,?,?,1)',[$name,$email,password_hash($password,PASSWORD_DEFAULT)]);$userId=(int)db()->lastInsertId();foreach($roles as $roleId)q('INSERT IGNORE INTO user_roles(user_id,role_id) VALUES(?,?)',[$userId,$roleId]);audit('User created','user',$userId);flash('success','User account created.');redirect('/users');}$users=all("SELECT u.*,GROUP_CONCAT(r.name SEPARATOR ', ') roles FROM users u LEFT JOIN user_roles ur ON ur.user_id=u.id LEFT JOIN roles r ON r.id=ur.role_id GROUP BY u.id ORDER BY u.name");$roles=all('SELECT * FROM roles ORDER BY name');page_start('Users');heading('Users','Manage who can access the system and what they can do.');?><section class="two-col"><article class="card"><h2>Create user</h2><form method="post" class="form-grid"><?=csrf_field()?><div><label>Name</label><input name="name" required></div><div><label>Email</label><input name="email" type="email" required></div><div><label>Temporary password</label><input name="password" type="password" minlength="12" required></div><div class="full"><label>Roles</label><select name="roles[]" multiple size="6"><?php foreach($roles as $role):?><option value="<?=$role['id']?>"><?=e($role['name'])?></option><?php endforeach;?></select></div><button class="button primary">Create user</button></form></article><article class="card"><h2>Role safety</h2><p>Admins can do everything. Only Admin, GSL, Chairperson and QM can approve equipment bookings or manage all tickets.</p></article></section><section class="card"><h2>Existing users</h2><div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Roles</th><th>Last login</th></tr></thead><tbody><?php foreach($users as $u):?><tr><td><a href="/users/<?=$u['id']?>"><?=e($u['name'])?></a></td><td><?=e($u['email'])?></td><td><?=e($u['roles']?:'No roles')?></td><td><?=e($u['last_login_at']?:'Never')?></td></tr><?php endforeach;?></tbody></table></div></section><?php page_end();exit;
    }

    if (preg_match('#^/users/(\d+)$#',$path,$m)) {
        require_admin();
        $managed=one('SELECT * FROM users WHERE id=?',[(int)$m[1]]);
        if(!$managed){http_response_code(404);exit('User not found.');}
        $roles=all('SELECT * FROM roles ORDER BY name');
        $assignedRoleIds=array_map(fn($r)=>(int)$r['role_id'],all('SELECT role_id FROM user_roles WHERE user_id=?',[$managed['id']]));
        if($method==='POST'){
            validate_csrf();
            $newName=trim((string)$_POST['name']);
            $newEmail=strtolower(trim((string)$_POST['email']));
            $active=!empty($_POST['is_active'])?1:0;
            $chosen=array_unique(array_map('intval',$_POST['roles']??[]));
            $adminRole=one("SELECT id FROM roles WHERE slug='admin'");
            $wasAdmin=in_array((int)$adminRole['id'],$assignedRoleIds,true);
            $willAdmin=in_array((int)$adminRole['id'],$chosen,true);
            if($wasAdmin && (!$willAdmin || !$active)){
                $adminCount=(int)(one("SELECT COUNT(DISTINCT ur.user_id) total FROM user_roles ur JOIN users u ON u.id=ur.user_id JOIN roles r ON r.id=ur.role_id WHERE r.slug='admin' AND u.is_active=1")['total']??0);
                if($adminCount<=1)throw new RuntimeException('You cannot remove or disable the final active Admin account.');
            }
            if($newName===''||!filter_var($newEmail,FILTER_VALIDATE_EMAIL))throw new RuntimeException('Enter a valid name and email address.');
            q('UPDATE users SET name=?,email=?,is_active=? WHERE id=?',[$newName,$newEmail,$active,$managed['id']]);
            q('DELETE FROM user_roles WHERE user_id=?',[$managed['id']]);
            foreach($chosen as $roleId)q('INSERT IGNORE INTO user_roles(user_id,role_id) VALUES(?,?)',[$managed['id'],$roleId]);
            if(trim((string)($_POST['new_password']??''))!==''){
                if(strlen((string)$_POST['new_password'])<12)throw new RuntimeException('Replacement password must be at least 12 characters.');
                q('UPDATE users SET password_hash=? WHERE id=?',[password_hash((string)$_POST['new_password'],PASSWORD_DEFAULT),$managed['id']]);
            }
            audit('User updated','user',$managed['id']);
            flash('success','User account updated.');redirect('/users/'.$managed['id']);
        }
        page_start('Manage user');heading('Manage user', $managed['email']);?>
        <form method="post" class="card form-grid"><?=csrf_field()?>
          <div><label>Name</label><input name="name" value="<?=e($managed['name'])?>" required></div>
          <div><label>Email</label><input type="email" name="email" value="<?=e($managed['email'])?>" required></div>
          <div><label>Set new password</label><input type="password" name="new_password" minlength="12" placeholder="Leave blank to keep current password"></div>
          <div class="checkbox"><input id="active" type="checkbox" name="is_active" value="1" <?=$managed['is_active']?'checked':''?>><label for="active">Account is active</label></div>
          <div class="full"><label>Roles</label><select name="roles[]" multiple size="7"><?php foreach($roles as $role):?><option value="<?=$role['id']?>" <?=in_array((int)$role['id'],$assignedRoleIds,true)?'selected':''?>><?=e($role['name'])?></option><?php endforeach;?></select><small>Admins can do everything. GSL, Chairperson and QM approve equipment bookings and manage all tickets.</small></div>
          <button class="button primary">Save user</button>
        </form><?php page_end();exit;
    }

    if ($path==='/roles') {
        require_admin();
        if($method==='POST'){
            validate_csrf();
            $action=$_POST['action']??'role';
            if($action==='group'){
                $name=trim((string)$_POST['group_name']);
                if($name==='')throw new RuntimeException('Enter a group name.');
                q('INSERT INTO groups(name,description) VALUES(?,?)',[$name,trim((string)$_POST['group_description'])?:null]);
                $groupId=(int)db()->lastInsertId();
                foreach(array_unique(array_map('intval',$_POST['group_users']??[])) as $userId)q('INSERT IGNORE INTO group_members(group_id,user_id) VALUES(?,?)',[$groupId,$userId]);
                audit('Group created','group',$groupId);flash('success','Group created.');redirect('/roles');
            }
            $name=trim((string)$_POST['name']);$slug=strtolower(trim(preg_replace('/[^a-z0-9]+/','_',$_POST['slug']?:$name),'_'));
            if($name===''||$slug==='')throw new RuntimeException('Enter a role name.');
            q('INSERT INTO roles(name,slug,description,permissions,is_system) VALUES(?,?,?,?,0)',[$name,$slug,trim((string)$_POST['description'])?:null,json_encode(['custom'])]);
            audit('Role created','role',(int)db()->lastInsertId());flash('success','Role created. Assign it from the Users page.');redirect('/roles');
        }
        $roles=all('SELECT * FROM roles ORDER BY is_system DESC,name');
        $groups=all('SELECT g.*,COUNT(gm.user_id) members FROM groups g LEFT JOIN group_members gm ON gm.group_id=g.id GROUP BY g.id ORDER BY g.name');
        $allUsers=all('SELECT * FROM users WHERE is_active=1 ORDER BY name');
        page_start('Groups & roles');heading('Groups & roles','Create local groups and view role permissions.');?>
        <section class="two-col"><article class="card"><h2>Create role</h2><form method="post" class="form-grid"><?=csrf_field()?><input type="hidden" name="action" value="role"><div><label>Role name</label><input name="name" required></div><div><label>Role key</label><input name="slug" placeholder="Optional — generated from name"></div><div class="full"><label>Description</label><textarea name="description" rows="3"></textarea></div><button class="button primary">Create role</button></form></article><article class="card"><h2>Create group</h2><form method="post" class="form-grid"><?=csrf_field()?><input type="hidden" name="action" value="group"><div><label>Group name</label><input name="group_name" required placeholder="For example: Hut Team"></div><div class="full"><label>Description</label><textarea name="group_description" rows="2"></textarea></div><div class="full"><label>Members</label><select name="group_users[]" multiple size="5"><?php foreach($allUsers as $user):?><option value="<?=$user['id']?>"><?=e($user['name'])?></option><?php endforeach;?></select></div><button class="button secondary">Create group</button></form><?php if($groups):?><hr><?php foreach($groups as $group):?><p><strong><?=e($group['name'])?></strong> <small><?=e((string)$group['members'])?> members</small></p><?php endforeach;?><?php endif;?></article></section><section class="card"><h2>Roles</h2><div class="table-wrap"><table><thead><tr><th>Role</th><th>Description</th><th>Permissions</th></tr></thead><tbody><?php foreach($roles as $role):?><tr><td><strong><?=e($role['name'])?></strong><?= $role['is_system']?' <small>System role</small>':''?></td><td><?=e($role['description'])?></td><td><?=e(implode(', ',json_decode($role['permissions']?:'[]',true)?:[]))?></td></tr><?php endforeach;?></tbody></table></div></section><?php page_end();exit;
    }

    if ($path==='/settings') {
        require_admin();if($method==='POST'){validate_csrf();foreach(['group_name','mail_from_address','mail_from_name','mail_reply_to','smtp_host','smtp_port','smtp_encryption','smtp_username'] as $key){save_setting($key,trim((string)($_POST[$key]??'')));}if(trim((string)($_POST['smtp_password']??''))!=='')save_setting('smtp_password',crypt_value(trim((string)$_POST['smtp_password'])));audit('System settings updated','settings',null);flash('success','System and email settings saved.');redirect('/settings');}page_start('System settings');heading('System settings','Admins can change system identity and outgoing email settings.');?><section class="two-col"><form method="post" class="card form-grid"><?=csrf_field()?><h2 class="full">System</h2><div class="full"><label>Group / system name</label><input name="group_name" value="<?=e(setting('group_name'))?>"></div><h2 class="full">Email sender</h2><div><label>From email address</label><input type="email" name="mail_from_address" value="<?=e(setting('mail_from_address'))?>"></div><div><label>From name</label><input name="mail_from_name" value="<?=e(setting('mail_from_name'))?>"></div><div class="full"><label>Reply-to address</label><input type="email" name="mail_reply_to" value="<?=e(setting('mail_reply_to'))?>"></div><h2 class="full">SMTP</h2><div><label>SMTP host</label><input name="smtp_host" value="<?=e(setting('smtp_host'))?>"></div><div><label>SMTP port</label><input type="number" name="smtp_port" value="<?=e(setting('smtp_port','587'))?>"></div><div><label>Encryption</label><select name="smtp_encryption"><?php foreach(['tls'=>'TLS / STARTTLS','ssl'=>'SSL / SMTPS','none'=>'None'] as $value=>$label):?><option value="<?=$value?>" <?=setting('smtp_encryption','tls')===$value?'selected':''?>><?=$label?></option><?php endforeach;?></select></div><div><label>SMTP username</label><input name="smtp_username" value="<?=e(setting('smtp_username'))?>"></div><div class="full"><label>SMTP password</label><input type="password" name="smtp_password" placeholder="Leave blank to keep the existing password"><small>The password is encrypted using APP_KEY and is never shown again.</small></div><button class="button primary">Save settings</button></form><article class="card"><h2>Email status</h2><p>Email sends use your configured SMTP server. Until it is set, the system records emails as skipped rather than silently pretending they sent.</p><p>Use a verified sender domain and a monitored reply address.</p><h2>Branding</h2><p>Add the approved Scouts Wales red Group logo to <code>public/assets/brand/group-logo-red.svg</code>. The installer deliberately does not recreate Scout artwork.</p></article></section><?php page_end();exit;
    }

    http_response_code(404);page_start('Page not found');heading('Page not found','The page you requested does not exist.');?><a class="button primary" href="/dashboard">Back to dashboard</a><?php page_end();
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    flash('error', $exception->getMessage());
    $fallback = logged_in() ? '/dashboard' : ($path === '/report-problem' ? '/report-problem' : '/');
    if (!headers_sent()) redirect($fallback);
    http_response_code(500); echo 'Something went wrong. Please try again.';
}
