<x-app-layout>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/store.css') }}">


    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Stores / {{ $store->name }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form class="mr-2" role="search" method="get" action="{{ route('dashboard') }}">
                <x-button type="submit">
                    Back
                </x-button>
            </form>
            @foreach ($products as $p)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-2 product-card">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center ">
                            @if ($p['image'])
                                <img src="{{ $p['image']['src'] }}" class="product-image">
                            @else
                                <img src="{{ URL::asset('/img/product_placeholder.png') }}" class="product-image">
                            @endif
                            <div>
                                {{ $p['title'] }}
                            </div>
                            <span style="flex-grow:1"></span>
                            <form class="mr-2" role="search" method="get"
                                action="{{ route('store-product', ['id' => $store->id, 'product_id' => $p['id']]) }}">
                                <x-button type="submit">
                                    Details
                                </x-button>
                            </form>
                            <form role="search" method="get"
                                action="https://{{ $store->shopify_url }}/admin/products/{{ $p['id'] }}"
                                target="_blank">
                                <x-button type="submit">
                                    Shopify
                                </x-button>
                            </form>

                        </div>
                    </div>

                </div>

            @endforeach


        </div>

    </div>
</x-app-layout>
<script>
    // $(".product-card").hover(
    //   function() {
    //     $( this ).find(".product-variants").removeClass("hidden-area");
    //   }, function() {
    //     $( this ).find(".product-variants").addClass("hidden-area");
    //   }
    // );
</script>
