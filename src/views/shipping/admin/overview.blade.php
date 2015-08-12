@extends('admin::overview')

@section('report')
	<div class="table-responsive">
	@if ($report->hasRows())
		<ol class="secret-list faux-table{{ $sortableClass }}" data-model="{{ $identifier }}">

			<li class="faux-table-row faux-table-heading">
			@if ($sortableClass)
				<div class="faux-cell cell-small"></div>
			@endif
			@foreach ($report->getHeadings() as $heading)
				<div class="faux-cell">{{ $heading }}</div>
			@endforeach
				<div class="no-wrap faux-cell"></div>
			</li>

		@foreach ($report->getRows() as $row)
			<li class="faux-table-row" data-id="{{ $row->getId() }}">
			@if ($sortableClass)
				<div class="faux-cell cell-small">
					<i class="fa fa-sort sorting-handle"></i>
				</div>
			@endif
			@foreach ($row->getColumns() as $name => $value)
				<div class="faux-cell">{{ $value }}</div>
			@endforeach
				<div class="no-wrap faux-cell">
					<a class="btn btn-success btn-sm" type="submit" href="{{ action('Admin\ShippingCostController@createForMethod', ['method' => $row->getId()]) }}">
						<i class="fa fa-plus-square"></i>
						Add Cost
					</a>

					<a href="{{ URL::action($controller . '@edit', [$row->getId()]) }}" class="btn btn-info btn-sm" type="submit">
						<i class="fa fa-pencil"></i>
						Edit
					</a>

					@if ($canDelete)
						{{ Form::open(['class' => 'inline-form', 'action' => [ $controller . '@destroy', $row->getId() ], 'method' => 'DELETE']) }}
							<button class="btn btn-danger btn-sm" data-warn="true" type="submit"><i class="fa fa-minus-square"></i> Delete</button>
						{{ Form::close() }}
					@endif
				</div>
			</li>
		@endforeach
		</ol>
	@else
		<p>Nothing here yet. Why not add something?</p>
	@endif
	</div>
@stop
