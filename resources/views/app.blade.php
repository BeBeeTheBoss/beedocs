<!DOCTYPE html>
<html>

<head>
    <title>BeeDocs - Create your docs</title>
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/128/809/809052.png" type="image/x-icon">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite('resources/js/app.js')
    @inertiaHead
    <style>
        ::-webkit-scrollbar{
            display: none;
        }
        body {
            background: rgb(2, 0, 32);
            background: linear-gradient(90deg, rgba(2, 0, 32, 1) 0%, rgba(0, 0, 0, 1) 0%, rgba(35, 1, 54, 1) 100%);
        }
    </style>
</head>

<body class="poppins-regular">
    @inertia
</body>

</html>
