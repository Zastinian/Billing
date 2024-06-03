<noscript>
    <div class="alert alert-danger">
        <h5><i class="icon fas fa-exclamation-triangle"></i> Your browser has disabled JavaScript!</h5>
        Please enable JavaScript so that we can enhance your browsing experience.
    </div>
</noscript>

@unless ($secure)
    <div class="alert alert-danger">
        <h5><i class="icon fas fa-exclamation-triangle"></i> You are browsing this site with an insecure protocol!</h5>
        In order to encrypt any payment information or login credentials you submitted, please switch back to the HTTPS protocol.
    </div>
@endunless
