@extends('layouts.default')

@section('main')
    <h1>{{ $product->name }}</h1>
    {{ Form::open(['route' => 'cart.add', "class" => "product__buy" ]) }}

        {{ Form::hidden('orderable_type', get_class($product)) }}
        {{ Form::hidden('orderable_id', $product->id) }}

        <button type="submit" value="Add" class="btn--small">
            buy
        </button>

    {{ Form::close() }}

@stop