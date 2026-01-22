<!DOCTYPE html>
<html>
<head>
    <title>Redirecting...</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        .loader { border: 5px solid #f3f3f3; border-top: 5px solid #3498db; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; margin: 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="loader"></div>
    <p>Redirecting to app...</p>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Try to open the app
        const deepLink = "{{ $deepLink }}";
        const appStoreUrl = "{{ $appStoreUrl }}";

        // Set a timeout for app open attempt
        const openTimeout = setTimeout(function() {
            // If we reach here, app is not installed - redirect to App Store
            window.location.href = appStoreUrl;
        }, 2000);

        // Try to open the app
        window.location.href = deepLink;

        // Listen for visibility change to cancel the fallback
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // User has likely switched to the app
                clearTimeout(openTimeout);
            }
        });
    });
    </script>
</body>
</html>
