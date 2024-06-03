@extends('layouts.admin')

@inject('affiliate_model', 'App\Models\AffiliateProgram')

@section('title', 'Affiliate Program')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.affiliate.update') }}" method="PUT" data-callback="changeSetting">
                    @csrf

                    <div class="card-body row">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label for="enabledInput">Enable Affiliate Program</label>
                                <select class="form-control" name="enabled">
                                    <option value="1" @if ($affiliate_model->where('key', 'enabled')->value('value')) selected @endif>Yes</option>
                                    <option value="0" @unless ($affiliate_model->where('key', 'enabled')->value('value')) selected @endunless>No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="conversionInput">Conversion (%)</label>
                                <input type="text" name="conversion" value="{{ $affiliate_model->where('key', 'conversion')->value('value') }}" class="form-control" id="conversionInput" placeholder="Conversion Rate" required>
                            </div>
                        </div>
                        <div class="col-lg-6 offset-lg-1">
                            <div class="form-group">
                                <div class="alert alert-info">
                                    For example, when a client purchases a $10-product and the conversion rate is 50%, his/her referer will receive <b>$10 * 50% = $5</b>. This only applies to the first purchase of the client.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
    <script>
        function changeSetting(data) {
            if (data.success) {
                toastr.success(data.success)
                waitRedirect('{{ route('admin.cache') }}')
            } else if (data.error) {
                toastr.error(data.error)
            } else if (data.errors) {
                data.errors.forEach(error => { toastr.error(error) });
            } else {
                wentWrong()
            }
        }
    </script>
@endsection
