{{ Form::bsText('firstname','First name','First name') }}
{{ Form::bsText('surname','Surname','Surname') }}
{{ Form::bsSelect('title','Title',array('Mr','Mrs','Ms','Dr','Rev')) }}
{{ Form::bsText('phone','Cellphone','Cellphone') }}
<div class="form-group">
    <label for="individual_id" class="control-label">Link to {{$setting['site_abbreviation']}} Member</label>
    <select name="individual_id" class="selectize">
      <option value="0"></option>
      @foreach ($individuals as $indiv)
         <option value="{{$indiv->id}}">{{$indiv->firstname}} {{$indiv->surname}}</option>
      @endforeach
    </select>
</div>
<div class="form-group">
    <label for="society_id" class="control-label">Society</label>
    <select name="society_id" class="selectize">
      <option></option>
      @foreach ($societies as $society)
         <option value="{{$society->id}}">{{$society->society}}</option>
      @endforeach
    </select>
</div>
<label for="positions" class="control-label">Role/s</label>
<select multiple class="selectize" name="positions[]">
  @foreach ($positions as $position)
    <option value="{{$position->id}}">{{$position->position}}</option>
  @endforeach
</select>
{{ Form::bsFile('image') }}
{{ Form::bsHidden('circuit_id',$circuit) }}