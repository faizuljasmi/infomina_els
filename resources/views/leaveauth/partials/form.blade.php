<form method="POST" action="{{$action}}">
                {{ csrf_field() }}
                    <div class="form-row">
                        <!-- AUTH 1 -->
                        <div class="form-group col-md-6">
                            <label for="name">Authority One</label>
                            <select class="form-control" id="authority_1_id" name="authority_1_id">
                            <option value="">NA</option>
                            @foreach($authUsers as $u)
                            <option value="{{$u->id}}" {{isset($empAuth->authority_1_id) && $empAuth->authority_one->name == $u->name ? 'selected':''}} >{{$u->name}}</option>
                            @endforeach
                            </select>
                        </div>
                        <!-- AUTH 2 -->
                        <div class="form-group col-md-6">
                            <label for="name">Authority Two</label>
                            <select class="form-control" id="authority_2_id" name="authority_2_id">
                            <option value="">NA</option>
                            @foreach($authUsers as $u)
                            <option value="{{$u->id}}" {{isset($empAuth->authority_2_id) && $empAuth->authority_two->name == $u->name ? 'selected':''}} >{{$u->name}}</option>
                            @endforeach
                            </select>
                        </div>
                        <!-- AUTH 3 -->
                        <div class="form-group col-md-6">
                            <label for="name">Authority Three</label>
                            <select class="form-control" id="authority_3_id" name="authority_3_id">
                            <option value="">NA</option>
                            @foreach($authUsers as $u)
                            <option value="{{$u->id}}" {{isset($empAuth->authority_3_id) && $empAuth->authority_three->name == $u->name ? 'selected':''}} >{{$u->name}}</option>
                            @endforeach
                            </select>
                        </div>
                        
                    </div>
                <div class="float-sm-right">
                <button type="submit" class="btn btn-success">Submit</button>
                </div>
</form>