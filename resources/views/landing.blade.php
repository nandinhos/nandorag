<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #0f172a;
            font-family: system-ui, -apple-system, sans-serif;
        }
        h1 {
            font-size: 3rem;
            font-weight: 700;
            color: #e2e8f0;
            letter-spacing: -0.02em;
        }
    </style>
</head>
<body>
    <h1>NandoRAG</h1>
</body>
</html>
