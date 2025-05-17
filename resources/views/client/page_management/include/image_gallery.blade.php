<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="border p-4 rounded">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="mb-0">{{ $title }}</h5>
                    </div>
                    <hr>
                    <form method="POST" action="{{ route('user.page.save') }}" class="row g-3 ajaxForm" novalidate="novalidate">
                        @csrf
                        <div class="col-md-12">
                            <label for="">Title<span class="text-danger fw-bold">*</span></label>
                            <input type="text" name="title" id="page_title" value="{{ $edit->title ?? "" }}" placeholder="Enter Page Title..."
                                class="form-control" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="images">Image Gallery<span class="text-danger">*</span></label>
                            <p style="font-size: 11px;">Add images by clicking on this input below</p>
                            <input type="file" name="pages_images[]" id="pages_images" accept="image/*" class="form-control" multiple>
                        </div>
                        <div class="col-md-12">
                            <div id="image-preview-container" class="image-preview-container">
                                @if(isset($edit->galleries) && !empty($edit->galleries))
                                    @foreach($edit->galleries as $index => $image)
                                        <div class="image-preview">
                                            <img src="{{ asset($image->images) }}" alt="Image Preview">
                                            <button class="delete-icon" onclick="removeExistingImage({{ $index }})">&times;</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <input type="hidden" name="edit_id" value="{{ $edit->id ?? ""}}">
                        <div class="form-group mb-3 text-right">
                            <button type="submit" class="btn btn-primary px-5 form-submit-btn col-md-12">{{ isset($edit) ? 'Update' : 'Create' }} Page</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('page-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script>

        $(document).ready(function() {
            $('#pages_images').on('change', function(event) {
                const files = event.target.files;
                const $container = $('#image-preview-container');
                $container.empty(); // Clear previous previews

                $.each(files, function(index, file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const $div = $('<div>', { class: 'image-preview' });

                        const $img = $('<img>', { src: e.target.result });

                        const $button = $('<button>', {
                            class: 'delete-icon',
                            html: '&times;',
                            click: function() {
                                $div.remove();
                                removeFile(index);
                            }
                        });

                        $div.append($img).append($button);
                        $container.append($div);
                    };

                    reader.readAsDataURL(file);
                });
            });

            function removeFile(index) {
                const input = $('#pages_images')[0];
                const dt = new DataTransfer();

                const { files } = input;
                for (let i = 0; i < files.length; i++) {
                    if (i !== index) {
                        dt.items.add(files[i]);
                    }
                }

                input.files = dt.files;
            }

            function removeExistingImage(index) {
                // Implement logic to remove existing image from the database or update form data
                // For example, update hidden input or make an AJAX request to update database
                // This function assumes you handle the backend logic for deleting the image
                console.log('Removing existing image at index:', index);
                // Optionally, you can remove the preview from the DOM
                // $(`.image-preview:eq(${index})`).remove();
            }
        });

        validations = $(".ajaxForm").validate();
        $('.ajaxForm').submit(function(e) {
            e.preventDefault();
            validations = $(".ajaxForm").validate();
            if (validations.errorList.length != 0) {
                return false;
            }
            var url = $(this).attr('action');
            var param = new FormData(this);
            my_ajax(url, param, 'post', function(res) {

            },true);
        });
    </script>
@endsection