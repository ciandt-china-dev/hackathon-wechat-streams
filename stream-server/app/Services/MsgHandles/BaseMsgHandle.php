<?php

namespace App\Services\MsgHandles;

/**
 *
 */
class BaseMsgHandle implements MsgHandleInterface
{

    /**
     * toUser
     * fromUser
     * CreateTime
     * Content
     */
    const MSG_TPL = '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>';

    protected $xmlPick;

    protected $xml;

    public function exec()
    {
        return '';
    }

    public function setXml(\DOMDocument $xml)
    {

        $this->xml = $xml;
        return $this;

//        $this->xmlPick = function ($tag) use ($xml) {
//            $xml->getElementsByTagName($tag)->item(0)->nodeValue;
//        };
//
//        return $this;
    }

    protected function xmlPick($tag)
    {
        try {
            return $this->xml->getElementsByTagName($tag)->item(0)->nodeValue;
        }
        catch (\ErrorException $e)
        {
            return 'Err: tag not found.';
        }
    }

    protected function renderResponseMsg($content)
    {
        $config = [
            'FromUserName' => $this->xmlPick('FromUserName'),
            'ToUserName' => $this->xmlPick('ToUserName'),
            'CreateTime' => $this->xmlPick('CreateTime'),
            'Content' => $content,
        ];
        return vsprintf(self::MSG_TPL, array_values($config));
    }
}
