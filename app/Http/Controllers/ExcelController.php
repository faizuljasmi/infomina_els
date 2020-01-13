<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\Import;
use App\Exports\Export;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use DB;
use App\User;

class ExcelController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('excel/transfer')->with('users', $users);
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'import_file'  => 'required|mimes:xls,xlsx'
           ]);
        Excel::import(new Import(), request()->file('import_file'));
        return back()->with('success', 'Data imported successfully.');
    }

    public function export()
    {
        return Excel::download(new Export(), 'export.xlsx');
    }
}
