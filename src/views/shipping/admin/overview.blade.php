@extends('admin::overview')

@section('report_header')
	@if ($canCreate)
		<a class="btn btn-success pull-right space-left" href="{{ action($createAction) }}">
			<i class="fa fa-plus-square"></i>
			New {{ $modelName }}
		</a>

		<a class="btn btn-info pull-right" href="{{ action($createBandAction) }}">
			<i class="fa fa-plus-square"></i>
			New Shipping Band
		</a>
	@endif

	<h1>{{ $heading }}</h1>

	@if (Session::has('model'))
		@foreach(Session::get('model') as $msg)
			<div id="js-alert" class="alert alert-success" data-alert="alert">
				{{ $msg }}
			</div>
		@endforeach
	@endif

	@include('admin::partials.sort-alert')

	{{ $report->getHeader() }}
@stop
