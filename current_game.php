<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/private/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/private/send_status.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/private/constants.php';

header('Content-Type: application/json; charset=utf-8');

// ensure the file exists
if (!file_exists(CURRENT_GAME_FILE_PATH)) {
  file_put_contents(CURRENT_GAME_FILE_PATH, null);
}

// if argument is passed
if (array_key_first($_GET)) {
  /**
   * set the current game
   */

  $path = GAME_FOLDER_PATH . '/' . array_key_first($_GET) . '.json';
  if (!file_exists($path)) {
    send_status('game not found');
    exit;
  }

  file_put_contents(CURRENT_GAME_FILE_PATH, json_encode(
    new CurrentGame(
      array_key_first($_GET),
      time()
    )
  )
  );
  send_status('ok');

} else {
  /**
   * info about the current game
   */

  print file_get_contents(CURRENT_GAME_FILE_PATH);

}

class CurrentGame
{
  public $name;
  public $start = -1;
  public $lastPause = -1;
  public $pauseTime = 0;
  public $isPaused = true;

  function __construct($name, $start = -1)
  {
    $this->name = $name;
    $this->start = $start;
  }
}

?>