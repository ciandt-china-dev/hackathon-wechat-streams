<?php

namespace App\Services\MsgHandles;

use App\Models\Image;

/**
*
*/
class ImageMsgHandle extends BaseMsgHandle
{

  public function exec()
  {
    $image = Image::create([
      'picUrl' => $this->xmlPick('PicUrl'),
      'wxUser' => $this->xmlPick('FromUserName'),
      'msgId' => $this->xmlPick('MsgId'),
      'mediaId' => $this->xmlPick('MediaId'),
      'storeOnLocal' => false,
    ]);

    return $this->renderResponseMsg("Your image id: $image->id. Your can tag the image with /ImgId #tag1 #tag2. Not using this format will be ignored.");
  }
}
