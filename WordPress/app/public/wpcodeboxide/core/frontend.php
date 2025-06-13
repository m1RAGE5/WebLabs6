<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="theme-color" content="#000000"/>
<meta
        name="description"
        content="WPCodeBox IDE"
/>


<title>WPCodeBox IDE</title>

<script>
    if (window.location.search.includes('token')) {
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        sessionStorage.setItem('wpcbide_token', token);
        window.history.replaceState({}, document.title, window.location.pathname);
    }


    function transformURL(url) {
        // Parse the URL to get different components
        let urlObject = new URL(url);

        // Replace "frontend.php" with "api.php"
        urlObject.pathname = urlObject.pathname.replace('frontend.php', 'api.php');

        // Remove existing query parameters
        urlObject.search = '?wpcodeboxide_route=';

        // Return the transformed URL as a string
        return urlObject.toString();
    }

    window.API_URL = transformURL(window.location.href);
    const urlObject = window.location;

    // Construct the base URL by combining protocol, hostname, and port
    window.HOME_URL = `${urlObject.protocol}//${urlObject.hostname}`;
    if(urlObject.port) {
        window.HOME_URL += `:${urlObject.port}`;
    }

</script>

    <?php if(getenv('WPCODEBOX_DEV')) { ?>
        <script defer src="http://localhost:3001/build/static/js/bundle.js"></script>
    <?php } else { ?>
        <script defer src="../public/js/main.js?v=1.0.1"></script>
        <link rel="stylesheet" href="../public/css/main.css?v=1.0.1">
    <?php } ?>
</head>
<body>
<noscript>You need to enable JavaScript to run this app.</noscript>
<div id="root"></div>
</body>
</html>