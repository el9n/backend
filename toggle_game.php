<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/private/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/private/send_status.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/private/constants.php';

header('Content-Type: application/json; charset=utf-8');

// ensure the file exists
if (!file_exists(CURRENT_GAME_FILE_PATH)) {
  send_status('current_game.json doesn\'t exist');
  die;
}

$content = json_decode(file_get_contents(CURRENT_GAME_FILE_PATH));

// if argument is passed
if (array_key_first($_GET) && (array_key_first($_GET) === 'on' || array_key_first($_GET) === 'off')) {

  $content->isPaused = array_key_first($_GET) === 'on' ? false : true;

} else {

  $content->isPaused = !($content->isPaused);

}

file_put_contents(
  CURRENT_GAME_FILE_PATH,
  json_encode($content)
);
send_status('ok');

?>