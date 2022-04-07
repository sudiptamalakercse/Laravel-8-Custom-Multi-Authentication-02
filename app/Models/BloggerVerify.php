<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloggerVerify extends Model
{
    use HasFactory;

    protected $table = "blogger_verifies";

     /**
     * Write code on Method
     *
     * @return response()
     */
    protected $fillable = [
        'blogger_id',
        'token',
    ];
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function blogger()
    {
        return $this->belongsTo(Blogger::class);
    }
}
