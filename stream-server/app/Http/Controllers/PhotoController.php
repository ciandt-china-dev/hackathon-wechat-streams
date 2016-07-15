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

    public function index($tag)
    {
        if ($tag == 'all' || $tag == null)
        {
            $photos = Image::with('tags')
                ->take($this->numOfPhotosInAreaPageLoad)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        else
        {
            $photos = Image::with(['tags' => function($query) use ($tag) {
                $query->where('label', '=', $tag);
            }])->get();
        }

        return $photos;
    }
}