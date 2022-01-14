<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex items-center">
                    <div class="p-6 bg-white border-b border-gray-200">
                        You're logged in, {{ $user->name }}! <br />API Token: {{ $user->api_token }}
                    </div>
                    <span style="flex-grow:1"> </span>
                    <form method="POST" action="/refresh-token">
                        @csrf

                        <input type="hidden" name="_method" value="POST">

                        <x-button type="submit" class="mr-2">
                            Refresh Token
                        </x-button>
                    </form>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <br />
            <div class="text-lg">Stores</div>
            @foreach ($stores as $store)

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-2" style="cursor:pointer;"
                    onclick="window.location='{{ route('store-dashboard', ['id' => $store->id]) }}'">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center ">
                            <div>
                                {{ $store->name }} - {{ $store->shopify_url }}
                            </div>
                            <span style="flex-grow:1"></span>
                            <x-button type="button"
                                onclick="window.location='{{ route('store-dashboard', ['id' => $store->id]) }}'"
                                class="ml-2">
                                Products
                            </x-button>
                            <div class="flex-end">
                                <form method="POST" action="/remove-store">
                                    @csrf

                                    <input type="hidden" name="shopify_store_id" value="{{ $store->id }}">
                                    <input type="hidden" name="_method" value="POST">

                                    <x-button type="submit" class="ml-2">
                                        X
                                    </x-button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach
            <div class="flex items-center justify-end mt-2">

                <x-button type="button" onclick="window.location='{{ route('register-store') }}'">
                    Add Store
                </x-button>
            </div>
        </div>

    </div>
</x-app-layout>
