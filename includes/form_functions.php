<?php
function check_required_fields($required_array, $source) {
    $field_errors = array();

    foreach ($required_array as $fieldname) {
        if (!isset($source[$fieldname]) ||
            (empty($source[$fieldname]) && $source[$fieldname] != 0)) {
            $field_errors[] = $fieldname;
        }
    }

    return $field_errors;
}

function check_max_field_lengths($field_length_array, $source) {
    $field_errors = array();

    foreach ($field_length_array as $fieldname => $maxlength) {
        if (isset($source[$fieldname]) &&
            strlen(trim($source[$fieldname])) > $maxlength) {
            $field_errors[] = $fieldname;
        }
    }

    return $field_errors;
}

function display_errors($error_array) {
	echo "<p class=\"errors\">";
	echo "Please review the following fields:<br />";
	foreach($error_array as $error) {
		echo " - " . $error . "<br />";
	}
	echo "</p>";
}

?>