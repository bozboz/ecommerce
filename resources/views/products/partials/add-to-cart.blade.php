{{ Form::open(['route' => 'cart.add', "class" => "product__buy" ]) }}

	{{ Form::hidden('orderable_type', get_class($product)) }}
	{{ Form::hidden('orderable_id', $product->id) }}

	<button type="submit" value="Add" class="btn--small">
		{{ $text or 'Add To Cart' }}
		<i class="fa fa-shopping-cart"></i>
	</button>

{{ Form::close() }}
