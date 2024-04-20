<div class="d-flex" style="flex-direction: column; flex:auto">
    <label id="file-drop-area" style="cursor: pointer" for="file" ondragover="handleDragOver(event)"
        ondragleave="handleDragLeave(event)">
        <i class="fa fa-arrow-circle-up" aria-hidden="true" id="upload-icon"></i>
        <p class="mt-4">Kéo thả hoặc tải file lên</p>
    </label>
    <input type="file" id="file" name="{{ $fileName }}" {{ $imageOnly ? 'accept="image/*"' : '' }}
        onchange="excuteFile(this)">
    <input type="hidden" name="isDeleted" id="isDeleted" value="false">
    <div id='file-container'>
        <div class="img-container">
            <i class=" fa fa-times" id="remove-icon" aria-hidden="true"></i>
        </div>
    </div>
    <p class="warning-msg" style="display: none">Yêu cầu tải lên tệp tin</p>
</div>


<script>
    @if (!is_null($filePath))
        var path = "{{ $filePath }}"
        var fileName = "{{ basename($filePath) }}"
    @endif

    @if(isset($require))
        var require = true
    @endif
</script>
