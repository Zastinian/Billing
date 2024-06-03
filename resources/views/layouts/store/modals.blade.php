@unless (auth()->check())
    <div class="modal fade" id="login-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Login</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('api.store.login') }}" method="POST" data-callback="loginForm">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="emailInput">Email Address</label>
                            <input type="email" name="email" class="form-control" id="emailInput" placeholder="Email Address" required>
                        </div>
                        <div class="form-group">
                            <label for="passwordInput">Password</label>
                            <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="remember" class="custom-control-input" id="checkboxInput">
                                <label class="custom-control-label" for="checkboxInput">Remember Me</label>
                            </div>
                        </div>
                        @include('layouts.store.hcaptcha')
                        @if (config('app.open_registration'))
                            <div class="form-group mb-0">
                                <a href="javascript:void(0);" data-dismiss="modal" data-toggle="modal" data-target="#register-modal">Don't have an account?</a>
                            </div>
                        @endif
                        <div class="form-group mb-0">
                            <a href="javascript:void(0);" data-dismiss="modal" data-toggle="modal" data-target="#forgot-modal">Forgot password?</a>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (config('app.open_registration'))
        <div class="modal fade" id="register-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Register</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('api.store.register') }}" method="POST" data-callback="registerForm">
                        @csrf

                        <div class="modal-body">
                            <div class="form-group">
                                <label for="emailInput">Email Address</label>
                                <input type="email" name="email" class="form-control" id="emailInput" placeholder="Email Address" required>
                            </div>
                            <div class="form-group">
                                <label for="passwordInput">Password</label>
                                <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirmPasswordInput">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" id="confirmPasswordInput" placeholder="Confirm Password" required>
                            </div>
                            @include('layouts.store.hcaptcha')
                            <div class="form-group mb-0">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="terms" value="yes" class="custom-control-input" id="checkboxInput" checked required>
                                    <label class="custom-control-label" for="checkboxInput">I agree to the <a {!! to_page('terms') !!}>terms of service</a> and the <a {!! to_page('privacy') !!}>privacy policy</a>.</label>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <a href="javascript:void(0);" data-dismiss="modal" data-toggle="modal" data-target="#login-modal">Already registered?</a>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="forgot-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Forgot Password</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('api.store.forgot') }}" method="POST" data-callback="forgotForm">
                    @csrf
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="emailInput">Email Address</label>
                            <input type="email" name="email" class="form-control" id="emailInput" placeholder="Email Address" required>
                        </div>
                        @include('layouts.store.hcaptcha')
                        <div class="form-group mb-0">
                            <a href="javascript:void(0);" data-dismiss="modal" data-toggle="modal" data-target="#login-modal">Go back to login</a>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endunless
