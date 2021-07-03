@section('css')
<style>
    span{
        font-size: 19px;
    }
</style>
@endsection

<li class="{{ Request::is('admin/home*') ? 'active' : '' }}">
    <a href="{!! route('admin.dashboard') !!}"><i class="fa fa-home"></i><span>{{ucfirst(config('settings.home_label_singular'))}}</span></a>
</li>
@can('read users')
    <li class="{{ Request::is('admin/users*') ? 'active' : '' }}">
        <a href="{!! route('users.index') !!}"><i class="fa fa-users"></i><span>{{ucfirst(config('settings.user_label_plural'))}}</span></a>
    </li>
@endcan
@can('read tags')
    <li class="{{ Request::is('admin/tags*') ? 'active' : '' }}">
        <a href="{!! route('tags.index') !!}"><i
                class="fa fa-tags"></i><span>{{ucfirst(config('settings.tags_label_plural'))}}</span></a>
    </li>
@endcan
@can('viewAny',\App\Document::class)
    <li class="{{ Request::is('admin/documents*') ? 'active' : '' }}">
        <a href="{!! route('documents.index') !!}"><i
                class="fa fa-file"></i><span>{{ucfirst(config('settings.document_label_plural'))}}</span></a>
    </li>
@endcan
<li class="{{ Request::is('reports/index*') ? 'active' : '' }}">
    <a href="{!! route('reports.index') !!}"><i class="glyphicon glyphicon-list-alt"></i><span>گزارش ها</span></a>
</li>
@if(auth()->user()->is_super_admin)
    <li class="treeview {{ Request::is('admin/advanced*') ? 'active' : '' }}">
        <a href="#">
            <i class="fa fa-info-circle"></i>
            <span>{{ucfirst(config('settings.advanced_settings_label_singular'))}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li class="{{ Request::is('admin/advanced/settings*') ? 'active' : '' }}">
                <a href="{!! route('settings.index') !!}"><i class="fa fa-gear"></i><span>{{ucfirst(config('settings.settings_label_singular'))}}</span></a>
            </li>
            <li class="{{ Request::is('admin/advanced/custom-fields*') ? 'active' : '' }}">
                <a href="{!! route('customFields.index') !!}"><i
                        class="fa fa-file-text-o"></i><span>{{ucfirst(config('settings.custom_fields_label_singular'))}}</span></a>
            </li>
            <li class="{{ Request::is('admin/advanced/file-types*') ? 'active' : '' }}">
                <a href="{!! route('fileTypes.index') !!}"><i class="fa fa-file-o"></i><span>انواع {{ucfirst(config('settings.file_label_singular'))}}</span></a>
            </li>
        </ul>
    </li>
@endif
<li style="position: fixed; bottom: 0; margin-left:1%;">
    Version: 1.0.2
</li>
