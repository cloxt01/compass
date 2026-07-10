@props([

    'title' => 'Compass • Cari Kerja Lebih Mudah',

    'description' => 'Lamar banyak lowongan lebih cepat, kelola status lamaran, dan pantau proses rekrutmen dalam satu tempat.',

    'image' => asset('assets/img/icon-hd.jpeg'),

])

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
