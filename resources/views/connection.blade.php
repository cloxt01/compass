@extends('layouts.app')

@section('title', 'Connections')

@section('content')
    <div class="max-w-xl mx-auto py-10">

        <div class="bg-[#161b22] border border-[#30363d] rounded-lg p-6">

            <h2 class="text-xl font-semibold text-white">
                Platform Connections
            </h2>

            <p class="text-gray-400 text-sm mt-1">
                Pilih platform yang ingin Anda kelola.
            </p>

            <div class="mt-6">
                <label class="block text-sm text-gray-400 mb-2">
                    Platform
                </label>

                <select
                    id="provider"
                    class="w-full rounded-md bg-[#0d1117] border border-[#30363d] px-3 py-2 text-white"
                >
                    @foreach($providers as $provider)
                        <option
                            value="{{ route('connection.'. $provider['key']) }}"
                            data-name="{{ $provider['name'] }}"
                            data-description="{{ $provider['description'] }}"
                            data-status="{{ $provider['connected'] ? 'Connected' : 'Disconnected' }}"
                        >
                            {{ $provider['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-6 rounded-md border border-[#30363d] p-4">

                <h3 id="providerName" class="font-semibold text-white"></h3>

                <p id="providerDescription" class="text-sm text-gray-400 mt-2"></p>

                <div class="mt-4">
                <span id="providerStatus"
                      class="inline-flex rounded-full px-2 py-1 text-xs">
                </span>
                </div>

            </div>

            <button
                id="continueBtn"
                class="mt-6 w-full rounded-md bg-blue-600 hover:bg-blue-700 py-2 text-white"
            >
                Continue
            </button>

        </div>

    </div>

    <script>
        const select = document.getElementById('provider');
        const name = document.getElementById('providerName');
        const description = document.getElementById('providerDescription');
        const status = document.getElementById('providerStatus');
        const button = document.getElementById('continueBtn');

        function refresh() {
            const option = select.options[select.selectedIndex];

            name.textContent = option.dataset.name;
            description.textContent = option.dataset.description;

            status.textContent = option.dataset.status;

            if (option.dataset.status === 'Connected') {
                status.className =
                    'inline-flex rounded-full bg-green-600/20 text-green-400 border border-green-600/40 px-2 py-1 text-xs';
            } else {
                status.className =
                    'inline-flex rounded-full bg-gray-700 text-gray-300 border border-gray-600 px-2 py-1 text-xs';
            }

            button.onclick = () => {
                window.location.href = option.value;
            };
        }

        refresh();
        select.addEventListener('change', refresh);
    </script>
@endsection
