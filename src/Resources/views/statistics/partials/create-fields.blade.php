{{ Form::bsText('statdate','Service date','Service date',date('Y-m-d',strtotime('last Sunday'))) }}
<div class="form-group">
    <label for="service_id" class="control-label">Service</label>
    <select name="service_id" class="selectize">
      @foreach ($services as $service)
         <option value="{{$service->id}}">{{$service->servicetime}}</option>
      @endforeach
    </select>
</div>
{{ Form::bsText('attendance','Attendance','Attendance') }}
<div class="form-group">
  <label class="control-label">Exclude major festivals</label>
  <div>
    <input type="radio" class="majorservice" name="included" value="1" checked> Included (normal service)
      &nbsp;<input type="radio" class="majorservice" name="included" value="0"> Exclude abnormal service from statistics
  </div>
</div>