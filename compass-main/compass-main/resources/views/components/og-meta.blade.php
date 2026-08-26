@props([
    'title' => 'Compass - Automation Job Workflow',
    'description' => 'Automate your job application workflow with CompassBot. Streamline your job search process, track applications, and enhance your productivity.',
    'image' => asset('assets/og-default.jpg'),
])

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
