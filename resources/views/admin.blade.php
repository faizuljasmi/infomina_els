@extends('adminlte::page')

@section('title', 'Infomina/ELS')

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
<h6 class="float-right">Hello, <strong>{{$user->name}}</strong> <span
        class="badge badge-info">{{$user->user_type}}</span></h6>
<h1 class="m-0 text-dark">Dashboard</h1>
<div>
    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif
</div>

<head>
    <meta charset='utf-8' />
    <link href='{{asset('assets/fullcalendar/packages/core/main.css')}}' rel='stylesheet' />
    <link href='{{asset('assets/fullcalendar/packages/daygrid/main.css')}}' rel='stylesheet' />
    <script src='{{asset('assets/fullcalendar/packages/core/main.js')}}'></script>
    <script src='{{asset('assets/fullcalendar/packages/interaction/main.js')}}'></script>
    <script src='{{asset('assets/fullcalendar/packages/daygrid/main.js')}}'></script>

    <script>

      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var evnts = {!! json_encode($events, JSON_HEX_TAG) !!};
        var calendar = new FullCalendar.Calendar(calendarEl, {

          defaultView: 'dayGridWeek',
          locale: 'en-GB',
          plugins: [ 'interaction', 'dayGrid' ],
          header: {
            left: 'prevYear,prev,next,nextYear today',
            center: 'title',
            right: 'dayGridWeek'
          },
          height: 700,
          navLinks: true, // can click day/week names to navigate views
          eventLimit: true, // allow "more" link when too many events
          events: evnts,
        });
        calendar.render();
      });

    </script>
    <style>

      #calendar {
        max-width: auto;
        margin: 0 auto;
      }

    </style>
    </head>


@stop

@section('content')
<section class="content">
    <div class="container-fluid">

        <!-- <div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Employees</span>
                <span class="info-box-number">
                  112
                </span>
              </div> -->
        <!-- /.info-box-content -->
        <!-- </div> -->
        <!-- /.info-box -->
        <!-- </div> -->
        <!-- /.col -->
        <!-- <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-sign-out-alt"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">On Leave</span>
                <span class="info-box-number">12</span>
              </div> -->
        <!-- /.info-box-content -->
        <!-- </div> -->
        <!-- /.info-box -->
        <!-- </div> -->
        <!-- /.col -->

        <!-- fix for small devices only -->
        <!-- <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clipboard"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Pending Applications</span>
                <span class="info-box-number">{{$leaveApps->count()}}</span>
              </div> -->
        <!-- /.info-box-content -->
        <!-- </div> -->
        <!-- /.info-box -->
        <!-- </div>
        </div> -->
        <!-- /.row -->

        <!-- ////////////////////////////////////////////////////////// -->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-teal">
                        <h5 class="card-title"><strong>Leave Applications</strong> <i class="fas fa-info-circle"
                                data-toggle="tooltip" data-placement="top"
                                title="This section shows pending leave applications that need your action."></i></h5>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p class="text-center">
                                    <strong>Recent Leave Applications</strong>
                                </p>
                                <h6><strong>Displaying {{$leaveApps->count()}} of {{$leaveApps->total()}}
                                        records.</strong>
                                </h6>
                                <table class="table table-striped table-bordered">
                                    @if($leaveApps->count() > 0)
                                    <thead>
                                        <tr>
                                            <th scope="col">No.</th>
                                            <th scope="col">Applicant</th>
                                            <th scope="col">Leave Type @sortablelink('leaveType.name','',[])</th>
                                            <th scope="col">From @sortablelink('date_from','',[])</th>
                                            <th scope="col">To @sortablelink('date_to','',[])</th>
                                            <th scope="col">Duration @sortablelink('total_days','',[])</th>
                                            <th scope="col">Submitted @sortablelink('created_at','',[])</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $count = ($leaveApps->currentPage()-1) * $leaveApps->perPage(); @endphp
                                        @foreach($leaveApps as $la)
                                        <tr>
                                            <th scope="row">{{++$count}}</th>
                                            <td>{{$la->user->name}}</td>
                                            <td>{{$la->leaveType->name}}</td>
                                            <td>{{ \Carbon\Carbon::parse($la->date_from)->isoFormat('ddd, D MMM YY')}}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($la->date_to)->isoFormat('ddd, D MMM YY')}}
                                            </td>
                                            <td>{{$la->total_days}} day(s)</td>
                                            <td>{{ \Carbon\Carbon::parse($la->created_at)->diffForHumans()}}</td>
                                            <td>
                                                @if(!isset($la->approver_id_2))
                                                @if($la->status == 'PENDING_1')
                                                <span class="badge badge-pill badge-warning"><i
                                                        class="far fa-clock"></i></i></span>
                                                @endif
                                                @elseif(!isset($la->approver_id_3))
                                                @if($la->status == 'PENDING_1')
                                                <span class="badge badge-pill badge-warning"><i
                                                        class="far fa-clock"></i></i></span>
                                                <span class="badge badge-pill badge-warning"><i
                                                        class="far fa-clock"></i></i></span>
                                                @elseif($la->status == 'PENDING_2')
                                                <span class="badge badge-pill badge-success"><i
                                                        class="far fa-check-circle"></i></span>
                                                <span class="badge badge-pill badge-warning"><i
                                                        class="far fa-clock"></i></i></span>
                                                @endif
                                                @else
                                                @if($la->status == 'PENDING_1')
                                                <span class="badge badge-pill badge-warning"><i
                                                        class="far fa-clock"></i></i></span>
                                                <span class="badge badge-pill badge-warning"><i
                                                        class="far fa-clock"></i></i></span>
                                                <span class="badge badge-pill badge-warning"><i
                                                        class="far fa-clock"></i></i></span>
                                                @elseif($la->status == 'PENDING_2')
                                                <span class="badge badge-pill badge-success"><i
                                                        class="far fa-check-circle"></i></span>
                                                <span class="badge badge-pill badge-warning"><i
                                                        class="far fa-clock"></i></i></span>
                                                <span class="badge badge-pill badge-warning"><i
                                                        class="far fa-clock"></i></i></span>
                                                @elseif($la->status == 'PENDING_3')
                                                <span class="badge badge-pill badge-success"><i
                                                        class="far fa-check-circle"></i></span>
                                                <span class="badge badge-pill badge-success"><i
                                                        class="far fa-check-circle"></i></span>
                                                <span class="badge badge-pill badge-warning"><i
                                                        class="far fa-clock"></i></i></span>
                                                @endif
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{route('view_application', $la->id)}}"><button type="button"
                                                        class="btn btn-success btn-sm">View</i></button></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <th>
                                            No pending application
                                        </th>
                                        @endif
                                    </tbody>
                                </table>
                                {!! $leaveApps->appends(\Request::except('page'),['pending' =>
                                $leaveApps->currentPage()])->render() !!}
                            </div>
                            <div class="col-lg-4 connectedSortable ui-sortable">
                                <!-- Calendar -->
                                <!-- Vanilla Calendar -->
                                <div class="card">
                                    <div class="card-header bg-teal">
                                        <strong>Calendar</strong>
                                    </div>
                                    <div class="myCalendar vanilla-calendar" style="margin: 20px auto"></div>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-warning" data-toggle="modal"
                                            data-target="#viewColleague">Team Members Applications</button>
                                    </div>
                                    <!-- Modal -->
                                    <div class="modal fade" id="viewColleague" tabindex="-1" role="dialog"
                                        aria-labelledby="viewColleagueTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewColleagueTitle">Team Members
                                                        Applications
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="col-md-12">
                                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                            @if(isset($user->emp_group_id))
                                                            <li class="nav-item">
                                                                <a class="nav-link active" id="group-one-tab"
                                                                    data-toggle="tab" href="#groupone" role="tab"
                                                                    aria-controls="groupone"
                                                                    aria-selected="true">{{$user->emp_group->name}}</a>
                                                            </li>
                                                            @endif
                                                            @if(isset($user->emp_group_two_id))
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="group-two-tab" data-toggle="tab"
                                                                    href="#grouptwo" role="tab" aria-controls="grouptwo"
                                                                    aria-selected="true">{{$user->emp_group_two->name}}</a>
                                                            </li>
                                                            @endif
                                                            @if(isset($user->emp_group_three_id))
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="group-three-tab"
                                                                    data-toggle="tab" href="#groupthree" role="tab"
                                                                    aria-controls="groupthree"
                                                                    aria-selected="true">{{$user->emp_group_three->name}}</a>
                                                            </li>
                                                            @endif
                                                            @if(isset($user->emp_group_four_id))
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="group-four-tab"
                                                                    data-toggle="tab" href="#groupfour" role="tab"
                                                                    aria-controls="groupfour"
                                                                    aria-selected="true">{{$user->emp_group_four->name}}</a>
                                                            </li>
                                                            @endif
                                                            @if(isset($user->emp_group_five_id))
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="group-five-tab"
                                                                    data-toggle="tab" href="#groupfive" role="tab"
                                                                    aria-controls="groupfive"
                                                                    aria-selected="true">{{$user->emp_group_five->name}}</a>
                                                            </li>
                                                            @endif
                                                        </ul>
                                                        <div class="tab-content" id="myTabContent">
                                                            @if(isset($user->emp_group_id))
                                                            <div class="tab-pane fade show active" id="groupone"
                                                                role="tabpanel" aria-labelledby="group-one-tab">
                                                                @foreach ($groupLeaveApps as $gla => $apps)
                                                                <h5><span class="badge badge-dark">{{$gla}}</span></h5>
                                                                <table
                                                                    class="table table-sm table-bordered table-striped">
                                                                    <tr class="bg-primary">
                                                                        <th style="width: 50%">Colleague Name</th>
                                                                        <th>Leave Type</th>
                                                                        <th>From</th>
                                                                        <th>To</th>
                                                                        <th style="width: 5%">Status</th>
                                                                    </tr>
                                                                    <?php $count = 0 ?>
                                                                    @foreach ($apps as $app)
                                                                    @if(( $user->emp_group_id ==
                                                                    $app->user->emp_group_id ||
                                                                    $user->emp_group_id ==
                                                                    $app->user->emp_group_two_id ||
                                                                    $user->emp_group_id ==
                                                                    $app->user->emp_group_three_id ||
                                                                    $user->emp_group_id ==
                                                                    $app->user->emp_group_four_id ||
                                                                    $user->emp_group_id ==
                                                                    $app->user->emp_group_five_id))
                                                                    <?php ++$count ?>
                                                                    <tr>
                                                                        <td><strong>{{$app->user->name}}</strong></td>
                                                                        <td>{{$app->leaveType->name}}</td>
                                                                        <td>{{ \Carbon\Carbon::parse($app->date_from)->isoFormat('ddd, D MMM')}}
                                                                        </td>
                                                                        <td>{{ \Carbon\Carbon::parse($app->date_to)->isoFormat('ddd, D MMM')}}
                                                                        </td>
                                                                        <td>
                                                                            @if($app->status == 'APPROVED')
                                                                            <span class="badge badge-success"><i
                                                                                    class="far fa-check-circle"></i></span>
                                                                            @else
                                                                            <span class="badge badge-warning"><i
                                                                                    class="far fa-clock"></i></span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endif
                                                                    @endforeach
                                                                    @if($count == 0)
                                                                    <tr>
                                                                        <td colspan="5"><strong>No Leave
                                                                                Applications</strong></td>
                                                                    </tr>
                                                                    @endif
                                                                </table>
                                                                @endforeach
                                                                <div class="float-right"><strong>Legends:</strong>
                                                                    <span class="badge badge-success"><i
                                                                            class="far fa-check-circle"></i>
                                                                        Approved</span>
                                                                    <span class="badge badge-warning"><i
                                                                            class="far fa-clock"></i>Pending</span>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            @if(isset($user->emp_group_two_id))
                                                            <div class="tab-pane fade" id="grouptwo" role="tabpanel"
                                                                aria-labelledby="group-two-tab">
                                                                @foreach ($groupLeaveApps as $gla => $apps)
                                                                <h5><span class="badge badge-dark">{{$gla}}</span></h5>
                                                                <table
                                                                    class="table table-sm table-bordered table-striped">
                                                                    <tr class="bg-primary">
                                                                        <th style="width: 50%">Colleague Name</th>
                                                                        <th>Leave Type</th>
                                                                        <th>From</th>
                                                                        <th>To</th>
                                                                        <th style="width: 5%">Status</th>
                                                                    </tr>
                                                                    <?php $count = 0 ?>
                                                                    @foreach ($apps as $app)
                                                                    @if(( $user->emp_group_two_id ==
                                                                    $app->user->emp_group_id ||
                                                                    $user->emp_group_two_id ==
                                                                    $app->user->emp_group_two_id ||
                                                                    $user->emp_group_two_id ==
                                                                    $app->user->emp_group_three_id ||
                                                                    $user->emp_group_two_id ==
                                                                    $app->user->emp_group_four_id ||
                                                                    $user->emp_group_two_id ==
                                                                    $app->user->emp_group_five_id))
                                                                    <?php ++$count ?>
                                                                    <tr>
                                                                        <td><strong>{{$app->user->name}}</strong></td>
                                                                        <td>{{$app->leaveType->name}}</td>
                                                                        <td>{{ \Carbon\Carbon::parse($app->date_from)->isoFormat('ddd, D MMM')}}
                                                                        </td>
                                                                        <td>{{ \Carbon\Carbon::parse($app->date_to)->isoFormat('ddd, D MMM')}}
                                                                        </td>
                                                                        <td>
                                                                            @if($app->status == 'APPROVED')
                                                                            <span class="badge badge-success"><i
                                                                                    class="far fa-check-circle"></i></span>
                                                                            @else
                                                                            <span class="badge badge-warning"><i
                                                                                    class="far fa-clock"></i></span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endif
                                                                    @endforeach
                                                                    @if($count == 0)
                                                                    <tr>
                                                                        <td colspan="5"><strong>No Leave
                                                                                Applications</strong></td>
                                                                    </tr>
                                                                    @endif
                                                                </table>
                                                                @endforeach
                                                                <div class="float-right"><strong>Legends:</strong>
                                                                    <span class="badge badge-success"><i
                                                                            class="far fa-check-circle"></i>
                                                                        Approved</span>
                                                                    <span class="badge badge-warning"><i
                                                                            class="far fa-clock"></i>Pending</span>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            @if(isset($user->emp_group_three_id))
                                                            <div class="tab-pane fade" id="groupthree" role="tabpanel"
                                                                aria-labelledby="group-three-tab">
                                                                @foreach ($groupLeaveApps as $gla => $apps)
                                                                <h5><span class="badge badge-dark">{{$gla}}</span></h5>
                                                                <table
                                                                    class="table table-sm table-bordered table-striped">
                                                                    <tr class="bg-primary">
                                                                        <th style="width: 50%">Colleague Name</th>
                                                                        <th>Leave Type</th>
                                                                        <th>From</th>
                                                                        <th>To</th>
                                                                        <th style="width: 5%">Status</th>
                                                                    </tr>
                                                                    <?php $count = 0 ?>
                                                                    @foreach ($apps as $app)
                                                                    @if(( $user->emp_group_three_id ==
                                                                    $app->user->emp_group_id ||
                                                                    $user->emp_group_three_id ==
                                                                    $app->user->emp_group_two_id ||
                                                                    $user->emp_group_three_id ==
                                                                    $app->user->emp_group_three_id ||
                                                                    $user->emp_group_three_id ==
                                                                    $app->user->emp_group_four_id ||
                                                                    $user->emp_group_three_id ==
                                                                    $app->user->emp_group_five_id))
                                                                    <?php ++$count ?>
                                                                    <tr>
                                                                        <td><strong>{{$app->user->name}}</strong></td>
                                                                        <td>{{$app->leaveType->name}}</td>
                                                                        <td>{{ \Carbon\Carbon::parse($app->date_from)->isoFormat('ddd, D MMM')}}
                                                                        </td>
                                                                        <td>{{ \Carbon\Carbon::parse($app->date_to)->isoFormat('ddd, D MMM')}}
                                                                        </td>
                                                                        <td>
                                                                            @if($app->status == 'APPROVED')
                                                                            <span class="badge badge-success"><i
                                                                                    class="far fa-check-circle"></i></span>
                                                                            @else
                                                                            <span class="badge badge-warning"><i
                                                                                    class="far fa-clock"></i></span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endif
                                                                    @endforeach
                                                                    @if($count == 0)
                                                                    <tr>
                                                                        <td colspan="5"><strong>No Leave
                                                                                Applications</strong></td>
                                                                    </tr>
                                                                    @endif
                                                                </table>
                                                                @endforeach
                                                                <div class="float-right"><strong>Legends:</strong>
                                                                    <span class="badge badge-success"><i
                                                                            class="far fa-check-circle"></i>
                                                                        Approved</span>
                                                                    <span class="badge badge-warning"><i
                                                                            class="far fa-clock"></i>Pending</span>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            @if(isset($user->emp_group_four_id))
                                                            <div class="tab-pane fade" id="groupfour" role="tabpanel"
                                                                aria-labelledby="group-four-tab">
                                                                @foreach ($groupLeaveApps as $gla => $apps)
                                                                <h5><span class="badge badge-dark">{{$gla}}</span></h5>
                                                                <table
                                                                    class="table table-sm table-bordered table-striped">
                                                                    <tr class="bg-primary">
                                                                        <th style="width: 50%">Colleague Name</th>
                                                                        <th>Leave Type</th>
                                                                        <th>From</th>
                                                                        <th>To</th>
                                                                        <th style="width: 5%">Status</th>
                                                                    </tr>
                                                                    <?php $count = 0 ?>
                                                                    @foreach ($apps as $app)
                                                                    @if(( $user->emp_group_four_id ==
                                                                    $app->user->emp_group_id ||
                                                                    $user->emp_group_four_id ==
                                                                    $app->user->emp_group_two_id ||
                                                                    $user->emp_group_four_id ==
                                                                    $app->user->emp_group_three_id ||
                                                                    $user->emp_group_four_id ==
                                                                    $app->user->emp_group_four_id ||
                                                                    $user->emp_group_four_id ==
                                                                    $app->user->emp_group_five_id))
                                                                    <?php ++$count ?>
                                                                    <tr>
                                                                        <td><strong>{{$app->user->name}}</strong></td>
                                                                        <td>{{$app->leaveType->name}}</td>
                                                                        <td>{{ \Carbon\Carbon::parse($app->date_from)->isoFormat('ddd, D MMM')}}
                                                                        </td>
                                                                        <td>{{ \Carbon\Carbon::parse($app->date_to)->isoFormat('ddd, D MMM')}}
                                                                        </td>
                                                                        <td>
                                                                            @if($app->status == 'APPROVED')
                                                                            <span class="badge badge-success"><i
                                                                                    class="far fa-check-circle"></i></span>
                                                                            @else
                                                                            <span class="badge badge-warning"><i
                                                                                    class="far fa-clock"></i></span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endif
                                                                    @endforeach
                                                                    @if($count == 0)
                                                                    <tr>
                                                                        <td colspan="5"><strong>No Leave
                                                                                Applications</strong></td>
                                                                    </tr>
                                                                    @endif
                                                                </table>
                                                                @endforeach
                                                                <div class="float-right"><strong>Legends:</strong>
                                                                    <span class="badge badge-success"><i
                                                                            class="far fa-check-circle"></i>
                                                                        Approved</span>
                                                                    <span class="badge badge-warning"><i
                                                                            class="far fa-clock"></i>Pending</span>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            @if(isset($user->emp_group_five_id))
                                                            <div class="tab-pane fade" id="groupfive" role="tabpanel"
                                                                aria-labelledby="group-five-tab">
                                                                @foreach ($groupLeaveApps as $gla => $apps)
                                                                <h5><span class="badge badge-dark">{{$gla}}</span></h5>
                                                                <table
                                                                    class="table table-sm table-bordered table-striped">
                                                                    <tr class="bg-primary">
                                                                        <th style="width: 50%">Colleague Name</th>
                                                                        <th>Leave Type</th>
                                                                        <th>From</th>
                                                                        <th>To</th>
                                                                        <th style="width: 5%">Status</th>
                                                                    </tr>
                                                                    <?php $count = 0 ?>
                                                                    @foreach ($apps as $app)
                                                                    @if(( $user->emp_group_five_id ==
                                                                    $app->user->emp_group_id ||
                                                                    $user->emp_group_five_id ==
                                                                    $app->user->emp_group_two_id ||
                                                                    $user->emp_group_five_id ==
                                                                    $app->user->emp_group_three_id ||
                                                                    $user->emp_group_five_id ==
                                                                    $app->user->emp_group_four_id ||
                                                                    $user->emp_group_five_id ==
                                                                    $app->user->emp_group_five_id))
                                                                    <?php ++$count ?>
                                                                    <tr>
                                                                        <td><strong>{{$app->user->name}}</strong></td>
                                                                        <td>{{$app->leaveType->name}}</td>
                                                                        <td>{{ \Carbon\Carbon::parse($app->date_from)->isoFormat('ddd, D MMM')}}
                                                                        </td>
                                                                        <td>{{ \Carbon\Carbon::parse($app->date_to)->isoFormat('ddd, D MMM')}}
                                                                        </td>
                                                                        <td>
                                                                            @if($app->status == 'APPROVED')
                                                                            <span class="badge badge-success"><i
                                                                                    class="far fa-check-circle"></i></span>
                                                                            @else
                                                                            <span class="badge badge-warning"><i
                                                                                    class="far fa-clock"></i></span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endif
                                                                    @endforeach
                                                                    @if($count == 0)
                                                                    <tr>
                                                                        <td colspan="5"><strong>No Leave
                                                                                Applications</strong></td>
                                                                    </tr>
                                                                    @endif
                                                                </table>
                                                                @endforeach
                                                                <div class="float-right"><strong>Legends:</strong>
                                                                    <span class="badge badge-success"><i
                                                                            class="far fa-check-circle"></i>
                                                                        Approved</span>
                                                                    <span class="badge badge-warning"><i
                                                                            class="far fa-clock"></i>Pending</span>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- ./card-body -->
            </div>
            <!-- /.card -->
        </div>

        <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <div class="col-md-12">
            <!-- MAP & BOX PANE -->
            <div class="card">
                <div class="card-header bg-teal">
                    <h3 class="card-title">Leave Calendar</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->



            </div>
            <!-- /.col -->
        </div>
        <div class="col-md-12">
            <!-- MAP & BOX PANE -->
            <div class="card">
                <div class="card-header bg-teal">
                    <h3 class="card-title">Leave Application History</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div id='calendar'></div>
                    <div class="d-md-flex">
                        <div class="p-1 flex-fill" style="overflow: hidden">
                            <div class="col-md-3 float-right mb-3">
                                <form action="{{ route('admin__leave_search') }}" method="get">
                                    <div class="input-group">
                                        <input type="search" name="search" class="form-control">
                                        <span class="input-group-prepend">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                            <h6><strong>Displaying {{$leaveHist->count()}} of {{$leaveHist->total()}} records.</strong>
                            </h6>
                            <table class="table table-striped table-bordered">
                                @if($leaveHist->count() > 0)
                                <thead>
                                    <tr>
                                        <th scope="col">No.</th>
                                        <th scope="col">Submitted by</th>
                                        <th scope="col">Leave Type @sortablelink('leaveType.name','',[])</th>
                                        <th scope="col">Duration @sortablelink('total_days','',[])</th>
                                        <th scope="col">From @sortablelink('date_from','',[])</th>
                                        <th scope="col">To @sortablelink('date_to','',[])</th>
                                        <th scope="col">Status @sortablelink('status','',[])</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $count = ($leaveHist->currentPage()-1) * $leaveHist->perPage(); @endphp
                                    @foreach($leaveHist as $lh)
                                    <tr>
                                        <th scope="row">{{++$count}}</th>
                                        <td>{{$lh->user->name}}</td>
                                        <td>{{$lh->leaveType->name}}</td>
                                        <td>{{$lh->total_days}} day(s)</td>
                                        <td>{{ \Carbon\Carbon::parse($lh->date_from)->isoFormat('ddd, D MMM YY')}}</td>
                                        <td>{{ \Carbon\Carbon::parse($lh->date_to)->isoFormat('ddd, D MMM YY')}}</td>
                                        <td>
                                            @if($lh->status == 'APPROVED')
                                            <span class="badge badge-pill badge-success">Approved</span>
                                            @elseif($lh->status == 'CANCELLED')
                                            <span class="badge badge-pill badge-secondary">Cancelled</span>
                                            @endif
                                        </td>
                                        <td><a href="{{route('view_application', $lh->id)}}"
                                                class="btn btn-success btn-sm" data-toggle="tooltip"
                                                title="View leave application">View</a></td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <th>No Record Found</th>
                                    @endif
                                </tbody>
                            </table>
                            {!! $leaveHist->appends(\Request::except('page'),['history' =>
                            $leaveHist->currentPage()])->render() !!}
                        </div>

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->



            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!--/. container-fluid -->
</section>

<script>
    $(document).ready(HolidayCreate);

  function HolidayCreate() {

    var dates = {!!json_encode($all_dates, JSON_HEX_TAG)!!};
    var applied = {!!json_encode($applied_dates, JSON_HEX_TAG)!!};
    var approved = {!!json_encode($approved_dates, JSON_HEX_TAG)!!};

    console.log(dates);

    let calendar = new VanillaCalendar({
      holiday: dates,
      applied: applied,
      approved: approved,
      selector: ".myCalendar",
      onSelect: (data, elem) => {
        // console.log(data, elem)
      }
    });


    const validation = {

      onchange: function(v, e, fc) {
        console.log("onchange", v, e, fc);
        let name = fc.name;

        if (name == FC.date_from.name || name == FC.date_to.name) {
          let error = validation.validateDateFromAndTo(name);
          if (error != null) {
            alert(error);
            _form.set(fc, "");
            return;
          }
        }

        validation._dateFrom(name);
        validation._dateTo(name);

        validation._totalDay(name);

      },
      validateDateFromAndTo: function(name) {

        let date_from = _form.get(FC.date_from);
        let date_to = _form.get(FC.date_to);



        if (
          (name == FC.date_from.name && calendar.isWeekend(date_from)) ||
          (name == FC.date_to.name && calendar.isWeekend(date_to))
        ) {
          return `Selected date is a WEEKEND. Please select another date.`;
        }
        if (
          (name == FC.date_from.name && calendar.isHoliday(date_from)) ||
          (name == FC.date_to.name && calendar.isHoliday(date_to))
        ) {
          return `Selected date is a HOLIDAY. Please select another date.`;
        }

        if (!_form.isEmpty(FC.date_from) && !_form.isEmpty(FC.date_to)) {
          if (calendar.isDateSmaller(date_to, date_from)) {
            if (name == FC.date_from.name) {
              return "[Date From] cannot be bigger than [Date To]";
            } else if (name == FC.date_to.name) {
              return "[Date To] cannot be smaller than [Date From]";
            }
          }
        }
        return null;
      },
      // #########################################
      // specific to field
      _dateFrom: function(name) {},
      _dateTo: function(name) {


      },
      _totalDay: function(name) {


        if (!_form.isEmpty(FC.date_from) && !_form.isEmpty(FC.date_to)) {
          let from = _form.get(FC.date_from);
          let to = _form.get(FC.date_to);
          let total = calendar.getTotalWorkingDay(from, to);
          console.log("total", total)
          _form.set(FC.total_days, total);
        } else {
          _form.set(FC.total_days, "");
        }
      }
    }

    let _form = null;
    let parent_id = "holiday-create";
    let FC = {
      holiday_name: {
        name: "holiday_name",
        type: MyFormType.TEXT
      },
      date_from: {
        name: "date_from",
        type: MyFormType.DATE
      },
      date_to: {
        name: "date_to",
        type: MyFormType.DATE
      },
      total_days: {
        name: "total_days",
        type: MyFormType.NUMBER
      },

    }

    _form = new MyForm({
      parent_id: parent_id,
      items: FC,
      onchange: validation.onchange
    });

    _form.required(FC.holiday_name);
    _form.required(FC.date_from);
    _form.required(FC.date_to);

    _form.disabled(FC.total_days);


  }
</script>
@stop
