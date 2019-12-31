@extends('adminlte::page')
 


@section('content_header')
 @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="icon fa fa-check"></i>
            {{ session()->get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
 
    <h1>User Settings</h1>
@stop
@section('content')
    <div class = "row">
    
    <div class ="col-md-6">
    <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
        Create New User
        </button>
    </div>

<!-- ACTIVE USERS LIST -->
<div class="mt-2 col-md-12">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Users List</strong>
            </div>
                <div class="card-body">
                {{ $users->links() }}
                <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                            <th style ="width: 7%" scope="col">Staff ID</th>
                            <th scope="col">Name</th>
                            <th style="width: 10%" scope="col">User Type</th>
                            <th style="width: 5%" scope="col">Status</th>
                            <th style="width: 13%" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $u)
                        @if($u->id != auth()->user()->id && $u->status == 'Active')
                            <tr>
                            <td>{{$u->staff_id}}</td>
                            <td>{{$u->name}}</td>
                            <td>{{$u->user_type}}</td>
                            <td>
                                @if($u->status == 'Active')
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="View user profile">
                                <a href="{{route('user_view', $u->id)}}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                            </span>
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Edit user profile and leave">
                                <a href="{{route('user_edit', $u->id)}}" class="btn btn-info btn-sm"><i class="fa fa-pencil-alt"></i></a>
                            </span>
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Delete user permanently" onclick="return confirm('Warning: Are you sure you want to delete this user? Deleting this user will also delete all users associated with it. Make sure you have detach all of its dependencies.')">
                                <a href="{{route('user_delete', $u->id)}}" class="btn btn-danger btn-sm"><i class="fa fa-trash-alt"></i></a>
                            </span>
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Deactivate user from the system">
                                <a href="{{route('user_deactivate', $u->id)}}" class="btn btn-warning btn-sm"><i class="fas fa-user-slash"></i></a>
                            </span>
                            </td>
                            </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
        </div>
</div>

<!-- INACTIVE USERS LIST -->
<div class="mt-2 col-md-12">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Inactive Users List</strong>
            </div>
                <div class="card-body">
                {{ $users->links() }}
                <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                            <th style ="width: 7%" scope="col">Staff ID</th>
                            <th scope="col">Name</th>
                            <th style="width: 10%" scope="col">User Type</th>
                            <th style="width: 5%" scope="col">Status</th>
                            <th style="width: 10%" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $u)
                        @if($u->id != auth()->user()->id && $u->status == 'Inactive')
                            <tr>
                            <td>{{$u->staff_id}}</td>
                            <td>{{$u->name}}</td>
                            <td>{{$u->user_type}}</td>
                            <td>
                                @if($u->status == 'Active')
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="View user profile">
                                <a href="{{route('user_view', $u->id)}}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                            </span>
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Edit user profile and leave">
                                <a href="{{route('user_edit', $u->id)}}" class="btn btn-info btn-sm"><i class="fa fa-pencil-alt"></i></a>
                            </span>
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Delete user permanently" onclick="return confirm('Warning: Are you sure you want to delete this user? Deleting this user will also delete all users associated with it. Make sure you have detach all of its dependencies.')">
                                <a href="{{route('user_delete', $u->id)}}" class="btn btn-danger btn-sm"><i class="fa fa-trash-alt"></i></a>
                            </span>
                            </td>
                            </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
        </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Create New Employee</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form method="POST" action="{{route('user_store')}}">
                    {{ csrf_field() }}
                    <label for="staff_id">Staff ID:</label>
                    <div class="input-group mb-3">
                    <input type="text" name="staff_id" class="form-control {{ $errors->has('staff_id') ? 'is-invalid' : '' }}" value="IF##"
                           placeholder="{{ __('adminlte::adminlte.staff_id') }}" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-id-card"></span>
                        </div>
                    </div>

                    @if ($errors->has('staff_id'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('staff_id') }}</strong>
                        </div>
                    @endif
                </div>
                <label for="name">Full Name:</label>
                    <div class="input-group mb-3">
                    <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}"
                           placeholder="{{ __('adminlte::adminlte.full_name') }}" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>

                    @if ($errors->has('name'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('name') }}</strong>
                        </div>
                    @endif
                </div>
                <label for="email">Email:</label>
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}"
                           placeholder="{{ __('adminlte::adminlte.email') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @if ($errors->has('email'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('email') }}</strong>
                        </div>
                    @endif
                </div>
                <label for="password">Password:</label>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           placeholder="{{ __('adminlte::adminlte.password') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @if ($errors->has('password'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('password') }}</strong>
                        </div>
                    @endif
                </div>
                <label for="password_confirmation">Retype Password:</label>
                <div class="input-group mb-3">
                    <input type="password" name="password_confirmation" class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                           placeholder="{{ __('adminlte::adminlte.retype_password') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @if ($errors->has('password_confirmation'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                        </div>
                    @endif
                </div>
                <div class = "form-group">
                <label for="user_type">Gender:</label>
                    <select class="form-control" id="gender" name="gender">
                    <option>Male</option>
                    <option>Female</option>
                    </select>
                </div>
                <div class = "form-group">
                <label for="user_type">User Level:</label>
                    <select class="form-control" id="user_type" name="user_type">
                    <option>Admin</option>
                    <option>Authority</option>
                    <option>Employee</option>
                    </select>
                </div>
                <!-- Choose Employee Type -->
                <div class = "form-group">
                <label for="employee_type">Employee Type:</label>
                    <select class="form-control" id="emp_type_id" name="emp_type_id">
                    @foreach($empTypes as $et)
                        <option value="{{$et->id}}">{{$et->name}}</option>
                    @endforeach
                    </select>
                </div>
                <!-- Choose Employee Group -->
                <div class = "form-group">
                <label for="employee_type">Employee Group:</label>
                    <select class="form-control" id="emp_group_id" name="emp_group_id">
                    <option value ="" selected>Unassigned</option>
                    @foreach($empGroups as $eg)
                        <option value="{{$eg->id}}">{{$eg->name}}</option>
                    @endforeach
                    </select>
                </div>
                <!-- Enter Job Title -->
                <div class = "form-group">
                <label for="employee_type">Job Title:</label>
                <input class="form-control" type="text" placeholder="Position Name" id="job_title" name="job_title">
                </div>
                <!-- Enter Join Date -->
                <div class = "form-group">
                <label for="employee_type">Join Date:</label>
                <input class="form-control" type="date" placeholder="Join Date" id="join_date" name="join_date">
                </div>
                    <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Create</button>
        </div>
            </form> 
      </div>
    </div>
  </div>
</div>
</div>
@endsection

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('js')
    @yield('js')
@endsection