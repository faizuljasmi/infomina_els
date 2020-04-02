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
use App\LeaveApplication;

class ExcelController extends Controller
{
    public function index()
    {
        $users = User::sortable()
        ->join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->join('leave_types', 'leave_types.id', '=', 'leave_applications.leave_type_id')
        ->select('users.*', 'leave_applications.*', 'leave_types.name as leave_type_name', 'leave_applications.id as leave_app_id')
        ->paginate(15);

        $count_approve = LeaveApplication::where('leave_applications.status','like','%APPROVED%')
        ->count();

        $count_cancel = LeaveApplication::where('leave_applications.status','like','%CANCELLED%')
        ->count();

        $count_pending = LeaveApplication::where('leave_applications.status','like','%PENDING_1%')
        ->orwhere('leave_applications.status','like','%PENDING_2%')
        ->orwhere('leave_applications.status','like','%PENDING_3%')
        ->count();

        $count_reject = LeaveApplication::where('leave_applications.status','like','%DENIED_1%')
        ->orwhere('leave_applications.status','like','%DENIED_2%')
        ->orwhere('leave_applications.status','like','%DENIED_3%')
        ->count();

        $count_all = LeaveApplication::count();
        
        return view('excel/transfer')->with(compact('users', 'count_approve', 'count_pending', 'count_reject', 'count_cancel', 'count_all'));
    }

    public function change_status(Request $request)
    {
        $new_status = $request->get('change_status');
        $user_id = $request->get('status_user_id');
        $app_id = $request->get('status_app_id');
        // dd($app_id);

        $update_status = LeaveApplication::where('id', '=', $app_id)
        ->where('user_id', '=', $user_id)
        ->first();

        $update_status->status = $new_status;
        $update_status->save();
        
        // $hist = new History;
        // $hist->leave_application_id = $leaveApplication->id;
        // $hist->user_id = $user->id;
        // $hist->action = "Approved";
        // $hist->save();

        return back();
    }

    public function search(Request $request)
    {
        $search_name = $request->get('name');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $leave_type = $request->get('leave_type');
        $leave_status = $request->get('leave_status');

        $count_approve = LeaveApplication::where('leave_applications.status','like','%APPROVED%')
        ->count();

        $count_cancel = LeaveApplication::where('leave_applications.status','like','%CANCELLED%')
        ->count();

        $count_pending = LeaveApplication::where('leave_applications.status','like','%PENDING_1%')
        ->orwhere('leave_applications.status','like','%PENDING_2%')
        ->orwhere('leave_applications.status','like','%PENDING_3%')
        ->count();

        $count_reject = LeaveApplication::where('leave_applications.status','like','%DENIED_1%')
        ->orwhere('leave_applications.status','like','%DENIED_2%')
        ->orwhere('leave_applications.status','like','%DENIED_3%')
        ->count();

        $count_all = LeaveApplication::count();

        $query = User::sortable()
        ->join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->join('leave_types', 'leave_types.id', '=', 'leave_applications.leave_type_id')
        ->select('users.*', 'leave_applications.*', 'leave_types.name as leave_type_name', 'leave_applications.id as leave_app_id');

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

        return view('excel/transfer')->with(compact('users', 'search_name', 'date_from', 'date_to', 'leave_type', 'leave_status', 'count_approve', 'count_pending', 'count_reject', 'count_cancel', 'count_all'));
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'import_file'  => 'required|mimes:xls,xlsx'
           ]);

        Excel::import(new Import(), request()->file('import_file'));

        return back()->with('success', 'Data imported successfully.');
    }

    public function export_all()
    {
        $users = User::join('leave_applications', 'leave_applications.user_id', '=', 'users.id')
        ->join('leave_types', 'leave_types.id', '=', 'leave_applications.leave_type_id')
        ->select('users.*', 'leave_applications.*', 'leave_types.name as leave_type_name')
        ->get();

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
        header('Content-Disposition: attachment; filename="Leave_Applications_All.xlsx"');
        $writer->save("php://output");

    }

    public function export_search(Request $request)
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

    public function export_leave_balance()
    {
        $annual = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '1' )->get();

        $calamity = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '2' )->get();

        $sick = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '3' )->get();

        $hospitalization = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '4' )->get();

        $compassionate = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '5' )->get();

        $emergency = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '6' )->get();

        $marriage = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '7' )->get();

        $maternity = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '8' )->get();

        $paternity = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '9' )->get();

        $traning = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '10' )->get();

        $unpaid = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '11' )->get();

        $replacement = User::leftjoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
        ->where('leave_balances.leave_type_id', '=', '12' )->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('A1', 'No.');
        $sheet->getStyle('B')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('B1', 'Name');
        $sheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('C1', 'Staff ID');
        $sheet->getStyle('D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('D1', 'Annual');
        $sheet->getStyle('E')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('E1', 'Calamity');
        $sheet->getStyle('F')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('F1', 'Sick');
        $sheet->getStyle('G')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('G1', 'Hospitalization');
        $sheet->getStyle('H')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('H1', 'Compassionate');
        $sheet->getStyle('I')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('I1', 'Emergency');
        $sheet->getStyle('J')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('J1', 'Marriage');
        $sheet->getStyle('K')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('K1', 'Maternity');
        $sheet->getStyle('L')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('L1', 'Paternity');
        $sheet->getStyle('M')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('M1', 'Traning');
        $sheet->getStyle('N')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('N1', 'Unpaid');
        $sheet->getStyle('O')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValue('O1', 'Replacement');
        $rows = 2;

        $countapp = count($annual);
        $count = 1;

        for($d=0; $d<$countapp; $d++) {
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->setCellValue('A' . $rows, $count++);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->setCellValue('B' . $rows, $annual[$d]->name);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->setCellValue('C' . $rows, $annual[$d]->staff_id);
            if ( $annual ) {
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->setCellValue('D' . $rows, $annual[$d]->no_of_days);
            }
            if ( $calamity ) {
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->setCellValue('E' . $rows, $calamity[$d]->no_of_days);
            }
            if ( $sick ) {
                $sheet->getColumnDimension('F')->setAutoSize(true);
                $sheet->setCellValue('F' . $rows, $sick[$d]->no_of_days);
            }
            if ( $hospitalization ) {
                $sheet->getColumnDimension('G')->setAutoSize(true);
                $sheet->setCellValue('G' . $rows, $hospitalization[$d]->no_of_days);
            }
            if ( $compassionate ) {
                $sheet->getColumnDimension('H')->setAutoSize(true);
                $sheet->setCellValue('H' . $rows, $compassionate[$d]->no_of_days);
            }
            if ( $emergency ) {
                $sheet->getColumnDimension('I')->setAutoSize(true);
                $sheet->setCellValue('I' . $rows, $emergency[$d]->no_of_days);
            }
            if ( $marriage ) {
                $sheet->getColumnDimension('J')->setAutoSize(true);
                $sheet->setCellValue('J' . $rows, $marriage[$d]->no_of_days);
            }
            if ( $maternity ) {
                $sheet->getColumnDimension('K')->setAutoSize(true);
                $sheet->setCellValue('K' . $rows, $maternity[$d]->no_of_days);
            }
            if ( $paternity ) {
                $sheet->getColumnDimension('L')->setAutoSize(true);
                $sheet->setCellValue('L' . $rows, $paternity[$d]->no_of_days);
            }
            if ( $traning ) {
                $sheet->getColumnDimension('M')->setAutoSize(true);
                $sheet->setCellValue('M' . $rows, $traning[$d]->no_of_days);
            }
            if ( $unpaid ) {
                $sheet->getColumnDimension('N')->setAutoSize(true);
                $sheet->setCellValue('N' . $rows, $unpaid[$d]->no_of_days);
            }
            if ( $replacement ) {
                $sheet->getColumnDimension('O')->setAutoSize(true);
                $sheet->setCellValue('O' . $rows, $replacement[$d]->no_of_days);
            }
            $rows++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Leave_Balance.xlsx"');
        $writer->save("php://output");

    }
}