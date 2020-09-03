<?php

require_once 'class.aeshandler.php';

class Device
{

  private $hostname = "localhost";
  private $username = "root";
  private $password = "";
  private $db = "offauth";
  private $dbconnect;
  private $query;
  private $id;
  private $device;
  private $deviceKeyDecrypted;
  private $distance;
  public $reqTimeStamp;
  private $customTimeStamp;
  private $customDate;

  function __construct($id)
  {
    date_default_timezone_set("Europe/Istanbul");
    $this->id = $id;
    $this->reqTimeStamp = microtime(true);
    $this->customTimeStamp = strtotime(date("d-m-Y H:i:00"));
    $this->customDate = date("d-m-Y H:i:00");
    $this->dbconnect = new mysqli($this->hostname,$this->username,$this->password,$this->db);
    if ($this->dbconnect->connect_error) {
      die("Connection failed: " . $this->dbconnect->connect_error);
    }
  }

  function __destruct() {
    $this->query->free();
    $this->dbconnect->close();
  }

  function deviceExists() {
    $sql = "SELECT device_key, lat, lon FROM devices WHERE id=".$this->id.";";
    $this->query = $this->dbconnect->query($sql);
    if($this->query->num_rows == 1) {
      $this->device = $this->query->fetch_array(MYSQLI_ASSOC);
      // decrypt device_key
      $keyObj = new aesHandler();
      $this->deviceKeyDecrypted = $keyObj->decrypt($this->device['device_key']);
      return true;
    }
    else {
      return false;
    }
  }

  function calculateDistance($lat, $lon, $acu) {
    if($this->device) {
      $theta = $this->device['lon'] - $lon;
      $miles = (sin(deg2rad($this->device['lat'])) * sin(deg2rad($lat))) + (cos(deg2rad($this->device['lat'])) * cos(deg2rad($lat)) * cos(deg2rad($theta)));
      $miles = acos($miles);
      $miles = rad2deg($miles);
      $miles = $miles * 60 * 1.1515;
      $meters = $miles * 1.609344 * 1000;
      $this->distance = round($meters, 3);
      return $meters;
    }
    else {
      return null;
    }
  }

  function generatePin() {
    $remainingMillis = $this->calculateRemainingMillis();
    $sleep = 0.0;
    if($remainingMillis < MILLIS_LIMIT) {
      $sleep = $remainingMillis;
      usleep($sleep*1000);
      $this->customTimeStamp = strtotime("+1 minutes", $this->customTimeStamp);
      $this->customDate = date("d-m-Y H:i:00", $this->customTimeStamp);
    }
    $pin = preg_replace("/[^0-9]/", "", $this->generateHash());
    $pin = substr($pin, 0, 6);
    return array( "pin" => "$pin",
                  "remaining_millis" => $this->calculateRemainingMillis(),
                  "process_delay" => '',
                  "sleep_time" => $sleep
                );
  }

  function generateHash() {
    $input = $this->customDate . $this->id . $this->deviceKeyDecrypted;
    return hash('sha256', $input);
  }

  function calculateRemainingMillis() {
        $minuteFactor = (60 * 1000);
        $currentTimeStamp = microtime(true) * 1000;
        return round($this->customTimeStamp*1000 + $minuteFactor - $currentTimeStamp, 3);
  }

}

 ?>
