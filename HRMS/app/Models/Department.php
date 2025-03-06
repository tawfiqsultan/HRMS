<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'department';
    protected $primaryKey = 'DepartmentID';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'DepartmentName',
        'DepartmentDesc',
        'ManagerID',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'DepartmentID', 'DepartmentID');
    }

    /**
     * العلاقة مع الموظف الذي يدير القسم (مدير القسم).
     * Department.manager_id -> Employee.employee_id
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'ManagerID');
    }
}
