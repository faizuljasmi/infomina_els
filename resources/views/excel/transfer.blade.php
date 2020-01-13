<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" value="{{ csrf_token() }}"/>
    <title>Data Transfers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" ></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" ></script>
</head>
<body>

<br/>

<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            Import Excel
        </div>
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
    <div class="card-body">
        <div class="float-left">
            <form class="form-inline" action="{{url('transfer/import')}}" method="post" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">
                    <div class="custom-file">
                        <input type="file" class="form-control" name="import_file"/>
                    </div>
                </div>
                <button style="margin-left: 10px;" class="btn btn-success" type="submit">Import</button>
            </form>
        </div>
        <div class="float-left">
            <form action="{{url('transfer/export')}}" enctype="multipart/form-data">
                <button class="btn btn-dark" type="submit">Export</button>
            </form>
        </div>
    </div>

<br/>

<div class="panel panel-default">
    <div class="panel-heading">
    <h3 class="panel-title">Users</h3>
    </div>
    <div class="panel-body">
    <div class="table-responsive">
    @if(count($users))
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Staff ID</th>
                    <th>E-mail</th>
                    <th>User Type</th>
                    <th>Employee Type ID</th>
                    <th>Employee Group ID</th>
                    <th>Joined Date</th>
                    <th>Job Title</th>
                    <th>Name</th>
                    <th>Emergency Contact Name</th>
                    <th>Emergency Contact No</th>
                </tr>
                @foreach($users as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->staff_id }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->user_type }}</td>
                    <td>{{ $u->emp_type_id }}</td>
                    <td>{{ $u->emp_group_id }}</td>
                    <td>{{ $u->join_date }}</td>
                    <td>{{ $u->gender }}</td>
                    <td>{{ $u->job_title }}</td>
                    <td>{{ $u->emergency_contact_name }}</td>
                    <td>{{ $u->emergency_contact_no }}</td>
                </tr>
                @endforeach
            </table>
    @endif

</div>

</body>
</html>