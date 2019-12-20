<form method="POST" action="{{$action}}">
                {{ csrf_field() }}
                    <div class="form-row">
                        <!-- Leave Earnings -->
                        @foreach($leaveEnt as $le)
                            @foreach($leave as $l)
                            @if($le->leave_type_id == $l->leave_type_id)
                            <div class="form-group col-md-6">
                            <label for="{{$le->leave_type->name}}">{{$le->leave_type->name}}</label>
                            <input class="form-control" type = "number" name="leave_{{$le->leave_type_id}}" value="{{isset($l->no_of_days) ? $l->no_of_days: '0'}}" />
                            </div>
                            @endif
                            @endforeach
                        @endforeach
                    </div>
                <div class="float-sm-right">
                <button type="submit" class="btn btn-success">Submit</button>
                </div>
</form>