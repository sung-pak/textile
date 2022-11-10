@extends('voyager::master')

@section('page_header')

@endsection

@include('voyager::alerts')
@include('voyager::dimmers')

@section('content')
    @if(session('skus'))
      <div class="container">
        <div class="alert alert-success alert-dismissible show" role="alert">
        <button type="button" class="close mr-5" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
          The following SKUs were updated: <br/>
          <h4 class="alert-heading"><strong>{{session('skus')}}</strong></h4>          
        </div>
      </div>
    @endif
      <h1 class="page-title">Add or update products from Web Distribution</h1>
      <div class="container-fluid">
        <p>Enter a new or existing product to update/add from Web Distribution.</p>
      </div>
        {{ Form::open(
          array(
              'name' => 'update-from-wd',
              'url' => 'dashboard/update-from-wd/',
              //'onSubmit' => 'actionOnSubmit()',
              'method' => 'post'
              )
          ) }}

         @php
         echo Form::label('product', 'Product to Update/Add', ['class' => 'label']);
         echo Form::text('product', null, ['required'=>'false', 'placeholder'=>'Product name', 'id' => 'product']);
         echo Form::submit('UPDATE/ADD', ['class' => 'btn btn-primary']);
         @endphp
         {{ Form::close() }}
         <br>
         <div class="container-fluid">
           <p>Enter a client id to update/add from Web Distribution.</p>
         </div>
         {{ Form::open(
           array(
               'name' => 'update-client-from-wd',
               'url' => 'dashboard/update-from-wd/',
               //'onSubmit' => 'actionOnSubmit()',
               'method' => 'post'
               )
           ) }}

          @php
          echo Form::label('client', 'Client to Update/Add', ['class' => 'label']);
          echo Form::text('client', null, ['required'=>'false', 'placeholder'=>'Client ID', 'id' => 'client']);
          echo Form::submit('UPDATE/ADD', ['class' => 'btn btn-primary']);
          @endphp
          {{ Form::close() }}

        <div class="container-fluid">
          <p>Update all Products Updated in WD since..</p>
        </div>
          {{ Form::open(
            array(
                'name' => 'update-product-from-wd',
                'url' => 'dashboard/update-from-wd/',
                //'onSubmit' => 'actionOnSubmit()',
                'method' => 'post',
                'class' => "col-md-6"
                )
            ) }}

          @php            
            echo Form::label('date_from', 'Date From', ['class' => 'control-label']);
            echo Form::date('date_from', null, ['id' => 'to', 'class'=>'form-control']);
            echo Form::submit('UPDATE/ADD', ['class' => 'btn btn-primary']);
          @endphp
          {{ Form::close() }}
          <br>
@endsection
