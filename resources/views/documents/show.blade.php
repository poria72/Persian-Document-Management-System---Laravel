@extends('layouts.app')
@section('title',"نمایش ".ucfirst(config('settings.document_label_singular')))
@section('css')
    <style>
        .box.custom-box {
            border: 1px solid #3c8dbc;
            box-shadow: 0 1px 2px 1px rgba(0, 0, 0, 0.08)
        }

        .box.custom-box .box-header {
            background-color: #3c8dbc;
            color: #fff;
            padding: 3px 5px;
        }

        .custom-box .user-block > .username, .custom-box .user-block > .description {
            margin-left: 0;
        }

        .custom-box .box-body img {
            height: 145px;
            object-fit: contain;
            width: 100%;
            border-radius: 3px;
        }

        object.obj-file-box {
            height: 80vh;
            object-fit: contain;
            width: 100%;
            border: 1px solid rgba(0, 40, 100, 0.2);
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .img-d-select .icheckbox_square-blue{
            position: absolute;
            right: 0;
            top: 0;
        }

        #sticky_footer {
            position: fixed;
            bottom: -4px;
            right: 10px;
        }
    </style>
@stop
@section('scripts')
    {{-- <script src="https://cdn.scaleflex.it/plugins/filerobot-image-editor/3/filerobot-image-editor.min.js"></script> --}}
    <script src="{{asset('js/filerobot-image-editor.min.js')}}"></script>
    <script id="file-modal-template" type="text/x-handlebars-template">
        <div id="fileModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">@{{name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-3">
                            <div class="form-group">
                                <a href="{{\Illuminate\Support\Str::finish(route('files.showfile',['dir'=>'original']),"/")}}@{{file}}?force=true"
                                   download class="btn btn-primary"><i
                                        class="fa fa-download"></i> دانلود اصل
                                </a>
                            </div>
                            <div class="form-group">
                                <label>{{"نوع ".ucfirst(config('settings.file_label_singular'))}}</label>
                                <p>@{{file_type.name}}</p>
                            </div>
                            <div class="form-group">
                                <label>بارگذاری توسط:</label>
                                <p>
                                    @{{created_by.name}}
                                </p>
                            </div>
                            <div class="form-group">
                                <label>زمان بارگذاری:</label>
                                <p>@{{formatDate created_at}}</p>

                            </div>
                            @{{#each custom_fields}}
                            <div class="form-group">
                                <label>@{{titleize @key}}</label>
                                <p>@{{this}}</p>
                            </div>
                            @{{/each}}
                        </div>
                        <div class="col-md-9">
                            <div class="file-modal-preview">
                                <object class="obj-file-box" classid=""
                                        data="{{\Illuminate\Support\Str::finish(route('files.showfile',['dir'=>'original']),"/")}}@{{file}}">
                                </object>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i>
                            بستن
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </script>
    <script>
        const ImageEditor = new FilerobotImageEditor();

        function showFileModal(data) {
            var template = Handlebars.compile($("#file-modal-template").html());
            var html = template(data);
            $("#modal-space").html(html);
            $("#fileModal").modal('show');

        }

        function submitPdfForm(varient){
            $("input[name='images_varient']").val(varient);
            $("#frm_image2pdf").submit();
        }

        $(function () {
            $("input[name='topdf_check[]']").on('ifToggled', function(event){
                var selectedValues = $("input[name='topdf_check[]']:checked").map(function(){
                    return $(this).val();
                }).toArray();
                if(selectedValues.length>0){
                    $("#sticky_footer").show();
                }else{
                    $("#sticky_footer").hide();
                }
                $("input[name='images']").val(selectedValues.join());
            });
            $("input[name='topdf_check[]']").trigger('ifToggled');
        });
    </script>
@stop
@section('content')
    <div id="modal-space">
    </div>
    <section class="content-header" style="margin-bottom: 27px;">
        <h1 class="pull-left">

            {{ucfirst(config('settings.document_label_singular'))}}
            {{$document->name}}  ({{ $document->id }})
        </h1>
        <h1 class="pull-right" style="margin-bottom: 5px;">
            <div class="dropdown" style="display: inline-block">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i
                        class="fa fa-download"></i> دانلود فایل فشرده
                    <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{route('files.downloadZip',['dir'=>'all','id'=>$document->id])}}">All</a>
                    </li>
                    <li>
                        <a href="{{route('files.downloadZip',['dir'=>'original','id'=>$document->id])}}">Original</a>
                    </li>
                    @foreach (explode(",",config('settings.image_files_resize')) as $varient)
                        <li>
                            <a href="{{route('files.downloadZip',['dir'=>$varient,'id'=>$document->id])}}">{{$varient}}w
                                (Images Only)</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @can('show', $document)
                <a href="{{action('DocumentController@print',$document->id)}}" class="btn btn-primary"><i class="fa fa-download"></i>
                    ذخیره تک برگ</a>
            @endcan
            @can('edit', $document)
                <a href="{{route('documents.edit', $document->id)}}" class="btn btn-primary"><i class="fa fa-edit"></i>
                    ویرایش</a>
            @endcan
            @can('delete', $document)
                {!! Form::open(['route' => ['documents.destroy', $document->id], 'method' => 'delete', 'style'=>'display:inline;']) !!}
                <button class="btn btn-danger" onclick="conformDel(this,event)" type="submit"><i
                        class="fa fa-trash"></i>
                    حذف
                </button>
                {!! Form::close() !!}
            @endcan
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="row">
            <div class="col-sm-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_files" data-toggle="tab"
                                              aria-expanded="true">{{ucfirst(config('settings.file_label_plural'))}}</a>
                        </li>
                        @can('verify', $document)
                            <li class=""><a href="#tab_verification" data-toggle="tab"
                                            aria-expanded="false">وضعیت تایید</a></li>
                        @endcan
                        <li class=""><a href="#tab_activity" data-toggle="tab" aria-expanded="false">اقدامات</a></li>
                        @can('user manage permission')
                            <li class=""><a href="#tab_permissions" data-toggle="tab"
                                            aria-expanded="false">دسترسی ها</a>
                            </li>
                        @endcan
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_files">
                            @if (config('settings.show_missing_files_errors')=='true' && $document->status!=config('constants.STATUS.APPROVED') && count($missigDocMsgs)!=0)
                                <div class="alert alert-danger fade in alert-dismissible">
                                    <button class="close" data-dismiss="alert" aria-label="close" title="close">
                                        &times;
                                    </button>
                                    {{-- <strong>The Following {{ucfirst(config('settings.file_label_plural'))}} Are
                                        Missing:</strong> --}}
                                        <strong>موارد زیر یافت نشد:</strong>
                                    <ul style="padding-inline-start: 20px;">
                                        @foreach ($missigDocMsgs as $msg)
                                            <li>{{$msg}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="row">
                                @foreach ($document->files->sortBy('file_type_id') as $file)
                                    <div class="col-xs-6 col-md-6 col-lg-4">
                                        <div class="box custom-box">
                                            <div class="box-body">
                                                @if (checkIsFileIsImage($file->file))
                                                    <span class="img-d-select">
                                                    <input type="checkbox" value="{{$file->file}}" name="topdf_check[]" class="iCheck-helper"/>
                                                </span>
                                                @endif
                                                <img onclick="showFileModal({{json_encode($file)}})"
                                                     style="cursor:pointer;"
                                                     src="{{buildPreviewUrl($file->file)}}"
                                                     alt="">
                                            </div>
                                            <div class="box-header">
                                                <div class="user-block">
                                                    <span class="label label-default">{{$file->fileType->name}}</span>
                                                    <span class="username" style="cursor:pointer;"
                                                          onclick="showFileModal({{json_encode($file)}})">{{$file->name}}</span>
                                                    <small class="description text-gray"><b
                                                            title="{{formatDateTime($file->created_at)}}"
                                                            data-toggle="tooltip">{{\Carbon\Carbon::parse($file->created_at)->diffForHumans()}}</b>
                                                        by <b>{{$file->createdBy->name}}</b></small>
                                                </div>
                                                <div class="pull-right box-tools">
                                                    <button type="button"
                                                            class="btn btn-default btn-flat dropdown-toggle"
                                                            data-toggle="dropdown" aria-expanded="false"
                                                            style="    background: transparent;border: none;">
                                                        <i class="fa fa-ellipsis-v" style="color: #fff;"></i>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><a href="javascript:void(0);"
                                                               onclick="showFileModal({{json_encode($file)}})">نمایش جزئیات</a></li>
                                                        <li>
                                                            <a href="{{route('files.showfile',['dir'=>'original','file'=>$file->file])}}?force=true"
                                                               download>دانلود
                                                                </a>
                                                        </li>
                                                        @if (checkIsFileIsImage($file->file))
                                                            @foreach (explode(",",config('settings.image_files_resize')) as $varient)
                                                                <li>
                                                                    <a href="{{route('files.showfile',['dir'=>$varient,'file'=>$file->file])}}?force=true"
                                                                       download>دانلود {{$varient}}w</a></li>
                                                            @endforeach
                                                            <li>
                                                                <a href="javascript:void(0)"
                                                                   onclick="javascript:ImageEditor.open('{{route('files.showfile',['dir'=>'original','file'=>$file->file])}}')">
                                                                    ویرایش تصویر
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            {!! Form::open(['route' => ['documents.files.destroy', $file->id], 'method' => 'delete', 'style'=>'display:inline;']) !!}
                                                            <button class="btn btn-link"
                                                                    onclick="conformDel(this,event)" type="submit">
                                                                حذف کردن
                                                            </button>
                                                            {!! Form::close() !!}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @can('update', [$document, $document->tags->pluck('id')])
                                <a href="{{route('documents.files.create',$document->id)}}"
                                   class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>
                                    اضافه کردن {{ucfirst(config('settings.file_label_plural'))}}</a>
                            @endcan
                        </div>
                        @can('verify', $document)
                            <div class="tab-pane" id="tab_verification">
                                @if ($document->status!=config('constants.STATUS.APPROVED'))
                                    {!! Form::open(['route' => ['documents.verify', $document->id], 'method' => 'post']) !!}
                                    <div class="form-group text-center">
                                    <textarea class="form-control" name="vcomment" id="vcomment" rows="4"
                                              placeholder="یادداشتی برای عملیات وارد نمایید (اختیاری)..."></textarea>
                                    </div>
                                    <div class="form-group text-center">
                                        <button class="btn btn-success" type="submit" name="action" value="approve"><i
                                                class="fa fa-check"></i> پذیرفته
                                        </button>
                                        <button class="btn btn-danger" type="submit" name="action" value="reject"><i
                                                class="fa fa-close"></i> رد شده
                                        </button>
                                    </div>
                                    {!! Form::close() !!}
                                @else
                                    <div class="form-group">
                                        <span class="label label-success">مورد تایید</span>
                                    </div>
                                    <div class="form-group">
                                        تایید کننده: <b>{{$document->verifiedBy->name}}</b>
                                    </div>
                                    <div class="form-gorup">
                                        زمان تایید: <b>{{formatDateTime($document->verified_at)}}</b>
                                        ({{\Carbon\Carbon::parse($document->verified_at)->diffForHumans()}})
                                    </div>
                                @endif
                            </div>
                        @endcan
                        <div class="tab-pane" id="tab_activity">
                            <ul class="timeline">
                                <li class="time-label">
                                    <span class="bg-red">{{formatDate($document->updated_at,'d M Y')}}</span>
                                </li>
                                @foreach ($document->activities as $activity)
                                    <li>
                                        <i class="fa fa-user bg-aqua" data-toggle="tooltip"
                                           title="{{$activity->createdBy->name}}"></i>

                                        <div class="timeline-item">
                                            <span class="time" data-toggle="tooltip"
                                                  title="{{formatDateTime($activity->created_at)}}"><i
                                                    class="fa fa-clock-o"></i> {{\Carbon\Carbon::parse($activity->created_at)->diffForHumans()}}</span>

                                            <h4 class="timeline-header no-border">{!! $activity->activity !!}</h4>
                                        </div>
                                    </li>
                                @endforeach
                                <li>
                                    <i class="fa fa-clock-o bg-gray"></i>
                                </li>
                            </ul>
                        </div>
                        @can('user manage permission')
                            <div class="tab-pane" id="tab_permissions">
                                <div>
                                    <div class="modal fade" id="modal-permission">
                                        {{Form::open(['route' => ['documents.store-permission',request('document')]])}}
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title">ایجاد دسترسی</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <select class="form-control" name="user_id" required>
                                                                <option value="">- انتخاب کاربر -</option>
                                                                @foreach($users as $usr)
                                                                    <option value="{{$usr->id}}">{{$usr->name}}({{$usr->username}})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @foreach (config('constants.DOCUMENT_LEVEL_PERMISSIONS')  as $perm)
                                                            <div class="col-sm-6" style="margin-top: 20px;">
                                                                <label>
                                                                    <input name="document_permissions[{{$perm}}]"
                                                                           type="checkbox" class=""
                                                                           value="1"> {{ucfirst($perm)}}
                                                                    این {{config('settings.document_label_singular')}}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default pull-left"
                                                            data-dismiss="modal">بستن
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">ذخیره دسترسی ها</button>
                                                </div>
                                            </div>
                                        </div>
                                        {{Form::close()}}
                                    </div>
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th colspan="3" style="font-size: 1.8rem;">
                                                دسترسی های مستقیم
                                                <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal"
                                                        data-target="#modal-permission">
                                                    <i class="fa fa-plus"></i> دسترسی جدید
                                                </button>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>کاربر</th>
                                            <th>دسترسی ها</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (count($thisDocPermissionUsers)==0)
                                            <tr>
                                                <td colspan="2">رکوردی یافت نشد</td>
                                            </tr>
                                        @endif
                                        @foreach($thisDocPermissionUsers as $perm)
                                            <tr>
                                                <td>{{$perm['user']->name}}</td>
                                                <td>
                                                    @foreach($perm['permissions'] as $item)
                                                        <label class="label label-default">{{$item}}</label>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    {{Form::open(['route' => ['documents.delete-permission',request('document'),$perm['user']->id]])}}
                                                    <button type="submit" class="btn btn-danger btn-xs"
                                                            onclick="return conformDel(this,event)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                    {{Form::close()}}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th colspan="3" style="font-size: 1.8rem;">دسترسی های به ارث رسیده
                                                توسط {{config('settings.tags_label_plural')}}</th>
                                        </tr>
                                        <tr>
                                            <th>{{ucfirst(config('settings.tags_label_singular'))}}</th>
                                            <th>کاربر</th>
                                            <th>دسترسی ها</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (count($tagWisePermList)==0)
                                            <tr>
                                                <td colspan="3">رکوردی یافت نشد</td>
                                            </tr>
                                        @endif
                                        @foreach ($tagWisePermList as $perm)
                                            <tr>
                                                <td>{{$perm['tag']->name}}</td>
                                                <td>{{$perm['user']->name}}</td>
                                                <td>
                                                    @foreach ($perm['permissions'] as $p)
                                                        <label class="label label-default">{{$p}}</label>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th colspan="3" style="font-size: 1.8rem;">دسترسی سراسری {{config('settings.document_label_plural')}}</th>
                                        </tr>
                                        <tr>
                                            <th>کاربر</th>
                                            <th>دسترسی ها</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (count($globalPermissionUsers)==0)
                                            <tr>
                                                <td colspan="2">رکوردی یافت نشد</td>
                                            </tr>
                                        @endif
                                        @foreach ($globalPermissionUsers as $perm)
                                            <tr>
                                                <td>{{$perm['user']->name}}</td>
                                                <td>
                                                    @foreach ($perm['permissions'] as $p)
                                                        <label class="label label-default">{{$p}}</label>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="box box-primary">
                    <div class="box-body" style="direction: rtl">
                        @foreach ($document->custom_fields??[] as $custom_field_name=>$custom_field_value)
                            <div class="form-group">
                                <?php if($custom_field_value != null){ ?>
                                {!! Form::label($custom_field_name, Str::title(str_replace('_',' ',$custom_field_name)).":",['style'=>'padding-left: 12px;']) !!}
                                <p>{{ $custom_field_value ?? '-'}}</p>
                                <?php } ?>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="box box-primary">
                    <div class="box-body" style="direction: rtl">
                        <div class="form-group">
                            <label style="padding-left: 12px;">نام {{ucfirst(config('settings.document_label_singular'))}} :</label>
                            <p>{{$document->name}}</p>
                        </div>
                        <div class="form-group">
                            <label style="padding-left: 12px;">{{ucfirst(config('settings.tags_label_plural'))}}:</label>
                            <p>
                                @foreach ($document->tags as $tag)
                                    <small class="label"
                                           style="background-color: {{$tag->color}};">{{$tag->name}}</small>
                                @endforeach
                            </p>
                        </div>
                        <div class="form-group">
                            <label style="padding-left: 12px;">توضیحات:</label>
                            <p>{!! $document->description ?? '-'!!}</p>
                        </div>
                        <div class="form-group">
                            <label style="padding-left: 12px;">وضعیت:</label>
                            @if ($document->status==config('constants.STATUS.PENDING'))
                                <span class="label label-warning">{{$document->status}}</span>
                            @elseif($document->status==config('constants.STATUS.APPROVED'))
                                <span class="label label-success">{{$document->status}}</span>
                            @else
                                <span class="label label-danger">{{$document->status}}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label style="padding-left: 12px;">ایجاد شده توسط:</label> {{$document->createdBy->name}}
                        </div>
                        <div class="form-group">
                            <label style="padding-left: 12px;">زمان تشکیل:</label>
                            <p>{!!jdate(\Carbon\Carbon::parse($document->created_at)->timestamp)!!} <br>
                                {{-- ({{\Carbon\Carbon::parse($document->created_at)->diffForHumans()}}) --}}
                                ({!!\Morilog\Jalali\Jalalian::forge($document->created_at)->ago()!!})
                            </p>
                        </div>
                        <div class="form-group">
                            <label style="padding-left: 12px;">آخرین به روز رسانی:</label>
                            <p>{!! jdate(\Carbon\Carbon::parse($document->updated_at)->timestamp) !!} <br>
                                {{-- ({{\Carbon\Carbon::parse($document->updated_at)->diffForHumans()}}) --}}
                                ({!!\Morilog\Jalali\Jalalian::forge($document->updated_at)->ago()!!})
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="sticky_footer">
        <form id="frm_image2pdf" action="{{route('files.downloadPdf')}}" method="post" style="display: inline">
            @csrf
            <input type="hidden" name="images">
            <input type="hidden" name="images_varient">
            <div class="dropup">
                <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-file-pdf-o"></i> PDF تبدیل تصاویر به
                    <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a href="javascript:void(0);" onclick="submitPdfForm('original')">اصل</a></li>
                    @foreach (explode(',',config('settings.image_files_resize')) as $varient)
                        <li><a href="javascript:void(0);" onclick="submitPdfForm('{{$varient}}')">{{$varient}}w</a></li>
                    @endforeach
                </ul>
            </div>
        </form>
    </div>
@endsection
