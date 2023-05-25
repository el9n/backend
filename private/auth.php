<?php

/**
 * This script prevents an unauthorised access to private actions
 * The password is contained in the password.txt file
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/private/constants.php';

// default password
$password = DEFAULT_PASSWORD;

// get the password from the file
set_error_handler(fn() => null);
$password = file_get_contents(PASSWORD_PATH);
restore_error_handler();

// authentication
if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_PW'] !== $password) {
  header('WWW-Authenticate: Basic realm="My Realm"');
  header('HTTP/1.0 401 Unauthorized');
  print 'authentication require_onced';
  exit;
}

?>