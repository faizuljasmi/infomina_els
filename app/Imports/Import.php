<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Import implements WithMultipleSheets 
{
    public function sheets(): array
    {
        return [
            'User' => new ImportUser(),
            'Approval Auth' => new ImportApprovalAuth(),
            'Leave Application' => new ImportLeaveApp(),
            'Leave Earning' => new ImportLeaveEarn(),
        ];
    }
}