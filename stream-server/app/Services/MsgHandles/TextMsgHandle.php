<?php

namespace App\Services\MsgHandles;

use App\Models\Image;
use App\Models\Tag;

/**
 *
 */
class TextMsgHandle extends BaseMsgHandle
{
    public function exec()
    {
        $content = $this->xmlPick('Content');
        $match = $this->match($content);

        if ($imageId = $match('/\/(\d+)/', false)) {
            $tags = $match('/#(\w+)/');
            return $this->renderResponseMsg($this->attachTags($imageId, $tags));
        } else {
            // todo: a better type of logic to determine if user wants to tag but without using correct format
            // Ignoring the invalid tags to avoid annoying error message.
            //return $this->renderResponseMsg('Err: format error, should like "/123 #tag1 #tag2 #tag3"');
        }
    }

    private function attachTags($imageId, $tags)
    {
        if (!($image = Image::find($imageId))) {
            return "Err: image id '$imageId' not found";
        }

        $existTagsPlucked = Tag::whereIn('label', $tags)->pluck('label', 'id')->all();
        $existTags = array_values($existTagsPlucked);
        $existTagIds = array_keys($existTagsPlucked);

        $newTags = array_diff($tags, $existTags);
        $newTagIds = array_map(function ($label) {
            return Tag::create(['label' => $label])->id;
        }, $newTags);

        $image->tags()->sync(array_merge($newTagIds, $existTagIds));

        $tags = $image->tags->pluck('label')->all();
        $tags = implode(', ', $tags);

        return "Image ($imageId) has tags: [$tags]";
    }

    private function match($subject)
    {
        return function ($patten, $all = true) use ($subject) {
            $func = $all ? 'preg_match_all' : 'preg_match';

            $matched = [];
            if ($func($patten, $subject, $matched) !== false && count($matched) > 1) {
                return $matched[1];
            }

            return false;
        };
    }
}
