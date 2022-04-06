<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminVerify extends Model
{
    use HasFactory;

    public $table = "admins_verify";
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    protected $fillable = [
        'admin_id',
        'token',
    ];
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
