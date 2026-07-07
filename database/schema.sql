CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(120) PRIMARY KEY,
    setting_value LONGTEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    permissions JSON NULL,
    is_system TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS groups (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_roles (
    user_id INT UNSIGNED NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, role_id),
    CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_roles_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS group_members (
    group_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (group_id, user_id),
    CONSTRAINT fk_group_members_group FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    CONSTRAINT fk_group_members_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hut_areas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    area_type VARCHAR(80) NOT NULL DEFAULT 'Area',
    description TEXT NULL,
    booking_enabled TINYINT(1) NOT NULL DEFAULT 0,
    capacity INT UNSIGNED NULL,
    photo_path VARCHAR(255) NULL,
    last_inspection_date DATE NULL,
    next_inspection_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS equipment (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL DEFAULT 'Other',
    description TEXT NULL,
    manufacturer VARCHAR(120) NULL,
    model VARCHAR(120) NULL,
    serial_number VARCHAR(120) NULL,
    quantity_owned INT UNSIGNED NOT NULL DEFAULT 1,
    quantity_available INT UNSIGNED NOT NULL DEFAULT 1,
    storage_location VARCHAR(150) NULL,
    current_status ENUM('Available','Reserved','Checked out','Under maintenance','Unsafe — do not use','Out of service','Lost','Disposed') NOT NULL DEFAULT 'Available',
    current_condition ENUM('Excellent','Good','Fair','Needs attention','Damaged','Unsafe — do not use','Out of service','Lost','Disposed') NOT NULL DEFAULT 'Good',
    purchase_date DATE NULL,
    purchase_price DECIMAL(10,2) NULL,
    purchase_place VARCHAR(180) NULL,
    supplier_contact VARCHAR(190) NULL,
    estimated_value DECIMAL(10,2) NULL,
    warranty_expiry DATE NULL,
    photo_path VARCHAR(255) NULL,
    last_inspection_date DATE NULL,
    next_inspection_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tickets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(40) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    source ENUM('Public','Scout user','External user','Admin') NOT NULL DEFAULT 'Public',
    reporter_name VARCHAR(150) NOT NULL,
    reporter_email VARCHAR(190) NOT NULL,
    reporter_user_id INT UNSIGNED NULL,
    category VARCHAR(100) NOT NULL,
    linked_type ENUM('Hut','Equipment','Other') NOT NULL DEFAULT 'Other',
    hut_area_id INT UNSIGNED NULL,
    equipment_id INT UNSIGNED NULL,
    location_text VARCHAR(255) NULL,
    priority ENUM('Low','Normal','High','Urgent','Emergency') NOT NULL DEFAULT 'Normal',
    status ENUM('New','Awaiting review','Assigned','In progress','Waiting for parts','Waiting for contractor','Waiting for approval','Resolved','Closed','Cancelled') NOT NULL DEFAULT 'New',
    description TEXT NOT NULL,
    public_token CHAR(64) NOT NULL UNIQUE,
    due_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    closed_at DATETIME NULL,
    CONSTRAINT fk_ticket_reporter FOREIGN KEY (reporter_user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_ticket_area FOREIGN KEY (hut_area_id) REFERENCES hut_areas(id) ON DELETE SET NULL,
    CONSTRAINT fk_ticket_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ticket_assignees (
    ticket_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ticket_id, user_id),
    CONSTRAINT fk_ticket_assignee_ticket FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    CONSTRAINT fk_ticket_assignee_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ticket_updates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NULL,
    body TEXT NOT NULL,
    is_internal TINYINT(1) NOT NULL DEFAULT 0,
    old_status VARCHAR(80) NULL,
    new_status VARCHAR(80) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ticket_update_ticket FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    CONSTRAINT fk_ticket_update_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS attachments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT UNSIGNED NULL,
    ticket_update_id INT UNSIGNED NULL,
    original_name VARCHAR(255) NOT NULL,
    storage_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(120) NOT NULL,
    size_bytes INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_attachment_ticket FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    CONSTRAINT fk_attachment_update FOREIGN KEY (ticket_update_id) REFERENCES ticket_updates(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS maintenance_records (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT UNSIGNED NULL,
    hut_area_id INT UNSIGNED NULL,
    equipment_id INT UNSIGNED NULL,
    recorded_by_user_id INT UNSIGNED NULL,
    contractor_name VARCHAR(180) NULL,
    work_date DATE NOT NULL,
    description TEXT NOT NULL,
    labour_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
    parts_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
    contractor_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
    delivery_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
    other_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
    receipt_path VARCHAR(255) NULL,
    completed TINYINT(1) NOT NULL DEFAULT 0,
    next_due_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_maintenance_ticket FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE SET NULL,
    CONSTRAINT fk_maintenance_area FOREIGN KEY (hut_area_id) REFERENCES hut_areas(id) ON DELETE SET NULL,
    CONSTRAINT fk_maintenance_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE SET NULL,
    CONSTRAINT fk_maintenance_user FOREIGN KEY (recorded_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hut_bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(40) NOT NULL UNIQUE,
    requester_user_id INT UNSIGNED NULL,
    requester_name VARCHAR(150) NOT NULL,
    requester_email VARCHAR(190) NOT NULL,
    organisation_name VARCHAR(180) NULL,
    title VARCHAR(180) NOT NULL,
    hut_area_id INT UNSIGNED NULL,
    whole_site TINYINT(1) NOT NULL DEFAULT 0,
    booking_type VARCHAR(100) NOT NULL DEFAULT 'Hut booking',
    attendee_count INT UNSIGNED NULL,
    starts_at DATETIME NOT NULL,
    ends_at DATETIME NOT NULL,
    status ENUM('Requested','Awaiting approval','Approved','Confirmed','Declined','Cancelled','Completed','No show') NOT NULL DEFAULT 'Requested',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_hut_booking_requester FOREIGN KEY (requester_user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_hut_booking_area FOREIGN KEY (hut_area_id) REFERENCES hut_areas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hut_booking_areas (
    hut_booking_id INT UNSIGNED NOT NULL,
    hut_area_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (hut_booking_id, hut_area_id),
    CONSTRAINT fk_hut_booking_areas_booking FOREIGN KEY (hut_booking_id) REFERENCES hut_bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_hut_booking_areas_area FOREIGN KEY (hut_area_id) REFERENCES hut_areas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS equipment_bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(40) NOT NULL UNIQUE,
    requester_user_id INT UNSIGNED NULL,
    requester_name VARCHAR(150) NOT NULL,
    requester_email VARCHAR(190) NOT NULL,
    title VARCHAR(180) NOT NULL,
    linked_hut_booking_id INT UNSIGNED NULL,
    starts_at DATETIME NOT NULL,
    ends_at DATETIME NOT NULL,
    status ENUM('Requested','Awaiting approval','Approved','Partially approved','Declined','Ready for collection','Checked out','Partially returned','Returned','Overdue','Cancelled') NOT NULL DEFAULT 'Requested',
    approval_note TEXT NULL,
    approved_by_user_id INT UNSIGNED NULL,
    approved_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipment_booking_requester FOREIGN KEY (requester_user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_equipment_booking_hut FOREIGN KEY (linked_hut_booking_id) REFERENCES hut_bookings(id) ON DELETE SET NULL,
    CONSTRAINT fk_equipment_booking_approver FOREIGN KEY (approved_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS equipment_booking_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipment_booking_id INT UNSIGNED NOT NULL,
    equipment_id INT UNSIGNED NOT NULL,
    quantity_requested INT UNSIGNED NOT NULL DEFAULT 1,
    quantity_approved INT UNSIGNED NULL,
    quantity_issued INT UNSIGNED NULL,
    quantity_returned INT UNSIGNED NULL,
    condition_out VARCHAR(100) NULL,
    condition_in VARCHAR(100) NULL,
    notes TEXT NULL,
    CONSTRAINT fk_booking_item_booking FOREIGN KEY (equipment_booking_id) REFERENCES equipment_bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_booking_item_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS email_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    related_type VARCHAR(50) NULL,
    related_id INT UNSIGNED NULL,
    recipient_email VARCHAR(190) NOT NULL,
    subject_line VARCHAR(255) NOT NULL,
    status ENUM('Queued','Sent','Failed','Skipped') NOT NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(190) NOT NULL,
    related_type VARCHAR(50) NULL,
    related_id INT UNSIGNED NULL,
    details JSON NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_tickets_status_priority ON tickets(status, priority);
CREATE INDEX idx_tickets_created ON tickets(created_at);
CREATE INDEX idx_equipment_condition ON equipment(current_condition, current_status);
CREATE INDEX idx_maintenance_links ON maintenance_records(ticket_id, hut_area_id, equipment_id);
CREATE INDEX idx_hut_bookings_dates ON hut_bookings(starts_at, ends_at);
CREATE INDEX idx_hut_booking_areas_area ON hut_booking_areas(hut_area_id);
CREATE INDEX idx_equipment_bookings_dates ON equipment_bookings(starts_at, ends_at);
