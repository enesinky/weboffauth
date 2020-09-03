<?php

class aesHandler {

  private $method;
  private $key;
  private $iv;

  function __construct() {
    $this->method = 'aes-256-cbc';
    $this->key = "thisAPPcanReallymakeADifference#";
    $this->iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
  }

  function encrypt($input) {
      $encrypted = base64_encode(openssl_encrypt($input, $this->method, $this->key, OPENSSL_RAW_DATA, $this->iv));
      return $encrypted;
  }

  function decrypt($encrypted) {
    $decrypted = openssl_decrypt(base64_decode($encrypted), $this->method, $this->key, OPENSSL_RAW_DATA, $this->iv);
    return $decrypted;
  }

}

 ?>
