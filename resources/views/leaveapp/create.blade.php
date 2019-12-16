@extends('adminlte::page')
@section('content')


              <!-- Approval Authorities -->
              <!-- <div class="card">
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
                        <td>{{isset($leaveAuth->authority_1_id) ? $leaveAuth->authority_one->name:'NA'}}</td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td>{{isset($leaveAuth->authority_2_id) ? $leaveAuth->authority_two->name:'NA'}}</td>
                      </tr>
                      <tr>
                        <td>3</td>
                        <td>{{isset($leaveAuth->authority_3_id) ? $leaveAuth->authority_three->name:'NA'}}</td>
                      </tr>
                    </table>
                  </div>
                </div> -->


<section id="leaveapp-create">
  <h3>Apply Leave</h3>
  <section class="content">
    <div class="container-fluid">

      <div class="row">

        <!-- Left Col -->
        <section class="col-lg-5 connectedSortable ui-sortable">
          <form method="POST" action="/leaveapp/create">
            <!-- Application Form -->
            <div class="card card-primary">
              <div class="card-header bg-teal">
                <strong>Application Form</strong>
              </div>
              <div class="card-body">

                <!-- Leave Type -->
                <div class="form-group">
                  <label>Leave Type</label>
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

                <!-- Leave Variation -->
                <div class="form-group">
                  <label>Apply For</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-clock"></i>
                      </span>
                    </div>
                    <select class="form-control" name="apply_for">
                      <option value="full-day">Full Day</option>
                      <option value="half-day">Half Day</option>
                    </select>
                  </div>
                </div>


                <!-- Date From -->
                <div class="form-group">
                  <label>Date From</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-calendar-day"></i>
                      </span>
                    </div>
                    <input type="date" class="form-control float-right" name="date_from">
                  </div>
                </div>

                <!-- Date From -->
                <div class="form-group">
                  <label>Date To</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-calendar-day"></i>
                      </span>
                    </div>
                    <input type="date" class="form-control float-right" name="date_to">
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
                      <option value="{{$emp->id}}">{{$emp->name}}</option>
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
                <button type="submit" class="btn btn-success float-right">Submit</button>

              </div>
            </div>
          </form>
        </section>

        <!-- Right Col -->
        <section class="col-lg-6 connectedSortable ui-sortable">

          <div class="row">
            <div class="col-lg-6 connectedSortable ui-sortable">
              <!-- Vanilla Calendar -->
              <div class="card">
                <div class="card-header bg-teal">
                  <strong>Calendar</strong>
                </div>
                <div class="myCalendar vanilla-calendar" style="margin: 20px auto"></div>
              </div>
            </div>
            <div class="col-lg-6 connectedSortable ui-sortable">
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
                        <td>Wan Zulsarhan Wan Shaari</td>
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
            </div>
            
            <div class="col-lg-6 connectedSortable ui-sortable">
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
    const isHalfDay = function(){
      return _form.get(FC.apply_for) == "half-day";
    }

    const updateDateResume = function(){
      console.log("updateDateResume")
    }
    
    const updateTotalDay = function(){
      console.log("updateTotalDay")
    }

    let calendar = new VanillaCalendar({
        holiday: [
          "20191204","20191205"
        ],
        selector: ".myCalendar",
        onSelect: (data, elem) => {
            console.log(data, elem)
        }
    });

    let _form = null;
    let parent_id = "leaveapp-create";
    let FC = {
      leave_type_id : {
        name : "leave_type_id",
        type : MyFormType.SELECT,
        onchange: function(v, el, meta){
          console.log(v,el,meta);

          let label = _form.desc(meta);
          _form.set(FC.total_days, 4);
        }
      },
      apply_for : {
        name : "apply_for",
        type : MyFormType.SELECT,
        onchange: function(v, el, meta){
          if(isHalfDay()){
            _form.disabled(FC.date_to);
            _form.copy(FC.date_from, FC.date_to);
            updateTotalDay();
            updateDateResume();
          } else{
            _form.required(FC.date_to);
          }
        }
      },
      date_from : {
        name : "date_from",
        type : MyFormType.DATE,
        onchange: function(v, el, meta){
          if(isHalfDay()){
            _form.copy(FC.date_from, FC.date_to);
          }

          updateTotalDay();
          updateDateResume();
        }
      },
      date_to : {
        name : "date_to",
        type : MyFormType.DATE,
        onchange: function(v, el, meta){
          updateTotalDay();
          updateDateResume();
        }
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
      // ####################################
      // ## data generated from controller ##
      // user_id
      // status 
      // approver_id_1
      // approver_id_2
      // approver_id_3
    }

    _form = new MyForm({parent_id : parent_id, items : FC});

    // set required
    _form.required(FC.leave_type_id);
    _form.required(FC.date_from);
    _form.required(FC.date_to);

    // set disabled
    _form.disabled(FC.date_resume);
    _form.disabled(FC.total_days);
  }

</script>

</section>


@endsection
@section('css')
<link rel="stylesheet" href="{{asset('')}}" @endsection