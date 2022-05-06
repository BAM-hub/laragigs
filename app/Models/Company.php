<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // relationship with user
    public function user() {
        return $this->hasMany(User::class, 'company_id');
    }
    public function listing() {
        return $this->hasMany(Listing::class, 'company_id');
    }
}