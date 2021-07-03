@extends('layouts.app')
@section('title',"لیست ".ucfirst(config('settings.document_label_plural')))
<?php
use Jenssegers\Date\Date;

Date::setLocale('fa');
?>
@section('css')
    <style type="text/css">
        .bg-folder-shaper {
            width: 100%;
            height: 115px;
            border-radius: 0px 15px 15px 15px !Important;
        }

        .folder-shape-top {
            width: 57px;
            height: 17px;
            border-radius: 20px 37px 0px 0px;
            position: absolute;
            top: -16px;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .folder-shape-top_notification {
            width: 57px;
            height: 17px;
            border-radius: 20px 37px 0px 0px;
            position: absolute;
            top: -16px;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: gold !important;
        }

        .widget-user-2 .widget-user-username, .widget-user-2 .widget-user-desc {
            margin-left: 10px;
            font-weight: 400;
            font-size: 17px;
        }

        .widget-user-username {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .m-t-20 {
            margin-top: 20px;
        }

        .dropdown-menu {
            min-width: 100%;
        }

        .doc-box.box {
            box-shadow: 0 0px 0px rgba(0, 0, 0, 0.0) !important;
        }

        .bg-folder-shaper:hover {
            background-color: yellow;
        }

        .select2-container {
            width: 100% !important;
        }

        #filterForm.in, filterForm.collapsing {
            display: block !important;
        }
    </style>
@stop
@section('scripts')
    <script>

    </script>
@stop
@section('content')
    <section class="content-header">
        <h1 class="pull-left">
            {{ucfirst(config('settings.document_label_plural'))}}
        </h1>
        <h1 class="pull-right">
            @can('create',\App\Document::class)
                <a href="{{route('documents.create')}}"
                   class="btn btn-primary">
                    <i class="fa fa-plus"></i>
                    پرونده جدید
                </a>
            @endcan
        </h1>
    </section>
    <div class="content" style="margin-top: 22px;">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-header">
                <div class="form-group hidden visible-xs">
                    <button type="button" class="btn btn-default btn-block" data-toggle="collapse"
                            data-target="#filterForm"><i class="fa fa-filter"></i> فیلتر
                    </button>
                </div>

                {!! Form::model(request()->all(), ['method'=>'get','class'=>'form-inline visible hidden-xs','id'=>'filterForm']) !!}
                <div class="form-group">
                    <label for="search" class="sr-only">جست و جو</label>
                    {!! Form::text('search',null,['class'=>'form-control input-sm','placeholder'=>'جست و جو...']) !!}
                </div>
                <div class="form-group">
                    <label for="tags" class="sr-only">{{config('settings.tags_label_singular')}}:</label>
                    <select class="form-control select2 input-sm" name="tags[]" id="tags"
                            data-placeholder="انتخاب {{config('settings.tags_label_singular')}}" multiple>
                        @foreach($tags as $tag)
                            @canany(['read documents','read documents in tag '.$tag->id])
                                <option
                                    value="{{$tag->id}}" {{in_array($tag->id,request('tags',[]))?'selected':''}}>{{$tag->name}}</option>
                            @endcanany
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="status" class="sr-only">{{config('settings.tags_label_singular')}}:</label>
                    {!! Form::select('status',['0'=>"همه",config('constants.STATUS.PENDING')=>config('constants.STATUS.PENDING'),config('constants.STATUS.APPROVED')=>config('constants.STATUS.APPROVED'),config('constants.STATUS.REJECT')=>config('constants.STATUS.REJECT')],null,['class'=>'form-control input-sm']) !!}
                </div>
                <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-filter"></i> فیلتر</button>
                {!! Form::close() !!}
            </div>
            <div class="box-body">
                <div class="row">
                    @foreach ($documents as $document)
                        @cannot('view',$document)
                            @continue
                        @endcannot
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 m-t-20" style="cursor:pointer;">
                            <div class="doc-box box box-widget widget-user-2">
                                <div class="widget-user-header bg-gray bg-folder-shaper no-padding">

                                    @if ($document->custom_fields["نام کارشناس"] == auth()->user()->name)
                                        <div class="folder-shape-top_notification bg-gray"></div>
                                    @else
                                        <div class="folder-shape-top bg-gray"></div>
                                    @endif


                                    <div class="box-header">
                                    {{ $document->id }}
                                        <a href="{{route('documents.show',$document->id)}}" style="color: black;">
                                            <h3 class="box-title"><i class="fa fa-folder text-yellow"></i></h3>
                                        </a>

                                        <div class="box-tools pull-right">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-flat dropdown-toggle"
                                                        data-toggle="dropdown" aria-expanded="false"
                                                        style="    background: transparent;border: none;">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                                    <li><a href="{{route('documents.show',$document->id)}}">نمایش</a>
                                                    </li>
                                                    @can('edit',$document)
                                                        <li><a href="{{route('documents.edit',$document->id)}}">ویرایش</a>
                                                        </li>
                                                    @endcan
                                                    @can('delete',$document)
                                                        <li>
                                                            {!! Form::open(['route' => ['documents.destroy', $document->id], 'method' => 'delete']) !!}
                                                            {!! Form::button('حذف', [
                                                                        'type' => 'submit',
                                                                        'class' => 'btn btn-link',
                                                                        'onclick' => "return conformDel(this,event)"
                                                                    ]) !!}
                                                            {!! Form::close() !!}
                                                        </li>
                                                    @endcan

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.widget-user-image -->
                                    <a href="{{route('documents.show',$document->id)}}" style="color: black;">
                                    <span style="max-lines: 1; white-space: nowrap;margin-left: 3px;">
                                    @foreach ($document->tags as $tag)
                                            <small class="label"
                                                   style="background-color: {{$tag->color}};font-size: 80%;">{{$tag->name}}</small>
                                        @endforeach
                                    </span>
                                        <h5 class="widget-user-username" title="{{$document->name}}"
                                            data-toggle="tooltip">{{$document->name}}</h5>
                                        <h5 class="widget-user-desc" style="font-size: 12px"><span data-toggle="tooltip"
                                                                                                   title="{{jdate(\Carbon\Carbon::parse($document->updated_at)->timestamp)}}">{{jdate(\Carbon\Carbon::parse($document->updated_at)->timestamp)}}</span>
                                            <span
                                                class="pull-right" style="margin-right: 15px;">
                                            {!! $document->isVerified ? '<i title="Verified" data-toggle="tooltip" class="fa fa-check-circle" style="color: #388E3C;"></i>':'<i title="Unverified" data-toggle="tooltip" class="fa fa-remove" style="color: #f44336;"></i>' !!}
                                        </span></h5>
                                    </a>
                                </div>
                            </div>
                            <!-- /.widget-user -->
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="box-footer">
                {!! $documents->appends(request()->all())->render() !!}
            </div>
        </div>
    </div>
@endsection
