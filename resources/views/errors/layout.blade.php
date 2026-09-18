{{-- ponytail: Simple error layout matching user specification --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>@yield('title', 'Error') - E-Vendor</title>
    <link rel="icon" type="image/png" href="{{ asset('images/evendor-logo.png') }}" sizes="32x32">
    <link href="{{ asset('css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .bg-home {
            min-height: 100vh;
            height: 100vh;
            position: relative;
        }
    </style>
</head>

<body
    onload="setTimeout(function(){ window.location.href = '{{ auth()->check() ? route('dashboard') : (Route::has('home') ? route('home') : url('/')) }}'; }, 3000);"
    class="bg-white">
    <!-- ERROR PAGE -->
    <section class="bg-home d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-12 text-center">
                    <a
                        href="{{ auth()->check() ? route('dashboard') : (Route::has('home') ? route('home') : url('/')) }}">
                        <img src="@yield('image', asset('images/illustrator/403.png'))" class="img-fluid" alt="@yield('title')">
                    </a>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end container-->
    </section><!--end section-->
    <!-- ERROR PAGE -->
</body>

</html>
