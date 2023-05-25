<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/private/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/private/send_status.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/private/constants.php';

header('Content-Type: application/json; charset=utf-8');

$body = json_decode(file_get_contents("php://input"));

// ensure the directory exists
if (!file_exists(GAME_FOLDER_PATH)) {
  mkdir(GAME_FOLDER_PATH);
}

// if the data passed
if ($body) {

  $path = GAME_FOLDER_PATH . '/' . $body->name . '.json';
  if (file_exists($path)) {
    send_status('game is already exists');
    exit;
  }

  file_put_contents($path, json_encode($body->data));
  send_status('ok');

} else {
  send_status('no data');
}

?>