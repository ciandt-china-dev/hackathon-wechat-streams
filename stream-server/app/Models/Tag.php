<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

  protected $guarded = ['id'];
  
  public function images()
  {
    return $this->belongsToMany('App\Models\Image')->withTimestamps();;
  }
}
