<?php

namespace App\Services\MsgHandles;

interface MsgHandleInterface {

  public function exec();

  public function setXml(\DOMDocument $xml);

}
