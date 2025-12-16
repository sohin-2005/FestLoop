<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <script>
      (function () {
        try {
          const theme = localStorage.getItem('theme');
          if (theme === 'dark') document.documentElement.classList.add('dark');
          else if (theme === 'light') document.documentElement.classList.remove('dark');
        } catch (e) {}
      })();
    </script>
    
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <!-- at the end of layout, before closing body -->
<script>
  // toggle logic — runs after DOM is built
  (function () {
    try {
      const btn = document.getElementById('theme-toggle');
      const sun = document.getElementById('icon-sun');
      const moon = document.getElementById('icon-moon');

      function updateIcons() {
        const isDark = document.documentElement.classList.contains('dark');
        if (sun && moon) {
          if (isDark) { moon.style.display = 'inline'; sun.style.display = 'none'; }
          else { sun.style.display = 'inline'; moon.style.display = 'none'; }
        }
      }

      if (!btn) return;
      updateIcons();

      btn.addEventListener('click', function () {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateIcons();
      });
    } catch(e) { console.error(e); }
  })();
</script>

    </body>
</html>
