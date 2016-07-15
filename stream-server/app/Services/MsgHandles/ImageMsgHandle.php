<?php

namespace App\Services\MsgHandles;

use App\Models;

/**
*
*/
class ImageMsgHandle extends BaseMsgHandle
{

  function exec(\DOMDocument xml)
  {
    $image = Image::create([
      'picUrl' => $this->xmlPick('PicUrl'),
      'wxUser' => $this->xmlPick('FromUserName'),
      'msgId' => $this->xmlPick('MsgId'),
      'mediaId' => $this->xmlPick('MediaId'),
      'storeOnLocal' => false,
    ]);

    return $this->renderReposneMsg('Your image id: ' . $image->id);
  }
}
