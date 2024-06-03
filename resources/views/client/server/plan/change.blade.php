@extends('layouts.client')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Plan 1 - <i>Category 1</i></h5>
                    <p class="card-text">
                        <ul>
                            <li>1 GB RAM</li>
                            <li>200% CPU</li>
                            <li>5 GB Disk</li>
                        </ul>
                    </p>
                    <a href="{{ route('client.server.plan.show', ['id' => $id]) }}" class="card-link"><i class="fas fa-arrow-left text-sm"></i> Choose another plan</a>
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
                    <label class="col-lg-3 col-form-label">Add-ons</label>
                    <div class="col-lg-7">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="addon1">
                            <p class="form-check-label">Add-on 1 <span class="float-right">$3 /month ($0 setup fee)</span></p>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="addon2">
                            <p class="form-check-label">Add-on 2 <span class="float-right">$5 /month ($0 setup fee)</span></p>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="addon3">
                            <p class="form-check-label">Add-on 3 <span class="float-right">$10 /month ($0 setup fee)</span></p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <label for="serverIpInput" class="col-lg-3 col-form-label">Billing Cycle</label>
                    <div class="col-lg-3">
                        <select class="form-control" name="cycle">
                            <option value="1">Monthly</option>
                            <option value="2">Trimonthly</option>
                            <option value="3">Biannually</option>
                            <option value="4">Annually</option>
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
                    <h6>Server <span class="float-right">Plan 1</span></h6>
                    <small>
                        Monthly <span class="float-right">$2 USD</span><br>
                        Setup Fee <span class="float-right">$0 USD</span>
                    </small>
                    <hr>
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