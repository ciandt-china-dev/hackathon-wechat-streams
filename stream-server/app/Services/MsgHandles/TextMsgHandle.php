<?php

namespace App\Services\MsgHandles;

use App\Models;

/**
*
*/
class TextMsgHandle extends BaseMsgHandle
{

  public function exec(\DOMDocument $xml)
  {
    $content = $this->xmlPick('content');
    $match = $this->match($content);

    if ($match('//', false)) {
      $imageId = reset($match('//'));
      $tags = $match('//');

      return $this->attchTags($imageId, $tags);

    } else {
      return 'Err: format error, should like "%123 #tag1 #tag2 #tag3"';
    }
  }

  private function attchTags($imageId, $tags)
  {
    if (!($image = Image::find($imageId)))
    {
      return "Err: image id '$imageId' not found";
    }

    $extistTags = Tag::whereIn('label', $tags)->pluck('label');

    $newTags = array_map(function($label) {
      return Tag::create(['label' => $label])->id;
    }, array_diff($tags, $extistTags));

    $image->tags->sync($newTags);

    $tags = $image->tags->pluck('label');
    $tags = implode(', ', $tags);

    return "image $imageId has tags: [$tags]";
  }

  private function match($subject)
  {
    return function($patten, $all = true) use ($subject) {
      $fun = $all ? 'preg_match_all' : 'preg_match';
      $matched = $fun($patten, $subject, $match);

      return $fun($patten, $subject, $match) ? $match[1] : false;
    };
  }
}
