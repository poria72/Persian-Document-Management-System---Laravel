<link rel="stylesheet" href="{{asset('css/lte/jquery.Bootstrap-PersianDateTimePicker.css')}}">
@section('css')
<style>
    .pouriaStyle p h1
    {
        direction: rtl !important;
    }

    .erja{
        direction: rtl !important;
        float: right !important;
    }

    .erja p
    {
        font-size: 18px;
        font-weight: bold;
        text-align: center
    }

    label
    {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 5px;
        font-weight: 700;
        float: right;
        direction: rtl;
    }
</style>
@stop

<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{asset('js/bootstrap.min.js')}}"></script>

<script>
// Return an array of the selected opion values
// select is an HTML select element
    function getSelectValues(select) {
    var result = [];
    var options = select && select.options;
    var opt;

    for (var i=0, iLen=options.length; i<iLen; i++) {
        opt = options[i];

        if (opt.selected) {
        result.push(opt.value || opt.text);
        }
    }
    return result;
    }
</script>

<!-- Name Field -->
{!! Form::bsText('name',null,[], 'نام پرونده') !!}
{{--if in edit mode--}}

@if ($document)
    @if (auth()->user()->can('update document '.$document->id) && !auth()->user()->is_super_admin)
        @foreach($document->tags->pluck('id')->toArray() as $tagId)
            <input type="hidden" name="tags[]" value="{{$tagId}}">
        @endforeach
    @else

        <div class="form-group col-sm-6 ">
            <label for="tags[]">{{ucfirst(config('settings.tags_label_plural'))}}</label>
            <select class="form-control select2" id="tags"
                    name="tags[]"
                    multiple
                    pattern = "">
                    @foreach($tags as $tag)
                        @canany (['update documents','update documents in tag '.$tag->id])
                            <option
                                value="{{$tag->id}}" {{(in_array($tag->id,old('tags', optional(optional(optional($document)->tags)->pluck('id'))->toArray() ?? [] )))?"selected":"" }}>{{$tag->name}}</option>
                        @endcanany
                    @endforeach
            </select>
        </div>
    @endif
@else
    <div class="form-group col-sm-6 {{ $errors->has("tags") ? 'has-error' :'' }}">
        <label for="tags[]">{{ucfirst(config('settings.tags_label_plural'))}}</label>
        <select class="form-control select2" id="tags" name="tags[]" multiple>
            @foreach($tags as $tag)
                @canany (['create documents','create documents in tag '.$tag->id])
                    <option
                        value="{{$tag->id}}" {{(in_array($tag->id,old('tags', optional(optional(optional($document)->tags)->pluck('id'))->toArray() ?? [] )))?"selected":"" }}>{{$tag->name}}</option>
                @endcanany
            @endforeach
        </select>
        {!! $errors->first("tags",'<span class="help-block">:message</span>') !!}
    </div>
@endif

{{-- <label for="description">توضیحات</label> --}}
{{-- <textarea name="description" value=null class="pouriaStyle form-control b-wysihtml5-editor"></textarea> --}}
{!! Form::bsTextarea('description',null,['class'=>'pouriaStyle form-control b-wysihtml5-editor'],'توضیحات') !!}

{{--additional Attributes--}}

<?php

$kolli = array();
$fardi = array();
$shoghli = array();
$tahsili = array();
$karshenas = (object) array();
$erja2 = array();
$erja3 = array();
$erja4 = array();
foreach ($customFields as $customField) {
    switch ($customField->name) {
        case "نام کارشناس":
            $karshenas = $customField;
            break;

        case "نام و نام خانوادگی":
        case "نام پدر":
        case "کد ملی":
        case "تلفن همراه":
            array_push($fardi,$customField);
            break;

        case "کد پرسنلی":
        case "تلفن مستقیم":
        case "سمت سازمانی":
        case "واحد محل خدمت":
        case "نوع استخدام":
        case "سنوات خدمت":
        case "نام مدیر مستقیم":
            array_push($shoghli,$customField);
            break;

        case "آخرین مقطع تحصیلی":
        case "نام دانشگاه":
        case "رشته تحصیلی":
        array_push($tahsili,$customField);
        break;

        //erja2
        case "حوزه ارجاع دوم":
        case "شماره نامه ارجاع دوم":
        case "تاریخ نامه ارجاع دوم":
        case "شماره ثبت داخلی دبیرخانه دوم":
        case "تاریخ ثبت داخلی دبیرخانه دوم":
        case "نوع درخواست ارجاع دوم":
        case "نحوه درخواست دوم":
        case "اقدامات انجام شده دوم":
        array_push($erja2,$customField);
        break;

        //erja3
        case "حوزه ارجاع سوم":
        case "شماره نامه ارجاع سوم":
        case "تاریخ نامه ارجاع سوم":
        case "شماره ثبت داخلی دبیرخانه سوم":
        case "تاریخ ثبت داخلی دبیرخانه سوم":
        case "نوع درخواست ارجاع سوم":
        case "نحوه درخواست سوم":
        case "اقدامات انجام شده سوم":
        array_push($erja3,$customField);
        break;

        //erja4
        case "حوزه ارجاع چهارم":
        case "شماره نامه ارجاع چهارم":
        case "تاریخ نامه ارجاع چهارم":
        case "شماره ثبت داخلی دبیرخانه چهارم":
        case "تاریخ ثبت داخلی دبیرخانه چهارم":
        case "نوع درخواست ارجاع چهارم":
        case "نحوه درخواست چهارم":
        case "اقدامات انجام شده چهارم":
        array_push($erja4,$customField);
        break;

        default:
            array_push($kolli,$customField);
            break;
    }
}
?>

@foreach ($kolli as $item)
    <div class="form-group col-sm-6 {{ $errors->has("custom_fields.$item->name") ? 'has-error' :'' }}">
        {!! Form::label("custom_fields[$item->name]", Str::title(str_replace('_',' ',$item->name)).":") !!}
        <?php $current = $document!=null ? $document->custom_fields[$item->name]?? '' : null; ?>
        @if (in_array($item->name, array("محل اشتغال","حوزه ارجاع","نحوه درخواست","طبقه بندی","نوع درخواست ارجاع")))
            {!! Form::select("custom_fields[$item->name]",array_combine($item->suggestions,$item->suggestions), $current ?? '',['class' => 'form-control typeahead']) !!}
        @elseif (in_array($item->name, array("اقدامات انجام شده")))
            {!! Form::textarea("custom_fields[$item->name]",null,['class' => 'form-control typeahead']) !!}
        @elseif (in_array($item->name, array("تاریخ نامه ارجاع","تاریخ ثبت داخلی دبیرخانه")))
            {!! Form::text("custom_fields[$item->name]", $current ?? '', ['class' => 'date form-control typeahead','id'=>'exampleInput3','data-MdDateTimePicker'=>'true', 'data-placement'=>'left']) !!}

        @else
            {!! Form::text("custom_fields[$item->name]", null, ['class' => 'form-control typeahead','data-source'=>json_encode($item->suggestions),'autocomplete'=>is_array($item->suggestions)?'off':'on']) !!}
        @endif
        {!! $errors->first("custom_fields.$item->name",'<span class="help-block">:message</span>') !!}
    </div>
@endforeach

<div class="form-group col-sm-6 {{ $errors->has("custom_fields.$karshenas->name") ? 'has-error' :'' }}">
    {!! Form::label("custom_fields[$karshenas->name]", Str::title(str_replace('_',' ',$karshenas->name)).":") !!}
    <?php
    $test = App\User::get('name')->toArray();
    $arr = array();
        foreach ($test as $key => $value) {
            $arr[$value["name"]] = $value["name"];
        }
        $currentMotavali = $document!=null ? $document->custom_fields["نام کارشناس"] : null;
    ?>
    {!! Form::select("custom_fields[$karshenas->name]",($arr),$currentMotavali,['class' => 'form-control typeahead']) !!}
    {!! $errors->first("custom_fields.$karshenas->name",'<span class="help-block">:message</span>') !!}
</div>

<div class="form-group col-sm-12">
    <p>
        <a class="btn btn-primary" data-toggle="collapse" href="#extra" role="button" aria-expanded="false" aria-controls="extra">
            اطلاعات فردی، شغلی، تحصیلی
        </a>
        <a class="btn btn-primary" data-toggle="collapse" href="#erjaat" role="button" aria-expanded="false" aria-controls="erjaat">
            ارجاعات
        </a>
    </p>
</div>

<div class="collapse" id="erjaat">
    <div class="card card-body">
        <div class="form-group col-sm-4 erja">
            <div id="erjaatClose1" class="alert alert-info" role="alert"><p>ارجاع دوم</p></div>
            @foreach ($erja2 as $item)
                <div class="erja form-group col-sm-12 {{ $errors->has("custom_fields.$item->name") ? 'has-error' :'' }}">
                    {!! Form::label("custom_fields[$item->name]", Str::title(str_replace('_',' ',$item->name)).":") !!}
                    <?php $current = $document!=null ? $document->custom_fields[$item->name] ?? '': null; ?>
                    @if (in_array($item->name, array("نحوه درخواست دوم","طبقه بندی دوم","نوع درخواست ارجاع دوم","حوزه ارجاع دوم")))
                        {!! Form::select("custom_fields[$item->name]",array_combine($item->suggestions,$item->suggestions), $current ?? '',['class' => 'form-control typeahead']) !!}
                    @elseif (in_array($item->name, array("اقدامات انجام شده دوم")))
                        {!! Form::textarea("custom_fields[$item->name]",null,['class' => 'form-control typeahead']) !!}
                    @elseif (in_array($item->name, array("تاریخ نامه ارجاع دوم","تاریخ ثبت داخلی دبیرخانه دوم")))
                        {!! Form::text("custom_fields[$item->name]", $current ?? '', ['class' => 'date form-control typeahead','id'=>'exampleInput3','data-MdDateTimePicker'=>'true', 'data-placement'=>'left']) !!}
                    @else
                        {!! Form::text("custom_fields[$item->name]", null, ['class' => 'form-control typeahead','data-source'=>json_encode($item->suggestions),'autocomplete'=>is_array($item->suggestions)?'off':'on']) !!}
                    @endif
                    {!! $errors->first("custom_fields.$item->name",'<span class="help-block">:message</span>') !!}
                </div>
            @endforeach
        </div>
        <div class="form-group col-sm-4 erja">
            <div id="erjaatClose2" class="alert alert-info" role="alert"><p>ارجاع سوم</p></div>
            @foreach ($erja3 as $item)
                <div class="erja form-group col-sm-12 {{ $errors->has("custom_fields.$item->name") ? 'has-error' :'' }}">
                    {!! Form::label("custom_fields[$item->name]", Str::title(str_replace('_',' ',$item->name)).":") !!}
                    <?php $current = $document!=null ? $document->custom_fields[$item->name] ?? '': null; ?>
                    @if (in_array($item->name, array("نحوه درخواست سوم","طبقه بندی سوم","نوع درخواست ارجاع سوم","حوزه ارجاع سوم")))
                        {!! Form::select("custom_fields[$item->name]",array_combine($item->suggestions,$item->suggestions), $current ?? '',['class' => 'form-control typeahead']) !!}
                    @elseif (in_array($item->name, array("اقدامات انجام شده سوم")))
                        {!! Form::textarea("custom_fields[$item->name]",null,['class' => 'form-control typeahead']) !!}
                    @elseif (in_array($item->name, array("تاریخ نامه ارجاع سوم","تاریخ ثبت داخلی دبیرخانه سوم")))
                        {!! Form::text("custom_fields[$item->name]", $current ?? '', ['class' => 'date form-control typeahead','id'=>'exampleInput3','data-MdDateTimePicker'=>'true', 'data-placement'=>'left']) !!}
                    @else
                        {!! Form::text("custom_fields[$item->name]", null, ['class' => 'form-control typeahead','data-source'=>json_encode($item->suggestions),'autocomplete'=>is_array($item->suggestions)?'off':'on']) !!}
                    @endif
                    {!! $errors->first("custom_fields.$item->name",'<span class="help-block">:message</span>') !!}
                </div>
            @endforeach
        </div>
        <div class="form-group col-sm-4 erja">
            <div id="erjaatClose3" class="alert alert-info" role="alert"><p>ارجاع چهارم</p></div>
            @foreach ($erja4 as $item)
                <div class="erja form-group col-sm-12 {{ $errors->has("custom_fields.$item->name") ? 'has-error' :'' }}">
                    {!! Form::label("custom_fields[$item->name]", Str::title(str_replace('_',' ',$item->name)).":") !!}
                    <?php $current = $document!=null ? $document->custom_fields[$item->name] ?? '': null; ?>
                    @if (in_array($item->name, array("نحوه درخواست چهارم","طبقه بندی چهارم","نوع درخواست ارجاع چهارم","حوزه ارجاع چهارم")))
                        {!! Form::select("custom_fields[$item->name]",array_combine($item->suggestions,$item->suggestions), $current ?? '',['class' => 'form-control typeahead']) !!}
                    @elseif (in_array($item->name, array("اقدامات انجام شده چهارم")))
                        {!! Form::textarea("custom_fields[$item->name]",null,['class' => 'form-control typeahead']) !!}
                    @elseif (in_array($item->name, array("تاریخ نامه ارجاع چهارم","تاریخ ثبت داخلی دبیرخانه چهارم")))
                        {!! Form::text("custom_fields[$item->name]", $current ?? '', ['class' => 'date form-control typeahead','id'=>'exampleInput3','data-MdDateTimePicker'=>'true', 'data-placement'=>'rigth']) !!}
                    @else
                        {!! Form::text("custom_fields[$item->name]", null, ['class' => 'form-control typeahead','data-source'=>json_encode($item->suggestions),'autocomplete'=>is_array($item->suggestions)?'off':'on']) !!}

                    @endif
                    {!! $errors->first("custom_fields.$item->name",'<span class="help-block">:message</span>') !!}
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="collapse" id="extra">
    <div class="card card-body">
    <div class="form-group col-sm-12 erja">
        <div id="extraClose" class="alert alert-info" role="alert"><p> اطلاعات فردی، شغلی، تحصیلی</p></div>
    </div>

        @foreach ($fardi as $item)
            <div class="form-group col-sm-6 {{ $errors->has("custom_fields.$item->name") ? 'has-error' :'' }}">
                {!! Form::label("custom_fields[$item->name]", Str::title(str_replace('_',' ',$item->name)).":") !!}
                {!! Form::text("custom_fields[$item->name]", null, ['class' => 'form-control typeahead','data-source'=>json_encode($item->suggestions),'autocomplete'=>is_array($item->suggestions)?'off':'on']) !!}
                {!! $errors->first("custom_fields.$item->name",'<span class="help-block">:message</span>') !!}
            </div>
        @endforeach
        @foreach ($shoghli as $item)
            <div class="form-group col-sm-6 {{ $errors->has("custom_fields.$item->name") ? 'has-error' :'' }}">
                {!! Form::label("custom_fields[$item->name]", Str::title(str_replace('_',' ',$item->name)).":") !!}
                @if (in_array($item->name, array("نوع استخدام")))
                    <?php $current = $document!=null ? $document->custom_fields[$item->name] ?? '': null; ?>
                    {!! Form::select("custom_fields[$item->name]",array_combine($item->suggestions,$item->suggestions), $current ?? '',['class' => 'form-control typeahead']) !!}
                @else
                    {!! Form::text("custom_fields[$item->name]", null, ['class' => 'form-control typeahead','data-source'=>json_encode($item->suggestions),'autocomplete'=>is_array($item->suggestions)?'off':'on']) !!}
                @endif
                {!! $errors->first("custom_fields.$item->name",'<span class="help-block">:message</span>') !!}
            </div>
        @endforeach
        @foreach ($tahsili as $item)
            <div class="form-group col-sm-6 {{ $errors->has("custom_fields.$item->name") ? 'has-error' :'' }}">
                {!! Form::label("custom_fields[$item->name]", Str::title(str_replace('_',' ',$item->name)).":") !!}
                {!! Form::text("custom_fields[$item->name]", null, ['class' => 'form-control typeahead','data-source'=>json_encode($item->suggestions),'autocomplete'=>is_array($item->suggestions)?'off':'on']) !!}
                {!! $errors->first("custom_fields.$item->name",'<span class="help-block">:message</span>') !!}
            </div>
        @endforeach
    </div>
</div>


{{--end additional attributes--}}

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('ذخیره', ['class' => 'btn btn-primary','name'=>'justsave']) !!}
    {!! Form::submit('ذخیره و خروج', ['class' => 'btn btn-primary']) !!}
    {!! Form::submit('ذخیره و بارگذاری', ['class' => 'btn btn-primary','name'=>'savnup']) !!}
    <a href="{!! route('documents.index') !!}" class="btn btn-default">انصراف</a>
</div>

<script src="{{asset('js/calendar.js')}}"></script>
<script src="{{asset('js/jquery.Bootstrap-PersianDateTimePicker.js')}}"></script>
<script>
    $('#extraClose').on('click',function(){
    $('#extra').collapse('hide');
    })

    $('#erjaatClose1').on('click',function(){
    $('#erjaat').collapse('hide');
    })

    $('#erjaatClose2').on('click',function(){
    $('#erjaat').collapse('hide');
    })

    $('#erjaatClose3').on('click',function(){
    $('#erjaat').collapse('hide');
    })
</script>
