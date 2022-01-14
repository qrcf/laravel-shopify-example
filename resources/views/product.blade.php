<x-app-layout>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">


    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Stores / {{ $store->name }} / {{ $product['title'] }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center">
                <form class="" role="search" method="get"
                    action="{{ route('store-dashboard', ['id' => $store->id]) }}">
                    <x-button type="submit">
                        Back
                    </x-button>
                </form>
                <span style="flex-grow:1"></span>

                <form method="post" method="POST"
                    action="{{ route('save-product', ['id' => $store->id, 'product_id' => $product['id']]) }}">
                    @csrf


                    <input class="save-field" type="hidden" name="title" value="{{ $product['title'] }}">
                    <input class="save-field" type="hidden" name="body_html" value="{{ $product['body_html'] }}">

                    <x-button id="save-btn" type="submit" disabled="disabled">
                        Save
                    </x-button>
                </form>
                <form role="search" method="get"
                    action="https://{{ $store->shopify_url }}/admin/products/{{ $product['id'] }}" target="_blank"
                    class="ml-2">
                    <x-button type="submit">
                        Shopify
                    </x-button>
                </form>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-2 product-card">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($errors->any())
                        <div class="alert alert-danger mt-1">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <input type="text" name="title" value="{{ $product['title'] }}" style="width: 60%;">
                    <div class="flex items-center mt-2">
                        @if ($product['image'])
                            <img src="{{ $product['image']['src'] }}" class="product-image">
                        @else
                            <img src="{{ URL::asset('/img/product_placeholder.png') }}" class="product-image">
                        @endif

                        <div class="body">
                            <textarea name="body_html" style="width:100%" rows="15"
                                cols="100">{{ $product['body_html'] }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <br />
            Variants ({{ count($product['variants']) }})

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-2 product-card">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div class="flex items-center">
                        <table>
                            <tr>
                                @foreach ($product['options'] as $o) <th class="variant-column">{{ $o['name'] }}</th> @endforeach
                                <th class="variant-column">SKU</th>
                                <th class="variant-column">Price</th>
                            </tr>
                            @foreach ($product['variants'] as $v)
                                <tr class="product-variant">
                                    @foreach ($product['options'] as $o)
                                        <td class="variant-column">{{ $v['option' . ($loop->index + 1)] }}</td>
                                    @endforeach
                                    <td class="variant-column">{{ $v['sku'] }}</td>
                                    <td class="variant-column">${{ $v['price'] }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
<script>
    var product = <?php echo json_encode($product); ?>;

    var bad_fields = {};

    function check_bad() {
        var is_bad = false;

        for (const [key, value] of Object.entries(bad_fields)) {
            if (value != false) {
                is_bad = true;
            }
        }
        if (is_bad) {
            $("#save-btn").prop("disabled", false);

        } else {
            $("#save-btn").prop("disabled", true);

        }
    }

    $("textarea").keyup(function() {

        if (product[$(this).attr("name")] != $(this).val()) {
            bad_fields[$(this).attr("name")] = $(this).val();
            $("input.save-field[name='" + $(this).attr("name") + "']").val($(this).val());

        } else {
            bad_fields[$(this).attr("name")] = false;

        }
        check_bad();

    });

    $("input").keyup(function() {

        if (product[$(this).attr("name")] != $(this).val()) {
            bad_fields[$(this).attr("name")] = $(this).val();
            $("input.save-field[name='" + $(this).attr("name") + "']").val($(this).val());

        } else {
            bad_fields[$(this).attr("name")] = false;

        }
        check_bad();

    });
</script>
