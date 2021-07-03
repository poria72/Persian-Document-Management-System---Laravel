<div class="form-group col-sm-12 {{ $errors->has($name) ? 'has-error' :'' }}">
    {!! Form::label($name, empty($label)?ucfirst($name).':':$label.":") !!}
    {!! Form::textarea($name, $value, array_merge(['class' => 'form-control'],$attributes)) !!}
    {!! $errors->first($name,'<span class="help-block">:message</span>') !!}
</div>
