<div class="group-input mt-4">
    <p class="input-label" class="p-2">{{ $title }}</p>
    <div>
        <div class="datepicker-container">
            <input class="enable-checkbox" type="checkbox" name="{{ $cbName }}"
                {{ $time ? 'checked' : '' }}>

            <p class="mr-3 p-2">Bật</p>

            <input class="datepicker form-control" type="date" name="{{ $dateName }}"
                id="{{ $dateName }}"
                {{ $time ? 'value=' . $time['date'] : 'disabled' }}>

            <select name="{{ $hourName }}" id="{{ $hourName }}" class="hour-select form-select mr-2"
                {{ $time ? 'data-selected=' . $time['hour'] : 'disabled' }}>
            </select>

            <select name="{{ $minuteName }}" id="{{ $minuteName }}"
                class="minute-select form-select mr-2"
                {{ $time ? 'data-selected=' . $time['minute'] : 'disabled' }}>
            </select>

            <label for="{{ $dateName }}">
                <i class="fa fa-calendar datepicker-icon" aria-hidden="true"></i>
            </label>
        </div>
    </div>
</div>