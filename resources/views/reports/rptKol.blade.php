@extends('layouts.app')
<style>
    .table{
        direction: rtl;
    }

    .table tr th{
        text-align: center !important;
    }

</style>
@section('title','گزارش کلی')
@section('content')
<meta http-equiv="Content-Language" content="ar-lb">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="{{asset('js/highcharts/highcharts.js')}}"></script>
<script src="{{asset('js/highcharts/data.js')}}"></script>
<script src="{{asset('js/highcharts/drilldown.js')}}"></script>
<script src="{{asset('js/highcharts/exporting.js')}}"></script>
<script src="{{asset('js/highcharts/export-data.js')}}"></script>
<script src="{{asset('js/highcharts/accessibility.js')}}"></script>
@php
$i = 0;
if($docs != null){
@endphp
<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">ردیف</th>
        <th scope="col">شماره پرونده</th>
        <th scope="col">نام پرونده</th>
        <th scope="col">توضیحات</th>
        <th scope="col">تاریخ آخرین ارجاع</th>
      </tr>
    </thead>

    <tbody>
        @foreach ( $docs as $item )
        <tr>
            <th scope="row">{{ ++$i }}</th>
            <td>{{ $item->id }}</td>
            <td>{!! $item->name !!}</td>
            <td>{!! $item->description !!}</td>
            <td>
                    @if(!is_null(data_get($item->custom_fields, "تاریخ نامه ارجاع چهارم")))
                        {!! ($item->custom_fields["تاریخ نامه ارجاع چهارم"]) !!}
                    @elseif(!is_null(data_get($item->custom_fields, "تاریخ نامه ارجاع سوم")))
                        {!! ($item->custom_fields["تاریخ نامه ارجاع سوم"]) !!}
                    @elseif(!is_null(data_get($item->custom_fields, "تاریخ نامه ارجاع دوم")))
                        {!! ($item->custom_fields["تاریخ نامه ارجاع دوم"]) !!}
                    @else(!is_null(data_get($item->custom_fields, "تاریخ نامه ارجاع")))
                        {!! ($item->custom_fields["تاریخ نامه ارجاع"]) !!}
                    @endif
            </td>
        </tr>
        @endforeach
    </tbody>

</table>
@php
}elseif($cnt!= null){
@endphp
<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">ردیف</th>
        <th scope="col">حوزه ارجاع</th>
        <th scope="col">تعداد</th>
      </tr>
    </thead>

    <tbody>
        @foreach ( $cnt as $item=>$value )
        <tr>
            <th scope="row">{{ ++$i }}</th>
            <td>{{ $item }}</td>
            <td>{{ $value }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div id="container" style="height: 300px;"></div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Highcharts.chart('container', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'تعداد ارجاعات به تفکیک حوزه ارجاع'
            },
            xAxis: {
                categories: <?php echo json_encode(array_keys($cnt),JSON_UNESCAPED_UNICODE) ?>
            },
            yAxis: {
                title: {
                    text: 'تعداد ارجاعات'
                }
            },
            series: [{
                name: 'همه سال‌ها',
                data: {{ (json_encode(array_values($cnt),JSON_UNESCAPED_UNICODE)) }}
            }],
        });
    });
    </script>


@php
}elseif($gardesh!= null){
@endphp
<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">ردیف</th>
        <th scope="col">تاریخ نامه</th>
        <th scope="col">شماره نامه</th>
        <th scope="col">شماره داخلی</th>
        <th scope="col">فرستنده</th>
        <th scope="col">گیرنده</th>
        <th scope="col">تعداد پیوست</th>
        <th scope="col">توضیحات</th>
      </tr>
    </thead>

    <tbody>
        @foreach ( $gardesh as $item)
        <tr>
            <th scope="row">{{ ++$i }}</th>
            <td>{{ $item["tarikh_name"] }}</td>
            <td>{{ $item["shomare_name"] }}</td>
            <td>{{ $item["shomare_dakheli"] }}</td>
            <td>{{ $item["ferestandeh"] }}</td>
            <td>{{ $item["girandeh"] }}</td>
            <td>{{ $item["peivast"] }}</td>
            <td>{{ $item["tozihat"] }}</td>
        </tr>
        @endforeach
    </tbody>

</table>
@php
}
@endphp
@endsection
