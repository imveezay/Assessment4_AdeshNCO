<?php
/**
 * Shared server-side validation and small utility helpers.
 * Every function returns a plain-English error string, or '' when valid —
 * this mirrors the client-side validators in js/validate.js so users see
 * consistent messages whether JavaScript is enabled or not.
 */

function e(?string $value): string
{
    // Shorthand for escaping output in templates.
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function validate_name(string $v): string
{
    $v = trim($v);
    if ($v === '') return 'Enter your full name.';
    if (mb_strlen($v) < 2) return 'Name looks too short.';
    if (mb_strlen($v) > 100) return 'Name is too long.';
    return '';
}

function validate_email_address(string $v): string
{
    $v = trim($v);
    if ($v === '') return 'Enter your email address.';
    if (!filter_var($v, FILTER_VALIDATE_EMAIL)) return 'Enter a valid email address, e.g. name@example.com.';
    return '';
}

function validate_phone(string $v): string
{
    $v = trim($v);
    if ($v === '') return ''; // phone is optional in most forms
    if (!preg_match('/^[0-9 +()\-]{6,20}$/', $v)) return 'Enter a valid phone number.';
    return '';
}

function validate_password(string $v): string
{
    if (strlen($v) < 8) return 'Password must be at least 8 characters.';
    if (!preg_match('/[A-Z]/', $v)) return 'Password needs at least one uppercase letter.';
    if (!preg_match('/[0-9]/', $v)) return 'Password needs at least one number.';
    return '';
}

function validate_event_type(string $v): string
{
    $allowed = ['wedding', 'corporate', 'celebration', 'other'];
    if (!in_array($v, $allowed, true)) return 'Select the type of event.';
    return '';
}

function validate_event_date(string $v): string
{
    if ($v === '') return 'Select a preferred date.';
    $date = DateTime::createFromFormat('Y-m-d', $v);
    if (!$date) return 'Enter a valid date.';
    $today = new DateTime('today');
    if ($date < $today) return 'Choose a date in the future.';
    return '';
}

function validate_message(string $v): string
{
    $v = trim($v);
    if ($v === '') return "Tell us a little about the event you're planning.";
    if (mb_strlen($v) < 10) return 'A few more details would help (10+ characters).';
    if (mb_strlen($v) > 2000) return 'Message is too long (max 2000 characters).';
    return '';
}

function validate_rating($v): string
{
    if (!is_numeric($v) || (int)$v < 1 || (int)$v > 5) return 'Choose a rating between 1 and 5.';
    return '';
}

/** Format a decimal price for display, e.g. 3200.00 -> "$3,200". */
function format_price($value): string
{
    return '$' . number_format((float)$value, 0);
}

/** Human-friendly status label with a CSS-friendly modifier class. */
function status_badge(string $status): array
{
    $map = [
        'new'         => ['New', 'badge-new'],
        'in_progress' => ['In progress', 'badge-progress'],
        'confirmed'   => ['Confirmed', 'badge-confirmed'],
        'closed'      => ['Closed', 'badge-closed'],
        'pending'     => ['Pending review', 'badge-new'],
        'approved'    => ['Approved', 'badge-confirmed'],
        'rejected'    => ['Rejected', 'badge-closed'],
    ];
    return $map[$status] ?? [ucfirst($status), 'badge-new'];
}
