<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Toastr -->
<script src="/plugins/toastr/toastr.min.js"></script>
<!-- AdminLTE App -->
<script src="/dist/js/adminlte.min.js"></script>

<!-- CUSTOM SCRIPTS -->
<script>
    // CSS Lazy Loading
    function lazyLoadCss(href) {
        var css = document.createElement('link')
        css.href = href
        css.rel = 'stylesheet'
        document.getElementsByTagName('head')[0].appendChild(css)
    }

    // Show error toast
    function wentWrong() { toastr.error('Something went wrong! Please try again later.') }

    // Reset all forms on the page
    function resetForms() { $('form').each(function() { this.reset() }) }

    // Redirect after 1 second
    function waitRedirect(url) { setTimeout(() => { window.location.replace(url) }, 1000) }

    // Fix button name and value
    $(":submit", $('form')).click(function() {
        if ($(this).attr('name')) {
            $('form').append(
                $("<input type='hidden'>").attr({ 
                    name: $(this).attr('name'), 
                    value: $(this).attr('value'),
                })
            );
        }
    });

    // Send form data to API
    $('form').submit(function(event) {
        const callback = $(this).attr('data-callback')
        if (!callback) return true;
        event.preventDefault()
        toastr.info('Loading...')
        const url = $(this).attr('action')
        const data = $(this).serialize()
        const method = $(this).attr('method')
        $.ajax({
            'url': url,
            'data': data,
            'headers': {
                'Accept': 'application/json; charset=UTF-8',
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            'method': method,
            'success': function(data) { window[callback](data) },
            'error': function() { wentWrong() }
        })
    })

    lazyLoadCss('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback')
    lazyLoadCss('/plugins/fontawesome-free/css/all.min.css')
    lazyLoadCss('/plugins/toastr/toastr.min.css')
</script>
