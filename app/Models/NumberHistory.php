<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberHistory extends Model {

    use HasFactory;

    protected $fillable = ['number_id', 'staff_id', 'start_date', 'staff_name', 'end_date'];

    public function number() {
        return $this->belongsTo(Number::class);
    }

    public function staff() {
        return $this->belongsTo(Staff::class);
    }
}
