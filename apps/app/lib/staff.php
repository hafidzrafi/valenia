<?php

declare(strict_types=1);

/**
 * Staff area helpers: one time flash messages and (below) the dashboard,
 * attendance, and visit completion queries.
 *
 * All queue and attendance dates use app_today() so the staff area follows the
 * Asia/Jakarta day boundary regardless of the container timezone.
 */

/** Queue one flash message for the next staff page render. */
function staff_flash(string $message): void
{
    $_SESSION['staff_flash'] = $message;
}

/** Read and clear the pending staff flash message. */
function take_staff_flash(): ?string
{
    $message = $_SESSION['staff_flash'] ?? null;
    unset($_SESSION['staff_flash']);

    return is_string($message) && '' !== $message ? $message : null;
}
