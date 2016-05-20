@extends('layouts.default')

@section('main')
    @each('ecommerce::products.partials.listing-item', $items, 'product')
@stop