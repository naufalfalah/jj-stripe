<div class="row mb-2">
    <div class="col-xl-6">
        <div class="form-group mb-2">
            <label for="ad_request_id" class="form-label">Ad Request
                <span class="text-danger fw-bold">*</span></label>
            <select class="form-select" name="ad_request_id" required>
                <option value="" selected>Select an ad request</option>
                @foreach ($adsRequests as $adsRequest)
                    <option value="{{ $adsRequest->id }}" 
                        data-client-id="{{ $adsRequest->client_id }}"
                        {{ old('ad_request_id') == $adsRequest->id ? 'selected' : '' }}>
                        {{ $adsRequest->adds_title }} - {{ $adsRequest->client->client_name }} - {{ $adsRequest->client->customer_id }}</option>
                @endforeach
            </select>
            @error('ad_request_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>