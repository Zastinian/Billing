<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="icon" href="{{ config('app.favicon_file_path') }}">

<!-- Lazy-loading Styles -->
<noscript>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
</noscript>

<!-- Theme Style -->
<link rel="stylesheet" href="/dist/css/adminlte.min.css">

@if (config('app.google_analytics_id'))
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('app.google_analytics_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('app.google_analytics_id') }}');
    </script>
@endif
    
@if (config('app.arc_widget_id'))
    <!-- Arc.io -->
    <script async src="https://arc.io/widget.min.js#{{ config('app.arc_widget_id') }}"></script>
@endif
