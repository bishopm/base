{{ Form::bsText('firstname','First name','First name',$preacher->firstname) }}
{{ Form::bsText('surname','Surname','Surname',$preacher->surname) }}
{{ Form::bsSelect('title','Title',array('Mr','Mrs','Ms','Dr','Rev'),$preacher->title) }}
{{ Form::bsSelect('status','Status',array('Local preacher','On trial preacher','Guest','Minister','Superintendent','Emeritus preacher',$preacher->status)) }}
{{ Form::bsText('phone','Cellphone','Cellphone',$preacher->phone) }}
<div class="form-group">
    <label for="individual_id" class="control-label">Link to {{$setting['site_abbreviation']}} Member</label>
    <select name="individual_id" class="selectize">
      <option value="0"></option>
      @foreach ($individuals as $indiv)
        @if ($preacher->individual_id==$indiv->id)
            <option selected value="{{$indiv->id}}">{{$indiv->firstname}} {{$indiv->surname}}</option>
        @else
            <option value="{{$indiv->id}}">{{$indiv->firstname}} {{$indiv->surname}}</option>
        @endif
      @endforeach
    </select>
</div>
<div class="form-group">
    <label for="society_id" class="control-label">Society</label>
    <select name="society_id" class="selectize">
      <option></option>
      @foreach ($societies as $society)
        @if ($preacher->society_id==$society->id)
          <option selected value="{{$society->id}}">{{$society->society}}</option>
        @else
          <option value="{{$society->id}}">{{$society->society}}</option>
        @endif
      @endforeach
    </select>
</div>
{{ Form::bsFile('image') }}