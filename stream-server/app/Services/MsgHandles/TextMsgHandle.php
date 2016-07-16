<?php

namespace App\Services\MsgHandles;

use App\Models;

/**
 *
 */
class TextMsgHandle extends BaseMsgHandle
{

    public function exec()
    {
        $content = $this->xmlPick('Content');
        $match = $this->match($content);

        if ($match('//', false)) {
            $imageId = reset($match('//'));
            $tags = $match('//');

            return $this->renderResponseMsg($this->attchTags($imageId, $tags));

        } else {
            return $this->renderResponseMsg('Err: format error, should like "%123 #tag1 #tag2 #tag3"');
        }
    }

    private function attchTags($imageId, $tags)
    {
        if (!($image = Image::find($imageId))) {
            return "Err: image id '$imageId' not found";
        }

        $extistTags = Tag::whereIn('label', $tags)->pluck('label');

        $newTags = array_map(function ($label) {
            return Tag::create(['label' => $label])->id;
        }, array_diff($tags, $extistTags));

        $image->tags->sync($newTags);

        $tags = $image->tags->pluck('label');
        $tags = implode(', ', $tags);

        return "image $imageId has tags: [$tags]";
    }

    private function match($subject)
    {
        return function ($patten, $all = true) use ($subject) {
            $matched = [];
            $func = $all ? 'preg_match_all' : 'preg_match';
            return $func($patten, $subject, $matched) && (count($matched) > 1) ? $matched[1] : false;
        };
    }
}
