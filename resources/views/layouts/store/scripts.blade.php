<!-- hCaptcha -->
<script src='https://js.hcaptcha.com/1/api.js' async defer></script>

<script>
    // Change pages without refresh
    $('a').click(function() {
        if ($(this).attr('data-href-no-refresh') != 'true') return true
        const url = this.href
        $.ajax({'url': url, 'success': function(data) {
            var title = $(data).filter('title').text()
            var header = $(data).find('.content-header').html()
            var container = $(data).find('#content-container').html()
            history.pushState({'title': title, 'header': header, 'container': container}, title, url)
            document.title = title
            $('.content-header').html(header)
            $('#content-container').html(container)
        }, 'error': function() { wentWrong() }})
    })

    // Fix back buttons
    window.onpopstate = function(event) {
        if (event.state) {
            document.title = event.state.title
            $('.content-header').html(event.state.header)
            $('#content-container').html(event.state.container)
        }
    }

    function loginForm(data) {
        if (data.success) {
            toastr.success(data.success)
            resetForms()
            waitRedirect(data.url ? data.url : '{{ route('client.dash') }}')
        } else if (data.error) {
            toastr.error(data.error)
        } else if (data.errors) {
            data.errors.forEach(error => { toastr.error(error) });
        } else {
            wentWrong()
        }
    }
    
    @if (config('app.open_registration')) 
    function registerForm(data) {
        if (data.success) {
            toastr.success(data.success)
            resetForms()
            waitRedirect('{{ route('client.dash') }}')
        } else if (data.error) {
            toastr.error(data.error)
        } else if (data.errors) {
            data.errors.forEach(error => { toastr.error(error) });
        } else {
            wentWrong()
        }
    }
    @endif
    
    function forgotForm(data) {
        if (data.success) {
            toastr.success(data.success)
            resetForms()
        } else if (data.error) {
            toastr.error(data.error)
        } else if (data.errors) {
            data.errors.forEach(error => { toastr.error(error) });
        } else {
            wentWrong()
        }
    }
    
    function resetForm(data) {
        if (data.success) {
            toastr.success(data.success)
            resetForms()
            waitRedirect('{{ route('home', ['#login']) }}')
        } else if (data.error) {
            toastr.error(data.error)
        } else if (data.errors) {
            data.errors.forEach(error => { toastr.error(error) });
        } else {
            wentWrong()
        }
    }
    
    @if (config('page.contact'))
    function contactForm(data) {
        if (data.success) {
            toastr.success(data.success)
            resetForms()
        } else if (data.error) {
            toastr.error(data.error)
        } else if (data.errors) {
            data.errors.forEach(error => { toastr.error(error) });
        } else {
            wentWrong()
        }
    }
    @endif

    $(document).ready(function() {
        history.replaceState({
            'title': $('title').text(),
            'header': $('.content-header').html(),
            'container': $('#content-container').html()
        }, $('title').text(), window.location.href)

        if (window.location.hash.toLowerCase() == '#login') {
            $('#login-modal').modal('show');
        } else if (window.location.hash.toLowerCase() == '#register') {
            $('#register-modal').modal('show');
        } else if (window.location.hash.toLowerCase() == '#forgot') {
            $('#forgot-modal').modal('show');
        }
    })
</script>

@yield('store_scripts')
        