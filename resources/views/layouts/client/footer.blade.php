<footer class="main-footer">
    <!-- Default to the left -->
    <strong>Copyright &copy; {{ date('Y') }} <a href="{{ route('home') }}">{{ config('app.company_name') }}</a>. All Rights Reserved. Powered by <a href="https://docs.hedystia.com">Hedystia</a>.</strong>
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline-block">
        <a href="{{ route('terms') }}">Terms of Service</a> | <a href="{{ route('privacy') }}">Privacy Policy</a>
    </div>
</footer>
