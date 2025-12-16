<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MaziwaHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'], 'https')
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js" defer></script>
</head>

<body class="bg-muted-50 text-muted-900 dark:bg-muted-950 dark:text-muted-100">
    <div id="app"></div>
</body>

</html>