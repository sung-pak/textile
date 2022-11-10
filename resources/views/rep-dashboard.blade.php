@extends('layouts.app')

@section('content')
<div class="container mt-5">

@if($rep == false)
<h3 class="text-center mx-auto col-md-10">Data not available</h3>
@else
  <div class="row align-items-center flex-column">

    @if ($userName)
    <div class = "row col-md-12">
      <div class="col-md-1"></div>
      <h3 class="text-center mx-auto col-md-10" style="margin-bottom: 30px;">     Hi, Rep {{strtoupper($userName)}}</h3>
      <a class="col-md-1" href="/logout">LOGOUT</a>
    </div>
    @else
    <h3 class="text-center mx-auto">My Dashboard</h3>
    @endif

    <div class="col-md-10 d-flex flex-md-row flex-sm-column homeTile-container">
      <div class="flex-grow-1 d-block">
        <a href="/sample-request-form"><img src="/images/ui/Innovations_Harlequin.jpg" style="width:100%"><h5 class="text-center mx-auto">REQUEST A SAMPLE</h5></a>

      </div>
      <div class="flex-grow-1 d-block">
        <a href="/docs/FA22-Pricelist-USA.pdf" target="_blank"><img src="/images/ui/Innovations_Intaglio.jpg" style="width:100%"><h5 class="text-center mx-auto">PRICE LIST</h5></a>

      </div>

      <div class="flex-grow-1 d-block myaccount-box">
        <a href="https://form.asana.com/?k=Clm-s_BIgVeHaDvrHsbE5A&d=1152687222974421" target="_blank"><img src="/images/ui/Innovations-Pulse.jpg" style="width:100%"><h5 class="text-center mx-auto">CUSTOMS REQUEST</h5></a>
    </div>
    </div>


  </div>
</div>
<div id="home" class="container-fluid">
      <div class="row col-md-10 mx-auto justify-content-center order-header pt-5 pb-2">
        <div class="col-md-12 row">
          <div class="col-4">
            <h6 class="font-weight-bold d-block text-right">Annual Target</h6>
          </div>
          <div class="col-4">
            <h6 class="font-weight-bold d-block">${{number_format($salesTarget,2,'.',',')}}</h6>
          </div>
        </div>
        <div class="col-md-12 row">
          <div class="col-4">
            <h6 class="font-weight-bold d-block text-right">Year-to-Date Target</h6>
          </div>
          <div class="col-4">
            <h6 class="font-weight-bold d-block">${{number_format($ytdTarget,2,'.',',')}}</h6>
          </div>
        </div>
        <div class="col-md-12 row pb-5">
          <div class="col-4">
            <h6 class="font-weight-bold d-block text-right">Year-to-Date Sales</h6>
          </div>
          <div class="col-8">


            <p class="font-weight-bold text-align-left">${{number_format($ytdSales, 2, '.', ',')}}
              @if($percent != -1)(@if($percent>=100)<span class="font-weight-bold font-green">{{$percent}}%</span>
                 @else<span class="font-weight-bold font-red">{{$percent}}%</span>
                @endif
                of target)
              @endif

            </p>
          </div>
        </div>
        <div class="border-bottom w-100"></div>

        <div class="col-md-12 row">
          <div class="col-4">
            <h6 class="font-weight-bold d-block text-right">Unshipped Orders</h6>
          </div>
          <div class="col-2">
            <h6 class="font-weight-bold d-block text-right" style="margin-right: 115px;">{{$unshippedOrder}}</h6>
          </div>
          <div class="col-4">
            <h6 class="font-weight-bold d-block text-right" style="margin-right:200px;">${{number_format($orderAmount, 2, '.', ',')}}</h6>
          </div>
        </div>
        <div class="col-md-12 row pb-5">
          <div class="col-4">
            <h6 class="font-weight-bold d-block text-right">Unshipped Reserves</h6>
          </div>
          <div class="col-2">
            <h6 class="font-weight-bold d-block text-right" style="margin-right: 115px;">{{$unshippedReserve}}</h6>
          </div>
          <div class="col-4">
            <h6 class="font-weight-bold d-block text-right" style='margin-right:200px;'>${{number_format($reserveAmount, 2, '.', ',')}}</h6>
          </div>
        </div>

        @if(isset($yearObj) && count($yearObj) > 0)
        <div class="col-md-12 purchase-container">


          <table class="table order-table">
            <thead class="">
              <tr>
                <th scope="col">Year</th>
                <th scope="col" style="padding-left:96px;">Sales</th>
                <th scope="col">Number of Invoices</th>
              </tr>
            </thead>
            <tbody>
            @foreach($yearObj as $transaction)
              <tr>
                <td>{{ $transaction['year'] }}</td>
                <td align="right" style="padding-right:200px;">${{ number_format($transaction['sales'], 2, '.', ',') }}</td>
                <td align="right" style="padding-right: 240px;">{{ $transaction['count_invoice'] }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>

        @else
        <div class="col-md-12">
          <div class="d-flex order-row-titles justify-content-center">
            No recent orders.
          </div>
        </div>
        @endif
      </div>



        @if(isset($monthObj) && count($monthObj) > 0)
        <div class="col-md-12 purchase-container">


          <table class="table order-table">
            <thead class="">
              <tr>
                <th scope="col">Month</th>
                <th scope="col" style="padding-left:96px;">Sales</th>
                <th scope="col">Number of Invoices</th>
              </tr>
            </thead>
            <tbody>
            @foreach($monthObj as $transaction)
              <tr>
                <td>{{ $transaction['month'] }}</td>
                <td align="right" style="padding-right:200px;">${{ number_format($transaction['sales'], 2, '.', ',')   }}</td>
                <td align="right" style="padding-right: 240px;">{{ $transaction['count_invoice'] }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>

        @else
        <div class="col-md-12">
          <div class="d-flex order-row-titles justify-content-center">
            No recent orders.
          </div>
        </div>
        @endif
      </div>
  @endif
    </div>
    <div class="container">

<div class="row align-items-center flex-column">
  <h4 class="text-center mx-auto font-weight-bold mt-5">NEED HELP?</h4>
  <h6 class="box-header text-center mx-auto mt-1">Call us at 800.2276.8053 or email us <a class="btn-link" href="mailto:info@innovationsusa.com">info@innovationsusa.com</a></h6>
  <script type="text/javascript">
    $(document).ready(function() {
        $('#purchase_order').on('click', function() {

          $('#sample_order').removeClass('active');
          $('#purchase_order').addClass('active');
          $('.sample-container').hide();
          $('.purchase-container').slideDown();
        });
        $('#sample_order').on('click', function() {

          $('#purchase_order').removeClass('active');
          $('#sample_order').addClass('active');
          $('.purchase-container').hide();
          $('.sample-container').slideDown();
        });
      }
    )
  </script>
    @endsection
