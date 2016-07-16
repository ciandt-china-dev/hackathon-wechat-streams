<?php

namespace App\Services\MsgHandles;

interface MsgHandleInterface {

  public function exec(\DOMDocument $xml);

  public function setXml(\DOMDocument $xml);

}
