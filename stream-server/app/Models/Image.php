<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model{

  public function tags()
  {
    return $this->belongsToMany('App\Models\Tag');
  }
}
