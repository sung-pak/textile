@extends('master')

@section('meta_tags')
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    {!! JsonLd::generate() !!}
@endsection

@section('main_content')

<div id="quickproductContainer" class="container my-5">
    <div class="col-md-12 my-5">
        <a href="/cart/sample" class="btn btn-primary float-right">PROCEED TO CHECKOUT</a>   
    </div>
    <div class="container my-5">
        <div id="quickSearchTable" class="my-5"></div>
    </div>
</div>
@endsection
