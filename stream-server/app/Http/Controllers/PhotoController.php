<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Tag;

class PhotoController extends Controller
{

    protected $numOfPhotosInAreaPageLoad = 5;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index($label)
    {
        if ($label == 'all' || $label == null)
        {
            return Image::with('tags')
                ->take($this->numOfPhotosInAreaPageLoad)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        else
        {
            $tag = Tag::where('label','=', $label)->firstOrFail();

            return $tag->images()->get();

        }

        return [];
    }
}
