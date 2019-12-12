@extends('adminlte::page')
 
@section('content')

 @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="icon fa fa-check"></i>
            {{ session()->get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
 
    <h2>User Settings</h2>

    <div class = "row">
    
    <div class ="col-md-6">
    <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
        Create New User
        </button>
    </div>

    <div class="mt-2 col-md-12">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Users List</strong>
            </div>
                <div class="card-body">
                <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                            <th style ="width: 7%" scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th style="width: 10%" scope="col">User Type</th>
                            <th style="width: 10%" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $u)
                            <tr>
                            <td>{{$u->id}}</td>
                            <td>{{$u->name}}</td>
                            <td>{{$u->user_type}}</td>
                            <td>
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="View user profile">
                                <a href="{{route('user_view', $u->id)}}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                            </span>
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Edit user profile and leave">
                                <a href="{{route('user_edit', $u->id)}}" class="btn btn-info btn-sm"><i class="fa fa-pencil-alt"></i></a>
                            </span>
                            <button class="btn btn-danger btn-sm"><i class="fa fa-trash-alt"></i></button>
                            </td>
                            </tr>
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
      <form method="POST" action="/create">
                    {{ csrf_field() }}
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
                <label for="user_type">User Level:</label>
                    <select class="form-control" id="user_type" name="user_type">
                    <option>Admin</option>
                    <option>Employee</option>
                    </select>
                </div>

                <div class = "form-group">
                <label for="employee_type">Employee Type:</label>
                    <select class="form-control" id="emp_type_id" name="emp_type_id">
                    @foreach($empTypes as $et)
                        <option value="{{$et->id}}">{{$et->name}}</option>
                    @endforeach
                    </select>
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
@endsection

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('js')
    @yield('js')
@endsection