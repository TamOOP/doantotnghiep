@if (session('error'))
    <div class="alert alert-danger ml-3 mr-3 mt-3" style="{{ isset($style) ? $style : '' }}">
        {{ session('error') }}
    </div>
@endif
