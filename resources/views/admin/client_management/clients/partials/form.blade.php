@push('scripts')
    <script>
        $(document).ready(function() {
            $('.ajaxForm').submit(function(e) {
                e.preventDefault();
                var url = $(this).attr('action');
                var param = new FormData(this);
                var files = $('#profile_image')[0].files[0] ?? '';
                param.append('profile_image', files);
                my_ajax(url, param, 'post', function(res) {

                }, true);
            });
        });

        $(document).on('change', '#agency_address', function () {
            var url = "{{ route('admin.sub_account.client-management.get_agency_address', ['sub_account_id' => $sub_account_id ]) }}"
            var agency_id = $(this).val();

            $.ajax({
                url: url,
                method: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    agency_id : agency_id,
                },
                dataType: "json",
                success: function (response) {
                    if (response['success'] !== undefined && response['address'] !== undefined) {
                        $('#address').val("");
                        $('#address').val(response.address);
                    }
                    
                },
                error: function(xhr, status, error) {
                    $('#address').val("");
                    console.error(xhr.responseText);
                }
            });
        });
    </script>
@endpush

<input type="hidden" value="1" name="sub_account_id">

<div class="col-md-6">
    <label for="inputName" class="form-label">Client Name <span
        class="text-danger fw-bold">*</span></label>
    <input type="text" name="client_name" id="client_name"
        value="{{ $edit->client_name ?? '' }}" placeholder="Enter Name"
        class="form-control" required
        oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');">
</div>

<div class="col-md-6">
    <label for="inputName" class="form-label">Mobile <span
        class="text-danger fw-bold">*</span></label>
    <input type="text" name="phone_number" id="name"
        value="{{ $edit->phone_number ?? '' }}" placeholder="Enter Number"
        class="form-control" required
        oninput="this.value = this.value.replace(/[^0-9]/g, '');"
        max="12">
</div>

<div class="col-md-6">
    <label for="inputName" class="form-label">Agency <span
        class="text-danger fw-bold">*</span></label>
    <select class="form-control" name="agency_id" id="agency_address" required>
        <option value="" disabled selected>Select Agency</option>
        @foreach ($agencies as $agency)
            <option value="{{ $agency->id }}"
                @if (isset($edit) && $edit->agency_id == $agency->id) selected @endif>
                {{ $agency->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-6">
    <label for="inputName" class="form-label">Package <span
            class="text-danger fw-bold">*</span></label>
    <select name="package" class="form-control">
        <option value="Package 1"
            @if (isset($edit) && $edit->package == 1) selected @endif>
            Package 1
        </option>
        <option value="Package 2"
            @if (isset($edit) && $edit->package == 2) selected @endif>
            Package 2
        </option>
        <option value="Package 3"
            @if (isset($edit) && $edit->package == 3) selected @endif>
            Package 3
        </option>
    </select>
</div>

<div class="col-md-6">
    <label for="inputName" class="form-label">Email <span
        class="text-danger fw-bold">*</span></label>
    <input type="email" name="email" id="email"
        value="{{ $edit->email ?? '' }}" placeholder="Enter email"
        class="form-control"
        @if (isset($edit)) readonly @endif required>
</div>

<div class="col-md-6">
    <label for="inputName" class="form-label">Address <span
        class="text-danger fw-bold">*</span></label>
    <input type="text" name="address" id="address"
        value="{{ $edit->address ?? '' }}" placeholder="Enter address"
        class="form-control" required>
</div>

<div class="col-12 col-lg-6">
    <label for="inputName" class="form-label">Industry <span
        class="text-danger fw-bold">*</span></label>
    <div class="ms-auto position-relative">
        <select name="industry_id" id="industry" class="form-control" required>
            <option value="">Select Industry</option>
            @foreach ($industries as $industry)
                <option value="{{ $industry->id }}"
                    @if (isset($edit) && $edit->industry_id == $industry->id) selected @endif>
                    {{ $industry->industries }}
                </option>
            @endforeach
        </select>
    </div>
    @error('agency')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div class="col-6">
    <label for="inputName" class="form-label">Professional Image (Image Type jpg,jpeg,png)</span></label>
    <input type="file" id="profile_image" name="image" class="form-control" accept=".jpg, .png, .jpeg">
</div>

@if (!isset($edit) && empty($edit))
    <div class="col-md-6">
        <label for="inputName" class="form-label">Password<span
            class="text-danger fw-bold"> *</span></label>
        <div>
            <small>Default password: P@$$word!</small>
        </div>
        <input type="password" value="P@$$word!" name="password"
            class="form-control" required>
    </div>

    <div class="col-md-6 d-flex flex-column justify-content-between">
        <label class="form-label">Confirm Password<span class="text-danger fw-bold"> *</span></label>
        <input type="password" value="P@$$word!" name="confirm_password"
            class="form-control mt-auto" required>
    </div>
@endif

<div class="form-group mb-3 text-right">
    <input type="hidden" name="id" value="{{ $edit->hashid ?? '' }}">
    <button type="submit" class="btn btn-primary px-5 form-submit-btn">Submit</button>
</div>