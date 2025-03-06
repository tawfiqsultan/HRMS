<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AttendanceController extends Controller
{

    public function getAllAttendance()
    {
        try {
            $attendance = Attendance::with('employee')->get();

            if ($attendance->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No attendance found.',
                    'data'    => null
                ], 404);
            }

            $attendance->transform(function ($attendance) {
                $attendance->employee_name = $attendance->employee ? $attendance->employee->FullName : null;

                unset($attendance->employee);
                return $attendance;
            });

            return response()->json([
                'success' => true,
                'message' => 'attendance retrieved successfully.',
                'data'    => $attendance
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'failed to get attendance.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function startWorkDay()
    {
        DB::beginTransaction();
        try {
            $today = Carbon::now()->format('Y-m-d');

            $employees = Employee::all();

            $attendance = [];
            foreach ($employees as $employee) {
                Attendance::create([
                    'EmployeeID' => $employee->EmployeeID,
                    'AttendanceDate' => $today
                ]);

                $attendance[] = [
                    'EmployeeID' => $employee->EmployeeID,
                    'EmployeeName' => $employee->FullName,
                    'AttendanceDate' => $today
                ];
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'started work day successfully.',
                'data'    => $attendance
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'failed to start work day.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function getAttendance(Request $request)
    {
        try {
            $attendance = Attendance::with('employee')->find($request->id);
            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'attendance not found.',
                    'data'    => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'attendance retrieved successfully.',
                'data'    => $attendance
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'failed to get attendance.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function updateAttendance(Request $request)
    {
        $attendance = Attendance::find($request->id);
        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'attendance not found.',
                'data'    => null
            ], 404);
        }

        DB::beginTransaction();
        try {
            $attendance->update([
                'CheckInTime' => $request->CheckInTime,
                'CheckOutTime' => $request->CheckOutTime
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'attendance updated successfully.',
                'data'    => $attendance
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'failed to update attendance.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
