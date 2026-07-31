@props([
    'lang'  => 'bash',   // bash | php | ini | plaintext
    'title' => null,     // filename label
    'code'  => '',       // raw code string (use $slot if omitted)
])

@php
use Highlight\Highlighter;

$raw = trim($code ?: $slot);

try {
    $hl  = new Highlighter();
    $hl->setAutodetectLanguages(['bash','php','ini','plaintext','shell']);
    $res = $hl->highlight($lang, $raw);
    $highlighted = $res->value;
} catch (\Throwable) {
    $highlighted = e($raw);
}

$dotColors = ['#FF5F57','#FEBC2E','#28C840'];  // macOS traffic lights
$langLabel  = strtoupper($lang);
$fileLabel  = $title ?: match($lang) {
    'php'  => 'snippet.php',
    'ini'  => '.env',
    'bash','shell' => 'terminal',
    default => 'code',
};
@endphp

<div {{ $attributes->merge(['class' => 'neo-code-window neo-border shadow-neo overflow-hidden']) }}>

    {{-- ── Mac window chrome ── --}}
    <div class="neo-code-titlebar">
        {{-- traffic lights --}}
        <div class="neo-code-dots">
            @foreach($dotColors as $color)
                <span class="neo-code-dot" style="background:{{ $color }}"></span>
            @endforeach
        </div>
        {{-- filename --}}
        <span class="neo-code-filename">{{ $fileLabel }}</span>
        {{-- lang badge --}}
        <span class="neo-code-lang">{{ $langLabel }}</span>
    </div>

    {{-- ── Code body ── --}}
    <div class="neo-code-body">
        <pre class="neo-code-pre hljs language-{{ $lang }}"><code>{!! $highlighted !!}</code></pre>
    </div>

</div>
