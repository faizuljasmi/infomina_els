@extends('adminlte::page')

@section('content')
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
<div class="container">
    <div class="card mt-4">
        <div class="card-header bg-teal">
            Import Excel
        </div>
        <div class="card-body">
            <div class="float-left">
                <form class="form-inline" action="{{route('excel_import')}}" method="post"
                    enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="form-group">
                        <input type="file" class="form-control" name="import_file" />
                    </div>
                    <button style="margin-left: 10px;" class="btn btn-success mr-2" type="submit">Import</button>
                </form>
            </div>
            <div class="float-left">
                <form action="{{route('excel_export')}}" enctype="multipart/form-data">
                    <button class="btn btn-dark" type="submit">Export</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
