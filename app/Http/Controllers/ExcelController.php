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

        $count_cancel = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->where('leave_applications.status','like','%CANCELLED%')
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
        
        return view('excel/transfer')->with(compact('users', 'count_approve', 'count_pending', 'count_reject', 'count_cancel'));
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

        $count_cancel = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->where('leave_applications.status','like','%CANCELLED%')
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

        return view('excel/transfer')->with(compact('users', 'search_name', 'date_from', 'date_to', 'leave_type', 'leave_status', 'count_approve', 'count_pending', 'count_reject', 'count_cancel'));
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
        $search_name = $request->get('excel_name');
        $date_from = $request->get('excel_date_from');
        $date_to = $request->get('excel_date_to');
        $leave_type = $request->get('excel_leave_type');
        $leave_status = $request->get('excel_leave_status');

        $query = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->join('leave_types', 'leave_types.id', '=', 'leave_applications.leave_type_id')
        ->select('users.*', 'leave_applications.*', 'leave_types.name as leave_type_name');

        if($request->get('excel_name') != '') {
            $query->where('users.name','like','%'.$search_name.'%');
        }

        if($request->get('excel_date_from') != '' && $request->get('excel_date_to') != '') {
            $query->wherebetween('leave_applications.date_from', [$date_from, $date_to]);
        }

        if($request->get('excel_leave_type') != '') {
            $query->where('leave_applications.leave_type_id','like','%'.$leave_type.'%');
        }

        if($request->get('excel_leave_status') != '') {
            if($request->get('leave_status') == 'PENDING') {
                $query->where('leave_applications.status','like','%PENDING_1%');
                $query->orwhere('leave_applications.status','like','%PENDING_2%');
                $query->orwhere('leave_applications.status','like','%PENDING_3%');
            }
            if($request->get('excel_leave_status') == 'DENIED') {
                $query->where('leave_applications.status','like','%DENIED_1%');
                $query->orwhere('leave_applications.status','like','%DENIED_2%');
                $query->orwhere('leave_applications.status','like','%DENIED_3%');
            }
            else {
                $query->where('leave_applications.status','like','%'.$leave_status.'%');
            }
        }

        $users = $query->get();
        // dd($users);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('A1', 'No.');
        $sheet->getStyle('B')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('B1', 'Name');
        $sheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('C1', 'Day(s)');
        $sheet->getStyle('D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('D1', 'Type');
        $sheet->getStyle('E')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('E1', 'From Date');
        $sheet->getStyle('F')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('F1', 'To Date');
        $sheet->getStyle('G')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('G1', 'Resume Date');
        $sheet->getStyle('H')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('H1', 'Reason');
        $sheet->getStyle('I')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('I1', 'Status');
        $sheet->getStyle('J')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('J1', 'Date Apply');
        $rows = 2;

        $countapp = count($users);
        // dd($countapp);
        $count = 1;

        for($d=0; $d<$countapp; $d++) {
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->setCellValue('A' . $rows, $count++);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->setCellValue('B' . $rows, $users[$d]->name);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->setCellValue('C' . $rows, $users[$d]->total_days);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->setCellValue('D' . $rows, $users[$d]->leave_type_name);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->setCellValue('E' . $rows, $users[$d]->date_from);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->setCellValue('F' . $rows, $users[$d]->date_to);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->setCellValue('G' . $rows, $users[$d]->date_resume);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->setCellValue('H' . $rows, $users[$d]->reason);
            $sheet->getColumnDimension('I')->setAutoSize(true);
            $sheet->setCellValue('I' . $rows, $users[$d]->status);
            $sheet->getColumnDimension('J')->setAutoSize(true);
            $sheet->setCellValue('J' . $rows, \Carbon\Carbon::parse($users[$d]->created_at)->isoFormat('Y-MM-DD'));
            $rows++;

            
        }

        $writer = new Xlsx($spreadsheet);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Leave_Applications.xlsx"');
        $writer->save("php://output");

        }
}