<?php

header('Content-Type: application/json');
$reqTimeStamp = microtime(true);
$json_params  = file_get_contents('php://input');
if($json_params) {
  // check if valid json request
  if(strlen($json_params) > 0 && isValidJSON($json_params)) {
      require_once 'config.php';
      require_once 'classes/class.device.php';
      http_response_code(200);
      $data = json_decode($json_params, true);
      $device = new Device($data['device']['id']);
      // check if requested device id exists
      if($device->deviceExists() == true) {
        $distance = $device->calculateDistance( $data['device']['location']['latitude'],
                                                $data['device']['location']['longitude'],
                                                $data['device']['location']['accuracy']
                                              );
        // check if request location is nearby
        if((LOCATION_VERIFY && $distance <= MAX_DISTANCE) || !LOCATION_VERIFY) {
          $array = $device->generatePin();
          $array['process_delay'] = round((microtime(true) - $reqTimeStamp)*1000 - $array['sleep_time'], 3);
          echo(json_encode($array));
        }
        else {
          $message = array("error" => ERR_OUT_OF_RANGE, "distance"=>$distance);
          echo(json_encode($message));
        }
      }
      else {
        $message = array("error" => ERR_NOT_FOUND);
        echo(json_encode($message));
      }
  }
  else {
  http_response_code(403);
  }
}
else {
  http_response_code(403);
}

function isValidJSON($str) {
    $obj = json_decode($str, true);
    if(isset($obj['device']) && isset($obj['device']['id']) &&
    isset($obj['device']['location']) && isset($obj['device']['location']['latitude'])
    && isset($obj['device']['location']['longitude']) && isset($obj['device']['location']['accuracy'])
    && is_numeric($obj['device']['id']) && is_numeric($obj['device']['location']['longitude'])
    && is_numeric($obj['device']['location']['latitude']) && is_numeric($obj['device']['location']['accuracy']) &&
    count($obj)==1 && count($obj['device'])==2 && count($obj['device']['location'])==3
    ) {
    $check = true;
    }
    else {
      $check = false;
    }
   return json_last_error() == JSON_ERROR_NONE && $check == true;
}

?>
