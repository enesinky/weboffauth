<?php

define("LOCATION_VERIFY", false); // verify user location is near the device

define("MAX_DISTANCE", 250.0);  // max distance from device (if location verify is true)

define("MILLIS_LIMIT", 5000); // if pincode is valid less than limit value, hold user and response with another pincode

define("ERR_OUT_OF_RANGE", "Device is out of range.");

define("ERR_NOT_FOUND", "Device not found.");


?>
