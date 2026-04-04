@props(['canResetPassword' => false])

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NandoRAG - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=JetBrains+Mono:ital,wght@0,400;0,700;1,400&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/landing.css'])
    <style>
        body {
            font-family: 'JetBrains Mono', 'Space Mono', monospace;
            background-color: #F0EAD6;
            background-image: radial-gradient(circle, rgba(0,0,0,0.13) 1.5px, transparent 1.5px);
            background-size: 22px 22px;
        }
        .font-heading {
            font-family: 'Oswald', 'Impact', sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', 'Space Mono', monospace;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white border-4 border-black p-8" style="box-shadow: 8px 8px 0 #000;">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-[#22D3EE] border-4 border-black mb-4" style="box-shadow: 4px 4px 0 #000;">
                    <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <h1 class="font-heading text-4xl font-bold uppercase tracking-wider">NandoRAG</h1>
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                        class="w-full border-[3px] border-black bg-white px-4 py-3 font-mono focus:border-[#E879F9] focus:outline-none"
                        style="box-shadow: 3px 3px 0 #000;"
                    />
                    @error('email')
                        <p class="text-xs text-red-600 mt-1 font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="password" required autocomplete="current-password"
                        class="w-full border-[3px] border-black bg-white px-4 py-3 font-mono focus:border-[#E879F9] focus:outline-none"
                        style="box-shadow: 3px 3px 0 #000;"
                    />
                    @error('password')
                        <p class="text-xs text-red-600 mt-1 font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" 
                        class="w-4 h-4 border-2 border-black accent-[#22D3EE]" />
                    <label for="remember" class="font-heading text-xs uppercase tracking-wider">Remember me</label>
                </div>

                <button type="submit" 
                    class="w-full border-[3px] border-black bg-[#22D3EE] px-6 py-4 font-heading text-lg font-bold uppercase tracking-wider hover:bg-[#FACC15] transition-all hover:-translate-y-1"
                    style="box-shadow: 4px 4px 0 #000;"
                >
                    Sign In
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <p class="font-mono text-sm text-gray-600">
                &copy; 2026 NandoRAG
            </p>
        </div>
    </div>
</body>
</html>