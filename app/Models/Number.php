<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Number extends Model {

    use HasFactory;

    protected $fillable = ['number', 'status'];

    // One number can be assigned to one staff
    public function staff() {
        return $this->hasOne(Staff::class, 'number_id', 'id');
    }

    public function histories() {
        return $this->hasMany(NumberHistory::class);
    }
}
