<footer class="main-footer">
    <div class="container">
        <!-- To the right -->
        <div class="float-right d-none d-sm-inline">
            @if (config('page.status')) <a {!! to_page('status') !!}>System Status</a> | @endif<a {!! to_page('terms') !!}>Terms of Service</a> | <a {!! to_page('privacy') !!}>Privacy Policy</a>
        </div>
        <!-- Default to the left -->
        <strong>Copyright &copy; {{ date('Y') }} <a {!! to_page('home') !!}>{{ config('app.company_name') }}</a>. All Rights Reserved. Powered by <a href="https://docs.hedystia.com">Hedystia</a>.</strong>
    </div>
</footer>
