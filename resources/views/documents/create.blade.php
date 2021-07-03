@extends('layouts.app')
@section('title',ucfirst(config('settings.document_label_singular'))." جدید")

@section('meta_tags')
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@endsection

@section('content')
    <section class="content-header">
        <h1>
            {{ucfirst(config('settings.document_label_singular'))}}
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'documents.store']) !!}
                        @include('documents.fields',['document'=>null])
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
