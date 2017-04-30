<div class="form-group">
  <label for="name">Title</label>
  <input class="form-control" data-slug="source" placeholder="Title" name="title" id="title" type="text" value="{{$book->title}}">
</div>
<div class="form-group">
  <label for="slug">Slug</label>
  <input class="form-control" data-slug="target" placeholder="Slug" name="slug" id="slug" type="text" value="{{$book->slug}}">
</div>
{{ Form::bsText('author','Author','Author',$book->author) }}
<div class='form-group '>
  <label for="supplier_id">Supplier</label>
  <select class="selectize" id="supplier_id" name="supplier_id">
    @foreach ($suppliers as $supplier)
      @if ($book->supplier_id==$supplier->id)
        <option selected value="{{$supplier->id}}">{{$supplier->supplier}}</option>
      @else
        <option value="{{$supplier->id}}">{{$supplier->supplier}}</option>
      @endif
    @endforeach
  </select>
</div>
{{ Form::bsTextarea('description','Description','Description',$book->description) }}
{{ Form::bsHidden('image',$book->image) }}
<div id="thumbdiv" style="margin-bottom:5px;"></div>
<div id="filediv"></div>
<div class='form-group '>
  <label for="tags">Tags</label>
  <select name="tags[]" class="input-tags" multiple>
  @foreach ($tags as $tag)
    @if ((count($btags)) and (in_array($tag->name,$btags)))
        <option selected value="{{$tag->name}}">{{$tag->name}}</option>
    @else
        <option value="{{$tag->name}}">{{$tag->name}}</option>
    @endif
  @endforeach
  </select>
</div>
{{ Form::bsText('stock','Current stock','Current stock',$book->stock) }}
{{ Form::bsText('saleprice','Sale price','Sale price',$book->saleprice) }}
{{ Form::bsText('costprice','Cost price','Cost price',$book->costprice) }}
