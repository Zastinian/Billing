@extends('layouts.client')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Add-on 1</h5>
                    <p class="card-text">
                        Description here
                    </p>
                    <a href="{{ route('client.server.addon.show', ['id' => $id]) }}" class="card-link"><i class="fas fa-arrow-left text-sm"></i> Choose another add-on</a>
                </div>
            </div>
            <form method="POST" action="" id="orderForm">
                @csrf
                
                @if ($errors->any())
                    <div class="form-group">
                        <div class="alert alert-danger">
                            Please fix the following error(s):
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                <div class="form-group row">
                    <label for="serverIpInput" class="col-lg-3 col-form-label">Billing Cycle</label>
                    <div class="col-lg-6">
                        <select class="form-control" name="cycle">
                            <option value="1">Monthly (Same as your plan)</option>
                        </select>
                    </div>
                </div>
            </form>
            <hr>
            <form method="POST" action="">
                @csrf

                <div class="form-group row">
                    <label for="couponCodeInput" class="col-lg-3 col-form-label">Coupon Code</label>
                    <div class="col-lg-4">
                        <input type="text" name="coupon" value="" class="form-control" id="couponCodeInput" placeholder="Coupon Code" required>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary btn-sm float-left">Apply</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-3">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title m-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <h6>Add-on <span class="float-right">Add-on 1</span></h6>
                    <small>
                        Monthly <span class="float-right">$3 USD</span><br>
                        Setup Fee <span class="float-right">$0 USD</span>
                    </small>
                    <hr>
                    <h6>Subtotal <span class="float-right">$5 USD</span></h6>
                    <small>
                        Coupon Code - EXAMPLE <span class="float-right">-$1 USD</span><br>
                        Account Credit <span class="float-right">-$2 USD</span>
                    </small>
                    <hr>
                    <h5>Due Today <span class="float-right">$2 USD</span></h5>
                    <small>Next Month <span class="float-right">$5 USD</span></small><br>
                    <button type="submit" form="orderForm" class="btn btn-primary float-right">Continue <i class="fas fa-arrow-circle-right"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection