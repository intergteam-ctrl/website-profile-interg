{{-- Language switcher: links to the same page in each language. --}}
@php($Lc = \App\Support\Locale::class)
<nav class="{{ $class }}" aria-label="{{ __('Pilih bahasa') }}">
  @foreach ($Lc::SUPPORTED as $code => $lang)
    <a href="{{ $Lc::switchUrl($code) }}" hreflang="{{ $lang['html'] }}" lang="{{ $lang['html'] }}" title="{{ $lang['name'] }}"{!! $code === $Lc::current() ? ' aria-current="true"' : '' !!}>{{ $lang['label'] }}</a>
  @endforeach
</nav>
