<link rel="stylesheet" href="{{asset('css/lte/jquery.Bootstrap-PersianDateTimePicker.css')}}">
<script src="{{asset('js/jquery.js')}}"></script>
<script src="{{asset('js/bootstrap.min.js')}}"></script>
@extends('layouts.app')
@section('title', 'گزارش ها')
@section('content')
    <?php $arr = [
    '0' => 'گزارش کلی',
    '1' =>'پرونده های جاری',
    '2' =>'پرونده های راکد',
    '3' =>'پرونده های حقیقی',
    '4' => 'پرونده های حقوقی',
    '5' =>'براساس حوزه ارجاع',
    '6' => 'جدول عملکرد',
    '7' =>'بر اساس تاریخ ارجاع',
    '8' =>'گردش پرونده',
    '9' =>'خلاصه عملکرد'];
    ?>
    <section class="content-header">
        <h1 class="pull-left">گزارش ها</h1>
        </br>
        </br>
    </section>

    {!! Form::open(['route' => 'reports.rptKol', 'method' => 'get']) !!}
    <div class="content">
        <div class="clearfix"></div>
        <div class="col-sm-9">
            @include('flash::message')

            {!! Form::label('lbl_rptType', 'انتخاب نوع گزارش: ') !!}
            {!! Form::select('slc_rpt', $arr, $arr, ['class' => 'form-control typeahead', 'id' => 'slc_rpt']) !!}
            </br>
            <div id="extraSearch" style="display: none">
                {!! Form::label('lbl_erja', 'حوزه مورد نظر: ') !!}
                {!! Form::select('txt_erja', array_combine($hoze, $hoze), null, ['class' => 'form-control typeahead']) !!}
            </div>
            <div class="col-sm-6" id="extraDate" style="display: none">
                {!! Form::label('lbl_erja', 'از تاریخ: ') !!}
                {!! Form::text("startDate", '', ['class' => 'date form-control typeahead','data-MdDateTimePicker'=>'true', 'data-placement'=>'rigth']) !!}
                {!! Form::label('lbl_erja', 'تا تاریخ: ') !!}
                {!! Form::text("endDate",strval(jdate()->format('y/m/d')), ['class' => 'date form-control typeahead','data-MdDateTimePicker'=>'true', 'data-placement'=>'rigth']) !!}
            </div>
            <div id="gardesh" style="display: none">
                {!! Form::label('lbl_erja', 'شماره پرونده: ') !!}
                {!! Form::text('doc_id', '', ['class' => 'form-control typeahead']) !!}
            </div>
            <button id="btn_submit" type="submit" class="btn btn-primary" style="float: right; margin-top:3px">نمایش گزارش</button>
        </div>
    </div>
    {!! Form::close() !!}

    <script type="text/javascript">
        var select = document.getElementById('slc_rpt');
        select.onchange = function() {
            if (select.value == 5) {
                document.querySelector('#extraSearch').style.display = 'unset';
                document.querySelector('#extraDate').style.display = 'none';
                document.querySelector('#gardesh').style.display = 'none';
            }
            else if (select.value == 7) {
                document.querySelector('#extraDate').style.display = 'grid';
                document.querySelector('#extraSearch').style.display = 'none';
                document.querySelector('#gardesh').style.display = 'none';

            }
            else if (select.value == 8) {
                document.querySelector('#gardesh').style.display = 'grid';
                document.querySelector('#extraSearch').style.display = 'none';
                document.querySelector('#extraDate').style.display = 'none';
            }
            else {
                document.querySelector('#extraSearch').style.display = 'none';
                document.querySelector('#extraDate').style.display = 'none';
                document.querySelector('#gardesh').style.display = 'none';
            }
        };

    </script>

@endsection
<script src="{{asset('js/calendar.js')}}"></script>
<script src="{{asset('js/jquery.Bootstrap-PersianDateTimePicker.js')}}"></script>
