<?php
// This file stores all basic functions

/* -----------------------------
   Escape string safely
------------------------------ */
function mysql_prep($value) {
    global $connection;
    return mysqli_real_escape_string($connection, $value);
}

/* -----------------------------
   Redirect helper
------------------------------ */
function redirect_to($location = null) {
    if ($location !== null) {
        header("Location: {$location}");
        exit;
    }
}

/* -----------------------------
   Confirm query
------------------------------ */
function confirm_query($result_set) {
    global $connection;
    if (!$result_set) {
        die("Database query failed: " . mysqli_error($connection));
    }
}

/* -----------------------------
   Get all subjects
------------------------------ */
function get_all_subjects($public = true) {
    global $connection;

    $query = "SELECT * FROM subjects ";
    if ($public) {
        $query .= "WHERE visible = 1 ";
    }
    $query .= "ORDER BY position ASC";

    $subject_set = mysqli_query($connection, $query);
    confirm_query($subject_set);
    return $subject_set;
}

/* -----------------------------
   Get pages for subject
------------------------------ */
function get_pages_for_subject($subject_id, $public = true) {
    global $connection;

    $query = "SELECT * FROM pages WHERE subject_id = {$subject_id} ";
    if ($public) {
        $query .= "AND visible = 1 ";
    }
    $query .= "ORDER BY position ASC";

    $page_set = mysqli_query($connection, $query);
    confirm_query($page_set);
    return $page_set;
}

/* -----------------------------
   Get subject by ID
------------------------------ */
function get_subject_by_id($subject_id) {
    global $connection;

    $query = "SELECT * FROM subjects WHERE id = {$subject_id} LIMIT 1";
    $result = mysqli_query($connection, $query);
    confirm_query($result);

    return mysqli_fetch_assoc($result) ?: null;
}

/* -----------------------------
   Get page by ID
------------------------------ */
function get_page_by_id($page_id) {
    global $connection;

    $query = "SELECT * FROM pages WHERE id = {$page_id} LIMIT 1";
    $result = mysqli_query($connection, $query);
    confirm_query($result);

    return mysqli_fetch_assoc($result) ?: null;
}

/* -----------------------------
   Get default page
------------------------------ */
function get_default_page($subject_id) {
    global $connection;

    $query = "SELECT * FROM pages
              WHERE subject_id = {$subject_id}
              AND visible = 1
              ORDER BY position ASC
              LIMIT 1";

    $result = mysqli_query($connection, $query);
    confirm_query($result);

    return mysqli_fetch_assoc($result) ?: null;
}

/* -----------------------------
   Find selected subject/page  ✅ FIXED
------------------------------ */
function find_selected_page() {
    global $sel_subject, $sel_page;

    if (isset($_GET['page'])) {
        $sel_page = get_page_by_id((int)$_GET['page']);
        $sel_subject = $sel_page
            ? get_subject_by_id($sel_page['subject_id'])
            : null;

    } elseif (isset($_GET['subj'])) {
        $sel_subject = get_subject_by_id((int)$_GET['subj']);
        $sel_page = $sel_subject
            ? get_default_page($sel_subject['id'])
            : null;

    } else {
        $sel_subject = null;
        $sel_page = null;
    }
}

/* -----------------------------
   Admin navigation
------------------------------ */
function navigation($sel_subject, $sel_page, $public = false) {
    $output = "<ul class=\"subjects\">";
    $subject_set = get_all_subjects($public);

    while ($subject = mysqli_fetch_assoc($subject_set)) {
        $output .= "<li";
        if ($sel_subject && $subject['id'] == $sel_subject['id']) {
            $output .= " class=\"selected\"";
        }
        $output .= "><a href=\"edit_subject.php?subj={$subject['id']}\">"
                . htmlspecialchars($subject['menu_name']) . "</a></li>";

        $page_set = get_pages_for_subject($subject['id'], $public);
        $output .= "<ul class=\"pages\">";
        while ($page = mysqli_fetch_assoc($page_set)) {
            $output .= "<li";
            if ($sel_page && $page['id'] == $sel_page['id']) {
                $output .= " class=\"selected ml-4\"";
            } else {
                $output .= " class=\"ml-4\"";
            }
            $output .= "><a href=\"content.php?page={$page['id']}\">"
                    . htmlspecialchars($page['menu_name']) . "</a></li>";
        }
        $output .= "</ul>";
    }

    return $output . "</ul>";
}

/* -----------------------------
   Public navigation
------------------------------ */
function public_navigation($sel_subject, $sel_page, $public = true) {
    $output = "<ul class=\"subjects\">";
    $subject_set = get_all_subjects($public);

    while ($subject = mysqli_fetch_assoc($subject_set)) {
        $output .= "<li";
        if ($sel_subject && $subject['id'] == $sel_subject['id']) {
            $output .= " class=\"selected\"";
        }
        $output .= "><a href=\"index.php?subj={$subject['id']}\">"
                . htmlspecialchars($subject['menu_name']) . "</a>";

        if ($sel_subject && $subject['id'] == $sel_subject['id']) {
            $page_set = get_pages_for_subject($subject['id'], $public);
            $output .= "<ul class=\"pages\">";
            while ($page = mysqli_fetch_assoc($page_set)) {
                $output .= "<li";
                if ($sel_page && $page['id'] == $sel_page['id']) {
                    $output .= " class=\"selected\"";
                }
                $output .= "><a href=\"index.php?page={$page['id']}\">"
                        . htmlspecialchars($page['menu_name']) . "</a></li>";
            }
            $output .= "</ul>";
        }
        $output .= "</li>";
    }

    return $output . "</ul>";
}

/* -----------------------------
   Position Management Helpers
------------------------------ */

// Normalize all subject positions to be sequential starting from 1
function normalize_subject_positions() {
    global $connection;

    // Get all subjects ordered by current position, then by ID for consistency
    $query = "SELECT id FROM subjects ORDER BY position ASC, id ASC";
    $result = mysqli_query($connection, $query);
    confirm_query($result);

    $position = 1;
    while ($subject = mysqli_fetch_assoc($result)) {
        $update_query = "UPDATE subjects SET position = {$position} WHERE id = {$subject['id']}";
        mysqli_query($connection, $update_query);
        confirm_query(mysqli_query($connection, $update_query));
        $position++;
    }

    return true;
}

function update_subject_position_safely($subject_id, $new_position) {
    global $connection;

    // Get current subject
    $subject = get_subject_by_id($subject_id);
    if (!$subject) return false;

    $current_position = $subject['position'];

    // If position hasn't changed, do nothing
    if ($current_position == $new_position) {
        return true;
    }

    // Check if new position is already taken
    $query = "SELECT id FROM subjects WHERE position = {$new_position} AND id != {$subject_id}";
    $result = mysqli_query($connection, $query);
    confirm_query($result);

    if (mysqli_num_rows($result) > 0) {
        // Position is taken, shift other subjects
        if ($new_position > $current_position) {
            // Moving to higher position - shift subjects between current and new position up
            $query = "UPDATE subjects SET position = position - 1
                     WHERE position > {$current_position} AND position <= {$new_position} AND id != {$subject_id}";
        } else {
            // Moving to lower position - shift subjects between new and current position down
            $query = "UPDATE subjects SET position = position + 1
                     WHERE position >= {$new_position} AND position < {$current_position} AND id != {$subject_id}";
        }
        mysqli_query($connection, $query);
        confirm_query(mysqli_query($connection, $query));
    }

    // Update the subject to new position
    $query = "UPDATE subjects SET position = {$new_position} WHERE id = {$subject_id}";
    $result = mysqli_query($connection, $query);
    confirm_query($result);

    return mysqli_affected_rows($connection) > 0;
}
?>
