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
    <h6 class="float-right">Hello, <strong>{{$user->name}}</strong> <span class="badge badge-info">{{$user->user_type}}</span></h6>
    <h1 class="m-0 text-dark">Dashboard</h1>
    <div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
    </div>
@stop

@section('content')
<section class="content">
      <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Employees</span>
                <span class="info-box-number">
                  112
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-sign-out-alt"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">On Leave</span>
                <span class="info-box-number">12</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clipboard"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Pending Applications</span>
                <span class="info-box-number">{{$leaveApps->count()}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
        </div>
        <!-- /.row -->

        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-teal">
                <h5 class="card-title">Leave Applications</h5>

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
                  <div class="col-md-9">
                    <p class="text-center">
                      <strong>Recent Leave Applications</strong>
                    </p>
                    <table class="table table-striped table-bordered">
                    @if($leaveApps->count() > 0)
                      <thead>
                        <tr>
                          <th scope="col">No.</th>
                          <th scope="col">Applicant</th>
                          <th scope="col">Leave Type</th>
                          <th scope="col">From</th>
                          <th scope="col">To</th>
                          <th scope="col">Duration</th>
                          <th scope="col">Submitted</th>
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
                          <td>{{ \Carbon\Carbon::parse($la->date_from)->isoFormat('ddd, D MMM YY')}}</td>
                          <td>{{ \Carbon\Carbon::parse($la->date_to)->isoFormat('ddd, D MMM YY')}}</td>
                          <td>{{$la->total_days}} day(s)</td>
                          <td>{{ \Carbon\Carbon::parse($la->created_at)->diffForHumans()}}</td>
                          <td>
                          @if(!isset($la->approver_id_2))
                            @if($la->status == 'PENDING_1')
                            <span class="badge badge-pill badge-warning"><i class="far fa-clock"></i></i></span>
                            @endif
                          @elseif(!isset($la->approver_id_3))
                            @if($la->status == 'PENDING_1')
                            <span class="badge badge-pill badge-warning"><i class="far fa-clock"></i></i></span>
                            <span class="badge badge-pill badge-warning"><i class="far fa-clock"></i></i></span>
                            @elseif($la->status == 'PENDING_2')
                            <span class="badge badge-pill badge-success"><i class="far fa-check-circle"></i></span>
                            <span class="badge badge-pill badge-warning"><i class="far fa-clock"></i></i></span>
                            @endif
                          @else
                            @if($la->status == 'PENDING_1')
                              <span class="badge badge-pill badge-warning"><i class="far fa-clock"></i></i></span>
                              <span class="badge badge-pill badge-warning"><i class="far fa-clock"></i></i></span>
                              <span class="badge badge-pill badge-warning"><i class="far fa-clock"></i></i></span>
                              @elseif($la->status == 'PENDING_2')
                              <span class="badge badge-pill badge-success"><i class="far fa-check-circle"></i></span>
                              <span class="badge badge-pill badge-warning"><i class="far fa-clock"></i></i></span>
                              <span class="badge badge-pill badge-warning"><i class="far fa-clock"></i></i></span>
                              @elseif($la->status == 'PENDING_3')
                              <span class="badge badge-pill badge-success"><i class="far fa-check-circle"></i></span>
                              <span class="badge badge-pill badge-success"><i class="far fa-check-circle"></i></span>
                              <span class="badge badge-pill badge-warning"><i class="far fa-clock"></i></i></span>
                            @endif
                          @endif
                        </td>
                          <td>
                            <a href="{{route('view_application', $la->id)}}"><button type="button" class="btn btn-success btn-sm">View</i></button></a>
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
                    {{$leaveApps->links()}}
                    

                  </div>
                  <!-- /.col -->
                  <div class="col-md-3">
                    <p class="text-center">
                      <strong>Leave Calendar</strong>
                    </p>

                    <div class="card bg-gradient-success">
              <div class="card-header border-0 ui-sortable-handle" style="cursor: move;">
              </div>
              <!-- /.card-header -->
              <div class="card-body pt-0">
                <!--The calendar -->
                <div id="calendar" style="width: 100%"><div class="bootstrap-datetimepicker-widget usetwentyfour"><ul class="list-unstyled"><li class="show"><div class="datepicker"><div class="datepicker-days" style=""><table class="table table-sm"><thead><tr><th class="prev" data-action="previous"><span class="fa fa-chevron-left" title="Previous Month"></span></th><th class="picker-switch" data-action="pickerSwitch" colspan="5" title="Select Month">December 2019</th><th class="next" data-action="next"><span class="fa fa-chevron-right" title="Next Month"></span></th></tr><tr><th class="dow">Su</th><th class="dow">Mo</th><th class="dow">Tu</th><th class="dow">We</th><th class="dow">Th</th><th class="dow">Fr</th><th class="dow">Sa</th></tr></thead><tbody><tr><td data-action="selectDay" data-day="12/01/2019" class="day weekend">1</td><td data-action="selectDay" data-day="12/02/2019" class="day">2</td><td data-action="selectDay" data-day="12/03/2019" class="day">3</td><td data-action="selectDay" data-day="12/04/2019" class="day">4</td><td data-action="selectDay" data-day="12/05/2019" class="day">5</td><td data-action="selectDay" data-day="12/06/2019" class="day">6</td><td data-action="selectDay" data-day="12/07/2019" class="day weekend">7</td></tr><tr><td data-action="selectDay" data-day="12/08/2019" class="day weekend">8</td><td data-action="selectDay" data-day="12/09/2019" class="day">9</td><td data-action="selectDay" data-day="12/10/2019" class="day">10</td><td data-action="selectDay" data-day="12/11/2019" class="day active today">11</td><td data-action="selectDay" data-day="12/12/2019" class="day">12</td><td data-action="selectDay" data-day="12/13/2019" class="day">13</td><td data-action="selectDay" data-day="12/14/2019" class="day weekend">14</td></tr><tr><td data-action="selectDay" data-day="12/15/2019" class="day weekend">15</td><td data-action="selectDay" data-day="12/16/2019" class="day">16</td><td data-action="selectDay" data-day="12/17/2019" class="day">17</td><td data-action="selectDay" data-day="12/18/2019" class="day">18</td><td data-action="selectDay" data-day="12/19/2019" class="day">19</td><td data-action="selectDay" data-day="12/20/2019" class="day">20</td><td data-action="selectDay" data-day="12/21/2019" class="day weekend">21</td></tr><tr><td data-action="selectDay" data-day="12/22/2019" class="day weekend">22</td><td data-action="selectDay" data-day="12/23/2019" class="day">23</td><td data-action="selectDay" data-day="12/24/2019" class="day">24</td><td data-action="selectDay" data-day="12/25/2019" class="day">25</td><td data-action="selectDay" data-day="12/26/2019" class="day">26</td><td data-action="selectDay" data-day="12/27/2019" class="day">27</td><td data-action="selectDay" data-day="12/28/2019" class="day weekend">28</td></tr><tr><td data-action="selectDay" data-day="12/29/2019" class="day weekend">29</td><td data-action="selectDay" data-day="12/30/2019" class="day">30</td><td data-action="selectDay" data-day="12/31/2019" class="day">31</td><td data-action="selectDay" data-day="01/01/2020" class="day new">1</td><td data-action="selectDay" data-day="01/02/2020" class="day new">2</td><td data-action="selectDay" data-day="01/03/2020" class="day new">3</td><td data-action="selectDay" data-day="01/04/2020" class="day new weekend">4</td></tr><tr><td data-action="selectDay" data-day="01/05/2020" class="day new weekend">5</td><td data-action="selectDay" data-day="01/06/2020" class="day new">6</td><td data-action="selectDay" data-day="01/07/2020" class="day new">7</td><td data-action="selectDay" data-day="01/08/2020" class="day new">8</td><td data-action="selectDay" data-day="01/09/2020" class="day new">9</td><td data-action="selectDay" data-day="01/10/2020" class="day new">10</td><td data-action="selectDay" data-day="01/11/2020" class="day new weekend">11</td></tr></tbody></table></div><div class="datepicker-months" style="display: none;"><table class="table-condensed"><thead><tr><th class="prev" data-action="previous"><span class="fa fa-chevron-left" title="Previous Year"></span></th><th class="picker-switch" data-action="pickerSwitch" colspan="5" title="Select Year">2019</th><th class="next" data-action="next"><span class="fa fa-chevron-right" title="Next Year"></span></th></tr></thead><tbody><tr><td colspan="7"><span data-action="selectMonth" class="month">Jan</span><span data-action="selectMonth" class="month">Feb</span><span data-action="selectMonth" class="month">Mar</span><span data-action="selectMonth" class="month">Apr</span><span data-action="selectMonth" class="month">May</span><span data-action="selectMonth" class="month">Jun</span><span data-action="selectMonth" class="month">Jul</span><span data-action="selectMonth" class="month">Aug</span><span data-action="selectMonth" class="month">Sep</span><span data-action="selectMonth" class="month">Oct</span><span data-action="selectMonth" class="month">Nov</span><span data-action="selectMonth" class="month active">Dec</span></td></tr></tbody></table></div><div class="datepicker-years" style="display: none;"><table class="table-condensed"><thead><tr><th class="prev" data-action="previous"><span class="fa fa-chevron-left" title="Previous Decade"></span></th><th class="picker-switch" data-action="pickerSwitch" colspan="5" title="Select Decade">2010-2019</th><th class="next" data-action="next"><span class="fa fa-chevron-right" title="Next Decade"></span></th></tr></thead><tbody><tr><td colspan="7"><span data-action="selectYear" class="year old">2009</span><span data-action="selectYear" class="year">2010</span><span data-action="selectYear" class="year">2011</span><span data-action="selectYear" class="year">2012</span><span data-action="selectYear" class="year">2013</span><span data-action="selectYear" class="year">2014</span><span data-action="selectYear" class="year">2015</span><span data-action="selectYear" class="year">2016</span><span data-action="selectYear" class="year">2017</span><span data-action="selectYear" class="year">2018</span><span data-action="selectYear" class="year active">2019</span><span data-action="selectYear" class="year old">2020</span></td></tr></tbody></table></div><div class="datepicker-decades" style="display: none;"><table class="table-condensed"><thead><tr><th class="prev" data-action="previous"><span class="fa fa-chevron-left" title="Previous Century"></span></th><th class="picker-switch" data-action="pickerSwitch" colspan="5">2000-2090</th><th class="next" data-action="next"><span class="fa fa-chevron-right" title="Next Century"></span></th></tr></thead><tbody><tr><td colspan="7"><span data-action="selectDecade" class="decade old" data-selection="2006">1990</span><span data-action="selectDecade" class="decade" data-selection="2006">2000</span><span data-action="selectDecade" class="decade active" data-selection="2016">2010</span><span data-action="selectDecade" class="decade" data-selection="2026">2020</span><span data-action="selectDecade" class="decade" data-selection="2036">2030</span><span data-action="selectDecade" class="decade" data-selection="2046">2040</span><span data-action="selectDecade" class="decade" data-selection="2056">2050</span><span data-action="selectDecade" class="decade" data-selection="2066">2060</span><span data-action="selectDecade" class="decade" data-selection="2076">2070</span><span data-action="selectDecade" class="decade" data-selection="2086">2080</span><span data-action="selectDecade" class="decade" data-selection="2096">2090</span><span data-action="selectDecade" class="decade old" data-selection="2106">2100</span></td></tr></tbody></table></div></div></li><li class="picker-switch accordion-toggle"></li></ul></div></div>
              </div>
              <!-- /.card-body -->
            </div>
                    
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
                <h3 class="card-title">Leave Applications History</h3>

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
              <div class="card-body p-0">
                <div class="d-md-flex">
                  <div class="p-1 flex-fill" style="overflow: hidden">
                  <table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">No.</th>
      <th scope="col">Submitted by</th>
      <th scope="col">Leave Type</th>
      <th scope="col">Duration</th>
      <th scope="col">From</th>
      <th scope="col">To</th>
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
        <button type="button" class="btn btn-success btn-sm">View</i></button>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
{{$leaveHist->links()}}
                  </div>
                  
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->


           
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!--/. container-fluid -->
    </section>
@stop
