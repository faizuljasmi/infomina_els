@extends('adminlte::page')
@section('content')

<section id="leaveapp-create">
  <h3>Apply Leave</h3>
  <section class="content">
    <div class="container-fluid">

      <div class="row">

        <!-- Left Col -->
        <section class="col-lg-6 connectedSortable ui-sortable">

          <!-- Application Form -->
          <div class="card card-primary">
            <div class="card-header bg-teal">
              <strong>Application Form</strong>
            </div>
            <div class="card-body">

              <!-- Leave Type -->
              <div class="form-group">
                <label>Leave Type:</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="far fa-star"></i>
                    </span>
                  </div>
                  <select class="form-control" name="leave_type_id">
                    <option value="">Choose Leave</option>
                    @foreach($leaveType as $lt)
                    <option value="{{$lt->id}}">{{$lt->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <!-- Date range -->
              <div class="form-group">
                <label>Date Range</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="fa fa-calendar-day"></i>
                    </span>
                  </div>
                  <input type="text" class="form-control float-right" name="date_from_to">
                </div>
              </div>

              <!-- Total Days -->
              <div class="form-group">
                <label>Total Days</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="far fa-calendar-check"></i>
                    </span>
                  </div>
                  <input type="number" class="form-control float-right" name="total_days">
                </div>
              </div>

              <!-- Date Resume -->
              <div class="form-group">
                <label>Date Resume</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="far fa-calendar-alt"></i>
                    </span>
                  </div>
                  <input type="text" class="form-control float-right" name="date_resume">
                </div>
              </div>


              <!-- Reason -->
              <div class="form-group">
                <label>Reason</label>
                <textarea class="form-control" rows="5" name="reason"></textarea>
              </div>

              <!-- File Attachment -->
              <div class="form-group">
                <label>Attachment</label>
                <div class="input-group">
                  <input type="file" class="form-control-file" id="exampleFormControlFile1">
                </div>
              </div>

              <!-- Relief Personel -->
              <div class="form-group">
                <label>Relief Personel</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="fa fa-user"></i>
                    </span>
                  </div>
                  <select class="form-control" id="exampleFormControlSelect1">
                    <option selected>Choose Person</option>
                    @foreach($groupMates as $emp)
                    <option>{{$emp->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>


              <!-- Emergency Contant -->
              <div class="form-group">
                <label>Emergency Contant</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="fa fa-phone"></i>
                    </span>
                  </div>
                  <input type="text" class="form-control float-right" name="emergency_contact">
                </div>
              </div>

              <!-- Submit Button -->
              <button type="button" class="btn btn-success float-right">Submit</button>

            </div>
          </div>

        </section>

        <!-- Right Col -->
        <section class="col-lg-5 connectedSortable ui-sortable">

          <!-- Approval Authorities -->
          <div class="card">
            <div class="card-header bg-teal">
              <strong>Approval Authorities</strong>
            </div>
            <div id="collapse-leave" class="collapse show" aria-labelledby="heading-leave">
              <div class="card-body">
                <table class="table table-bordered">
                  <tr>
                    <th>Level</th>
                    <th>Name</th>
                  </tr>
                  <tr>
                    <td>1</td>
                    <td>Authority 1</td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>Authority 2</td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td>Authority 3</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          <!-- Calendar -->
          {{-- <div class="card bg-gradient-success">
            <div class="card-header border-0 ui-sortable-handle" style="cursor: move;">

              <h3 class="card-title">
                <i class="far fa-calendar-alt"></i>
                Calendar
              </h3>
              <!-- tools card -->
              <div class="card-tools">
                <!-- button with a dropdown -->
                <div class="btn-group">
                  <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-bars"></i></button>
                  <div class="dropdown-menu float-right" role="menu">
                    <a href="#" class="dropdown-item">Add new event</a>
                    <a href="#" class="dropdown-item">Clear events</a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">View calendar</a>
                  </div>
                </div>
                <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>

            <!--The calendar -->
            <div class="card-body pt-0">
              <div id="calendar" style="width: 100%">
                <div class="bootstrap-datetimepicker-widget usetwentyfour">
                  <ul class="list-unstyled">
                    <li class="show">
                      <div class="datepicker">
                        <div class="datepicker-days" style="">
                          <table class="table table-sm">
                            <thead>
                              <tr>
                                <th class="prev" data-action="previous"><span class="fa fa-chevron-left"
                                    title="Previous Month"></span></th>
                                <th class="picker-switch" data-action="pickerSwitch" colspan="5" title="Select Month">
                                  December 2019</th>
                                <th class="next" data-action="next"><span class="fa fa-chevron-right"
                                    title="Next Month"></span></th>
                              </tr>
                              <tr>
                                <th class="dow">Su</th>
                                <th class="dow">Mo</th>
                                <th class="dow">Tu</th>
                                <th class="dow">We</th>
                                <th class="dow">Th</th>
                                <th class="dow">Fr</th>
                                <th class="dow">Sa</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td data-action="selectDay" data-day="12/01/2019" class="day weekend">1</td>
                                <td data-action="selectDay" data-day="12/02/2019" class="day">2</td>
                                <td data-action="selectDay" data-day="12/03/2019" class="day">3</td>
                                <td data-action="selectDay" data-day="12/04/2019" class="day">4</td>
                                <td data-action="selectDay" data-day="12/05/2019" class="day">5</td>
                                <td data-action="selectDay" data-day="12/06/2019" class="day">6</td>
                                <td data-action="selectDay" data-day="12/07/2019" class="day weekend">7</td>
                              </tr>
                              <tr>
                                <td data-action="selectDay" data-day="12/08/2019" class="day weekend">8</td>
                                <td data-action="selectDay" data-day="12/09/2019" class="day">9</td>
                                <td data-action="selectDay" data-day="12/10/2019" class="day">10</td>
                                <td data-action="selectDay" data-day="12/11/2019" class="day active today">11</td>
                                <td data-action="selectDay" data-day="12/12/2019" class="day">12</td>
                                <td data-action="selectDay" data-day="12/13/2019" class="day">13</td>
                                <td data-action="selectDay" data-day="12/14/2019" class="day weekend">14</td>
                              </tr>
                              <tr>
                                <td data-action="selectDay" data-day="12/15/2019" class="day weekend">15</td>
                                <td data-action="selectDay" data-day="12/16/2019" class="day">16</td>
                                <td data-action="selectDay" data-day="12/17/2019" class="day">17</td>
                                <td data-action="selectDay" data-day="12/18/2019" class="day">18</td>
                                <td data-action="selectDay" data-day="12/19/2019" class="day">19</td>
                                <td data-action="selectDay" data-day="12/20/2019" class="day">20</td>
                                <td data-action="selectDay" data-day="12/21/2019" class="day weekend">21</td>
                              </tr>
                              <tr>
                                <td data-action="selectDay" data-day="12/22/2019" class="day weekend">22</td>
                                <td data-action="selectDay" data-day="12/23/2019" class="day">23</td>
                                <td data-action="selectDay" data-day="12/24/2019" class="day">24</td>
                                <td data-action="selectDay" data-day="12/25/2019" class="day">25</td>
                                <td data-action="selectDay" data-day="12/26/2019" class="day">26</td>
                                <td data-action="selectDay" data-day="12/27/2019" class="day">27</td>
                                <td data-action="selectDay" data-day="12/28/2019" class="day weekend">28</td>
                              </tr>
                              <tr>
                                <td data-action="selectDay" data-day="12/29/2019" class="day weekend">29</td>
                                <td data-action="selectDay" data-day="12/30/2019" class="day">30</td>
                                <td data-action="selectDay" data-day="12/31/2019" class="day">31</td>
                                <td data-action="selectDay" data-day="01/01/2020" class="day new">1</td>
                                <td data-action="selectDay" data-day="01/02/2020" class="day new">2</td>
                                <td data-action="selectDay" data-day="01/03/2020" class="day new">3</td>
                                <td data-action="selectDay" data-day="01/04/2020" class="day new weekend">4</td>
                              </tr>
                              <tr>
                                <td data-action="selectDay" data-day="01/05/2020" class="day new weekend">5</td>
                                <td data-action="selectDay" data-day="01/06/2020" class="day new">6</td>
                                <td data-action="selectDay" data-day="01/07/2020" class="day new">7</td>
                                <td data-action="selectDay" data-day="01/08/2020" class="day new">8</td>
                                <td data-action="selectDay" data-day="01/09/2020" class="day new">9</td>
                                <td data-action="selectDay" data-day="01/10/2020" class="day new">10</td>
                                <td data-action="selectDay" data-day="01/11/2020" class="day new weekend">11</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="datepicker-months" style="display: none;">
                          <table class="table-condensed">
                            <thead>
                              <tr>
                                <th class="prev" data-action="previous"><span class="fa fa-chevron-left"
                                    title="Previous Year"></span></th>
                                <th class="picker-switch" data-action="pickerSwitch" colspan="5" title="Select Year">
                                  2019</th>
                                <th class="next" data-action="next"><span class="fa fa-chevron-right"
                                    title="Next Year"></span></th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td colspan="7"><span data-action="selectMonth" class="month">Jan</span><span
                                    data-action="selectMonth" class="month">Feb</span><span data-action="selectMonth"
                                    class="month">Mar</span><span data-action="selectMonth"
                                    class="month">Apr</span><span data-action="selectMonth"
                                    class="month">May</span><span data-action="selectMonth"
                                    class="month">Jun</span><span data-action="selectMonth"
                                    class="month">Jul</span><span data-action="selectMonth"
                                    class="month">Aug</span><span data-action="selectMonth"
                                    class="month">Sep</span><span data-action="selectMonth"
                                    class="month">Oct</span><span data-action="selectMonth"
                                    class="month">Nov</span><span data-action="selectMonth"
                                    class="month active">Dec</span></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="datepicker-years" style="display: none;">
                          <table class="table-condensed">
                            <thead>
                              <tr>
                                <th class="prev" data-action="previous"><span class="fa fa-chevron-left"
                                    title="Previous Decade"></span></th>
                                <th class="picker-switch" data-action="pickerSwitch" colspan="5" title="Select Decade">
                                  2010-2019</th>
                                <th class="next" data-action="next"><span class="fa fa-chevron-right"
                                    title="Next Decade"></span></th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td colspan="7"><span data-action="selectYear" class="year old">2009</span><span
                                    data-action="selectYear" class="year">2010</span><span data-action="selectYear"
                                    class="year">2011</span><span data-action="selectYear" class="year">2012</span><span
                                    data-action="selectYear" class="year">2013</span><span data-action="selectYear"
                                    class="year">2014</span><span data-action="selectYear" class="year">2015</span><span
                                    data-action="selectYear" class="year">2016</span><span data-action="selectYear"
                                    class="year">2017</span><span data-action="selectYear" class="year">2018</span><span
                                    data-action="selectYear" class="year active">2019</span><span
                                    data-action="selectYear" class="year old">2020</span></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="datepicker-decades" style="display: none;">
                          <table class="table-condensed">
                            <thead>
                              <tr>
                                <th class="prev" data-action="previous"><span class="fa fa-chevron-left"
                                    title="Previous Century"></span></th>
                                <th class="picker-switch" data-action="pickerSwitch" colspan="5">2000-2090</th>
                                <th class="next" data-action="next"><span class="fa fa-chevron-right"
                                    title="Next Century"></span></th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td colspan="7"><span data-action="selectDecade" class="decade old"
                                    data-selection="2006">1990</span><span data-action="selectDecade" class="decade"
                                    data-selection="2006">2000</span><span data-action="selectDecade"
                                    class="decade active" data-selection="2016">2010</span><span
                                    data-action="selectDecade" class="decade" data-selection="2026">2020</span><span
                                    data-action="selectDecade" class="decade" data-selection="2036">2030</span><span
                                    data-action="selectDecade" class="decade" data-selection="2046">2040</span><span
                                    data-action="selectDecade" class="decade" data-selection="2056">2050</span><span
                                    data-action="selectDecade" class="decade" data-selection="2066">2060</span><span
                                    data-action="selectDecade" class="decade" data-selection="2076">2070</span><span
                                    data-action="selectDecade" class="decade" data-selection="2086">2080</span><span
                                    data-action="selectDecade" class="decade" data-selection="2096">2090</span><span
                                    data-action="selectDecade" class="decade old" data-selection="2106">2100</span></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </li>
                    <li class="picker-switch accordion-toggle"></li>
                  </ul>
                </div>
              </div>
            </div>



          </div> --}}

          <!-- Leaves Balance -->
          <div class="card">
            <div class="card-header bg-teal">
              <strong>Leaves Balance</strong>
            </div>
            <div id="collapse-leave" class="collapse show" aria-labelledby="heading-leave">
              <div class="card-body">
                <table class="table table-bordered">
                  @foreach($leaveType as $lt)
                  <tr>
                    <th>{{$lt->name}}</th>
                    <td>-</td>
                  </tr>
                  @endforeach
                </table>
              </div>
            </div>
          </div>

        </section>

        <!-- Empty Col -->
        <section class="col-lg-1 connectedSortable ui-sortable">
        </section>

      </div>

    </div>
  </section>

  <script>
    $(document).ready(MainLeaveApplicationCreate);

  function MainLeaveApplicationCreate() {
    let parent_id = "leaveapp-create";
    let items = {
      leave_type_id : {
        name : "leave_type_id",
        type : MyFormType.SELECT
      },
      date_from_to : {
        name : "date_from_to",
        type : MyFormType.DATE_RANGE
      },
      total_days : {
        name : "total_days",
        type : MyFormType.NUMBER
      },
      date_resume : {
        name : "date_resume",
        type : MyFormType.DATE
      },
      reason : {
        name : "reason",
        type : MyFormType.TEXTAREA
      },
      attachment : {
        name : "attachment",
        type : MyFormType.FILE
      },
      relief_personnel_id : {
        name : "relief_personnel_id",
        type : MyFormType.SELECT
      },
      emergency_contact  : {
        name : "emergency_contact",
        type : MyFormType.TEXT
      },

      // #####################################
      // ## data need to create by frontend ##
      // date_from
      // date_to

      // ####################################
      // ## data generated from controller ##
      // user_id
      // status 
      // approver_id_1
      // approver_id_2
      // approver_id_3
    }

    console.log("items",items)
    let laForm = new MyForm({parent_id : parent_id, items : items});
  }

  </script>

</section>


@endsection
@section('css')
<link rel="stylesheet" href="{{asset('')}}" @endsection