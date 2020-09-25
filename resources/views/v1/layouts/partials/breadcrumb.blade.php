@php
    $path = explode(".", Route::current()->getName());
@endphp
@if (Route::current()->getName() == 'home')
<div id="layers" class="layer--header"></div>
@else
<section class="content-header" style="display: list-item;">
    <ol class="breadcrumb">
        <li><a href="{{ url(__prefix()) }}"><i class="fa fa-windows"></i> Dashboard</a></li>
        @for ($i = 0; $i < count($path); $i++)
            @if ($i == (count($path) - 2))
                <li class="active">{{ str_title($path[$i]) }}</li>
            @elseif($i == (count($path) - 1))
            @else 
                <li><a href="{{ url(str_slug($path[$i])) }}">{{ str_title($path[$i]) }}</a></li>
            @endif
        @endfor
    </ol>
</section>
@endif