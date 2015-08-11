@extends('admin::overview')

@section('report_header')
	{{ Form::open(['action' => $controller . '@downloadCsv']) }}
		{{ Form::submit('Download CSV', ['class' => 'btn btn-primary pull-right']) }}
	{{ Form::close() }}

	<h1>{{ $modelName }}</h1>

	@include('admin::partials.sort-alert')

	{{ $report->getHeader() }}
@stop

@section('report_footer')
	{{ $report->getFooter() }}
@stop
