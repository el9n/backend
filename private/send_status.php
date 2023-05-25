<?php

function send_status($status)
{
  print json_encode(array('status' => $status));
}

?>