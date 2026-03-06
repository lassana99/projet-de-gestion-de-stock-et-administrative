<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectContact extends Model
{
    use HasFactory;

    protected $fillable = ['prospect_id', 'name', 'position', 'phone', 'email'];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }
}