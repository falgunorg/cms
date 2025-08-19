<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model {

    use HasFactory;

    protected $table = 'staffs';
    protected $fillable = [
        'name', 'company_id', 'department_id', 'designation_id',
        'status', 'number_id', 'balance_limit', 'staff_id'
    ];

    public function number() {
        return $this->belongsTo(Number::class, 'number_id');
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function designation() {
        return $this->belongsTo(Designation::class);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function numberHistories() {
        return $this->hasMany(NumberHistory::class);
    }
}
