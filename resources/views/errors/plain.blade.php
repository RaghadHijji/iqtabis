{{-- a light layout for server errors: no navbar and no database, so it still shows when something is broken --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | اقتبس</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&family=Amiri:wght@400;700&family=Aref+Ruqaa:wght@700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-page text-ink">
    <header class="flex justify-center pt-10">
        <span class="font-brand text-5xl font-bold text-ink">اقتبس</span>
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>
