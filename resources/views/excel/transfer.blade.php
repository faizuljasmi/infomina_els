@extends('adminlte::page')

@section('content')
<style>
    .buttonStat {width: 100px;}
    .card {margin: 0 auto; float: none; margin-bottom: 20px;}
</style>
@if ($errors->any())
<div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@if($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
<div class="mt-2 col-md-12">
    <div class="card">
        <div class="card-header bg-teal">
            Reports
        </div>
        <div class="card-body">
            <div class="panel panel-default">
                <div class="row">
                    <div class="card col-8">
                        <div class="card-body">
                            <form id="btnSearch" class="input-horizontal" action="{{ route('search') }}" method="get">
                                <div class="form-inline form-group">
                                    <!-- Name -->
                                    <div class="input-group col-12">
                                        <input type="search" name="name" id="name" placeholder="Name" value="{{ isset($search_name)? $search_name: '' }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-inline form-group">
                                    <!-- Date From -->
                                    <div class="input-group col-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar-day"></i>
                                            </span>
                                        </div>
                                        <input placeholder="Leave From" class="form-control" type="text" onfocus="(this.type='date')" value="{{ isset($date_from)? $date_from: '' }}" name="date_from" id="date_from">
                                    </div>
                                    <!-- Leave Type -->
                                    <div class="input-group col-6">
                                        <select class="form-control" name="leave_type" id="leave_type">
                                            <option value="" disabled selected>Select Leave Type</option>
                                            <option value="1">Annual</option>
                                            <option value="2">Calamity</option>
                                            <option value="3">Sick</option>
                                            <option value="4">Hospitalization</option>
                                            <option value="5">Compassionate</option>
                                            <option value="6">Emergency</option>
                                            <option value="7">Marriage</option>
                                            <option value="8">Maternity</option>
                                            <option value="8">Paternity</option>
                                            <option value="8">Traning</option>
                                            <option value="8">Unpaid</option>
                                            <option value="8">Replacement</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-inline form-group">
                                    <!-- Date To -->
                                    <div class="input-group col-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar-day"></i>
                                            </span>
                                        </div>
                                        <input placeholder="Leave To" class="form-control" type="text" onfocus="(this.type='date')" value="{{ isset($date_to)? $date_to: '' }}" name="date_to" id="date_to">
                                    </div>
                                    <!-- Status -->
                                    <div class="input-group col-6">
                                        <select class="form-control" name="leave_status" id="leave_status">
                                            <option value="" disabled selected>Select Status</option>
                                            <option value="PENDING">In Progress</option>
                                            <option value="APPROVED">Approved</option>
                                            <option value="CANCELLED">Cancelled</option>
                                            <option value="DENIED">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                            <div class="d-flex justify-content-end col-12">
                                <button data-toggle="collapse" data-target="#importCard" class="btn btn-warning mr-1">Import</button>
                                <button form="btnExport" type="submit" class="btn btn-warning mr-1">Export</button>
                                <button onclick="resetForm()" type="button" class="btn btn-primary mr-1">Reset</button>
                                <button form="btnSearch" type="submit" class="btn btn-primary">Search</button>
                            </div>
                            <form id="btnExport" action="{{ route('excel_export') }}" enctype="multipart/form-data">
                                <input type="hidden" name="excel_name" value="{{isset($search_name)? $search_name: ''}}">
                                <input type="hidden" name="excel_date_from" value="{{isset($date_from)? $date_from: ''}}">
                                <input type="hidden" name="excel_date_to" value="{{isset($date_to)? $date_to: ''}}">
                                <input type="hidden" name="excel_leave_type" value="{{isset($leave_type)? $leave_type: ''}}">
                                <input type="hidden" name="excel_leave_status" value="{{isset($leave_status)? $leave_status: ''}}">
                            </form>
                        </div>
                    </div>
                    <div class="card col-4">
                        <div class="card-body">
                        <table class="table table-sm table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th colspan="2">Leave Applications</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Approved</td>
                                <td>{{ $count_approve }}</td>
                            </tr>
                            <tr>
                                <td>Pending</td>
                                <td>{{ $count_pending }}</td>
                            </tr>
                            <tr>
                                <td>Rejected</td>
                                <td>{{ $count_reject }}</td>
                            </tr>
                            <tr>
                                <td>Cancelled</td>
                                <td>{{ $count_cancel }}</td>
                            </tr>
                            <tr>
                                <td><b>Total</b></td>
                                <td><b>{{ $users->total() }}</b></td>
                            </tr>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="importCard" class="collapse">
                <div class="card col-12">
                    <div class="card-body">
                        <form class="form-inline" action="{{ route('excel_import') }}" method="post">
                            <div class="form-group">
                                <input type="file" class="form-control" name="import_file" />
                            </div>
                            <button style="margin-left: 10px;" class="btn btn-success mr-2" type="submit">Upload</button>
                        </form>
                    </div>
                </div>
                </div>
                @if ($users->count() > 0)
                    <h6><strong>Displaying {{$users->count()}} out of {{$users->total()}} records.</strong></h6>
                @endif
                <table class="table table-sm table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>No.</th>
                    <th>@sortablelink('name', 'Name')</th>
                    <th>Day(s)</th>
                    <th>Type</th>
                    <th width="10%">@sortablelink('date_from', 'From Date')</th>
                    <th width="10%">@sortablelink('date_from', 'To Date')</th>
                    <th width="10%">@sortablelink('date_from', 'Resume Date')</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th width="10%">@sortablelink('created_at', 'Apply Date')</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = 0;?>
                @foreach($users as $row)
                <tr>
                    <td>{{ ++$count }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->total_days }}</td>
                    <td>{{ $row->leave_type_name }}</td>
                    <td>{{ $row->date_from }}</td>
                    <td>{{ $row->date_to }}</td>
                    <td>{{ $row->date_resume }}</td>
                    <td align="center">
                        <button type="button" class="btn btn-sm btn-info" data-toggle="popover" data-trigger="focus" title="Details" 
                        data-content="{{ $row->reason }}">View</button>
                    </td>
                    <td align="center">
                        @if ($row->status == "APPROVED" )
                            <button type="button" class="btn buttonStat btn-sm btn-success" disabled>Approved</button>
                        @elseif ($row->status == "CANCELLED")
                            <button type="button" class="btn buttonStat btn-sm btn-warning" disabled>Cancelled</button>
                        @elseif ($row->status == "DENIED_1" || $row->status == "DENIED_2" || $row->status == "DENIED_3")
                            <button type="button" class="btn buttonStat btn-sm btn-danger" disabled>Rejected</button>
                        @else
                            <button type="button" class="btn buttonStat btn-sm btn-primary" disabled>In Progress</button>
                        @endif
                    </td>
                    <td>{{\Carbon\Carbon::parse($row->created_at)->isoFormat('Y-MM-DD')}}</td>
                </tr>
                @endforeach
                @if ($users->count() == 0)
                    <tr align="center">
                        <td colspan="10"><strong>No records found.</strong></td>
                    </tr>
                @endif
                </tbody>
                </table>
                {!! $users->appends(\Request::except('page'))->render() !!}
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<script>

$(function () {
  $('[data-toggle="popover"]').popover()
})

function resetForm() {
    document.getElementById("name").value = '';
    document.getElementById("date_from").value = '';
    document.getElementById("date_to").value = '';
    document.getElementById("leave_type").value = '';
    document.getElementById("leave_status").value = '';
    window.location.href = "{{ route('search') }}";
}

</script>
@endsection
