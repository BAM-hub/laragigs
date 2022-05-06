<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'title', 'company', 'location', 'tags',
    //     'description', 'website', 'email'
    // ];

    public function scopeFilter($query, array $filters) {
        if($filters['tag'] ?? false) {
          $query->where('tags', 'like', '%' . request('tag') . '%');
        }

        if($filters['search'] ?? false) {
            $query->where('title', 'like', '%' . request('search') . '%')
            ->orWhere('description', 'like', '%' . request('search') . '%')
            ->orWhere('tags', 'like', '%' . request('search') . '%');
        }

        if($filters['company'] ?? false) {
          $query->where('company_id', '=', request('company'));
        }
    }

    // relationship to user
    public function user() {
      return $this->belongsTo(User::class, 'user_id');
    }
    public function company() {
      return $this->belongsTo(Company::class, 'comapny_id');
    }
}