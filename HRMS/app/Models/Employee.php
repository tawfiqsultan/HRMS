<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employee';

    public $timestamps = false;

    protected $fillable = [
        'FullName',
        'PhoneNumber',
        'Salary',
        'StartDate',
        'DepartmentID',
    ];

    protected $guarded = [
        'EmployeeID',
    ];
}
