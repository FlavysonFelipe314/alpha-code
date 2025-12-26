<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Autenticação') - AlphaCode</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #0A0A0A; 
            color: #E0E0E0; 
            margin: 0;
            padding: 0;
        }
        .auth-background {
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%;
            background: radial-gradient(circle at 50% 50%, rgba(176, 26, 26, 0.15) 0%, rgba(10,10,10,0) 60%);
            z-index: -1;
        }
        .auth-card {
            background: rgba(24, 24, 27, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid #3f3f46;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .form-input {
            background-color: #27272a;
            border: 1px solid #3f3f46;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            color: white;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        .btn-primary {
            background: #ef4444;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 700;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        .btn-primary:hover {
            background: #dc2626;
        }
        .link {
            color: #60a5fa;
            text-decoration: none;
        }
        .link:hover {
            color: #93c5fd;
            text-decoration: underline;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-background"></div>
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="auth-card p-8 w-full max-w-md">
            @yield('content')
        </div>
    </div>
    @stack('scripts')
</body>
</html>

