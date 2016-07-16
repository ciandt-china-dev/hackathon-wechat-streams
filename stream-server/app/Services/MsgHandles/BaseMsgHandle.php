<?php

namespace App\Services\MsgHandles;

/**
*
*/
class BaseMsgHandle implements MsgHandleInterface
{

  /**
   * toUser
     fromUser
     CreateTime
     Content
   */
  const MSG_TPL = '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>';

  protected $xmlPick;

  public function exec(\DOMDocument $xml)
  {
    return '';
  }

  public function setXml(\DOMDocument $xml)
  {
    $this->xmlPick = function($tag) use ($xml){
      $xml->getElementsByTagName($tag)[0]->nodeValue;
    };

    return $this;
  }

  protected function renderReposneMsg($content)
  {
    $config = [
      'fromUser' => $this->xmlPick('fromUser'),
      'toUser' => $this->xmlPick('toUser'),
      'createTime' => datetime(),
      'Content' => $content,
    ];
    return vsprintf(self::MSG_TPL, array_values($config));
  }
}
