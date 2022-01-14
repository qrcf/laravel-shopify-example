<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Store') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger mt-1">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form class="mt-1" method="POST" action="{{ route('store') }}" autocomplete="off">
                        @csrf

                        <div>
                            <x-label for="shopify_url" :value="__('Shopify Store URL')" />

                            <x-input id="shopify_url" class="block mt-1 w-full" type="text" name="shopify_url"
                                placeholder="example.myshopify.com" :value="old('shopify_url')" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-label for="shopify_api_key" :value="__('API Key')" />

                            <x-input id="shopify_api_key" class="block mt-1 w-full" type="text" name="shopify_api_key"
                                autocomplete="off" :value="old('shopify_api_key')" required />
                        </div>
                        <!-- Password -->
                        <div class="mt-4">
                            <x-label for="shopify_api_token" :value="__('API Password')" />

                            <x-input id="shopify_api_token" class="block mt-1 w-full" type="text"
                                name="shopify_api_token" autocomplete="new-password" required />
                        </div>
                        <div class="flex items-center mt-4">
                            <x-button onclick="window.location='{{ route('dashboard') }}'">
                                {{ __('Cancel') }}
                            </x-button>
                            <span style="flex-grow:1"></span>

                            <x-button class="ml-3" type="submit">
                                {{ __('Register store') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
