<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/private/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/private/send_status.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/private/constants.php';

header('Content-Type: application/json; charset=utf-8');

// ensure the directory exists
if (!file_exists(GAME_FOLDER_PATH)) {
  mkdir(GAME_FOLDER_PATH);
}

// if argument is passed
if (array_key_first($_GET)) {
  /**
   * info about the chosen game
   */

  $path = GAME_FOLDER_PATH . '/' . array_key_first($_GET) . '.json';
  if (!file_exists($path)) {
    send_status('game not found');
    exit;
  }

  print file_get_contents($path);

} else {
  /**
   * info about all games
   */

  // scan the directory
  if ($handle = opendir(GAME_FOLDER_PATH)) {
    $games = [];

    while (false !== ($entry = readdir($handle))) {
      // filter files
      if (is_file(GAME_FOLDER_PATH . "/$entry") && str_ends_with($entry, '.json')) {
        array_push($games, substr($entry, 0, -1 * strlen('.json')));
      }
    }
    closedir($handle);

    print json_encode($games);
  }

}

?>