<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('page_title', 'Document')</title>
    @stack('styles')
</head>

<body>
    @yield('admin_main_content')
</body>

</html>
