<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\Import;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use Datatables;
use DB;
use App\User;
use App\LeaveApplications;

class ExcelController extends Controller
{
    public function index()
    {
        $users = User::sortable()
        ->join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->join('leave_types', 'leave_types.id', '=', 'leave_applications.leave_type_id')
        ->select('users.*', 'leave_applications.*', 'leave_types.name as leave_type_name')
        ->paginate(15);

        $count_approve = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->where('leave_applications.status','like','%APPROVED%')
        ->count();

        $count_pending = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->where('leave_applications.status','like','%PENDING_1%')
        ->orwhere('leave_applications.status','like','%PENDING_2%')
        ->orwhere('leave_applications.status','like','%PENDING_3%')
        ->count();

        $count_reject = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->where('leave_applications.status','like','%DENIED_1%')
        ->orwhere('leave_applications.status','like','%DENIED_2%')
        ->orwhere('leave_applications.status','like','%DENIED_3%')
        ->count();
        
        return view('excel/transfer')->with(compact('users', 'count_approve', 'count_pending', 'count_reject'));
    }

    public function search(Request $request)
    {
        $search_name = $request->get('name');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $leave_type = $request->get('leave_type');
        $leave_status = $request->get('leave_status');

        $count_approve = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->where('leave_applications.status','like','%APPROVED%')
        ->count();

        $count_pending = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->where('leave_applications.status','like','%PENDING_1%')
        ->orwhere('leave_applications.status','like','%PENDING_2%')
        ->orwhere('leave_applications.status','like','%PENDING_3%')
        ->count();

        $count_reject = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->where('leave_applications.status','like','%DENIED_1%')
        ->orwhere('leave_applications.status','like','%DENIED_2%')
        ->orwhere('leave_applications.status','like','%DENIED_3%')
        ->count();

        $query = User::sortable()
        ->join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->join('leave_types', 'leave_types.id', '=', 'leave_applications.leave_type_id')
        ->select('users.*', 'leave_applications.*', 'leave_types.name as leave_type_name');

        if($request->get('name') != '') {
            $query->where('users.name','like','%'.$search_name.'%');
        }

        if($request->get('date_from') != '' && $request->get('date_to') != '') {
            $query->wherebetween('leave_applications.date_from', [$date_from, $date_to]);
        }

        if($request->get('leave_type') != '') {
            $query->where('leave_applications.leave_type_id','like','%'.$leave_type.'%');
        }

        if($request->get('leave_status') != '') {
            if($request->get('leave_status') == 'PENDING') {
                $query->where('leave_applications.status','like','%PENDING_1%');
                $query->orwhere('leave_applications.status','like','%PENDING_2%');
                $query->orwhere('leave_applications.status','like','%PENDING_3%');
            }
            if($request->get('leave_status') == 'DENIED') {
                $query->where('leave_applications.status','like','%DENIED_1%');
                $query->orwhere('leave_applications.status','like','%DENIED_2%');
                $query->orwhere('leave_applications.status','like','%DENIED_3%');
            }
            else {
                $query->where('leave_applications.status','like','%'.$leave_status.'%');
            }
        }

        $users = $query->paginate(15);

        return view('excel/transfer')->with(compact('users', 'search_name', 'date_from', 'date_to', 'leave_type', 'leave_status', 'count_approve', 'count_pending', 'count_reject'));
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'import_file'  => 'required|mimes:xls,xlsx'
           ]);

        Excel::import(new Import(), request()->file('import_file'));

        return back()->with('success', 'Data imported successfully.');
    }

    public function export(Request $request)
    {
        $search_name = $request->get('excel_search_name');
        $date_from = $request->get('excel_date_from');
        $date_to = $request->get('excel_date_to');
        $leave_type = $request->get('excel_leave_type');
        $leave_status = $request->get('excel_leave_status');

        $query = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->join('leave_types', 'leave_types.id', '=', 'leave_applications.leave_type_id')
        ->select('users.*', 'leave_applications.*', 'leave_types.name as leave_type_name');

        if($request->get('name') != '') {
            $query->where('users.name','like','%'.$search_name.'%');
        }

        if($request->get('date_from') != '' && $request->get('date_to') != '') {
            $query->wherebetween('leave_applications.date_from', [$date_from, $date_to]);
        }

        if($request->get('leave_type') != '') {
            $query->where('leave_applications.leave_type_id','like','%'.$leave_type.'%');
        }

        if($request->get('leave_status') != '') {
            if($request->get('leave_status') == 'PENDING') {
                $query->where('leave_applications.status','like','%PENDING_1%');
                $query->orwhere('leave_applications.status','like','%PENDING_2%');
                $query->orwhere('leave_applications.status','like','%PENDING_3%');
            }
            if($request->get('leave_status') == 'DENIED') {
                $query->where('leave_applications.status','like','%DENIED_1%');
                $query->orwhere('leave_applications.status','like','%DENIED_2%');
                $query->orwhere('leave_applications.status','like','%DENIED_3%');
            }
            else {
                $query->where('leave_applications.status','like','%'.$leave_status.'%');
            }
        }

        $users = $query->get();
        dd($users);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('A1', 'name');
        $rows = 2;

        $countapp = count($query);
        dd($countapp);
        $count = 1;

        for($d=0; $d<$countapp; $d++) {
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->setCellValue('A' . $rows, $users->name);
            
            $rows++;

        }

        $writer = new Xlsx($spreadsheet);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Leave_Applications.xlsx"');
        $writer->save("php://output");

        }
}