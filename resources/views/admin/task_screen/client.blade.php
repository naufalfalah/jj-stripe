<div class="row mb-3 client-section">
    <div class="col-xl-3 col-md-6 task-client-col">
        <div class="client-title"><i class="fas fa-user"></i> {{ __('Clent') }}</div>
    </div>
    <div class="col-xl-9 col-md-6 task-client-col">
        <div class="client-detail">
            <select id="task-client" name="client" class="task-client" @disabled(auth()->user()->role_name != 'Manager')>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ ucwords($client->client_name) }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
