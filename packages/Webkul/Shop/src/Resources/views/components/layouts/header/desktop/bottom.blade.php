{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.before') !!}

<!-- Main Header Bar -->
<div class="w-full bg-[#0f172a]">
    <div class="flex min-h-[70px] w-full items-center justify-between px-[60px] max-1180:px-8 gap-4">

        <!-- Logo -->
        <div class="flex-shrink-0">
            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}
            <a href="{{ route('shop.home.index') }}" aria-label="Xylavix">
                <img
                    src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                    width="260"
                    height="60"
                    alt="{{ config('app.name') }}"
                >
            </a>
            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}
        </div>

        <!-- Search Bar - Amazon Style -->
        <div class="flex-1 max-w-[600px]">
            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.before') !!}
            <form action="{{ route('shop.search.index') }}" class="flex w-full" role="search">
                <input
                    type="text"
                    name="query"
                    value="{{ request('query') }}"
                    class="flex-1 rounded-l-md border-none px-4 py-3 text-sm text-gray-900 outline-none"
                    style="background:white;"
                    minlength="{{ core()->getConfigData('catalog.products.search.min_query_length') }}"
                    maxlength="{{ core()->getConfigData('catalog.products.search.max_query_length') }}"
                    placeholder="Search products, brands and more..."
                    required
                >
                <button
                    type="submit"
                    class="rounded-r-md px-5 py-3"
                    style="background:#0072ff;"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </form>
            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.after') !!}
        </div>

        <!-- Right Icons -->
        <div class="flex items-center gap-x-6 flex-shrink-0">

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.compare.before') !!}

            <!-- Compare -->
            @if(core()->getConfigData('catalog.products.settings.compare_option'))
                <a href="{{ route('shop.compare.index') }}" class="flex flex-col items-center gap-0.5 group" aria-label="Compare">
                    <span class="icon-compare text-2xl text-white group-hover:text-[#00c6ff]" role="presentation"></span>
                    <span class="text-[10px] text-gray-400 group-hover:text-[#00c6ff]">Compare</span>
                </a>
            @endif

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.compare.after') !!}
            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.before') !!}

            <!-- Mini Cart -->
            @if(core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                <div class="flex flex-col items-center gap-0.5 group">
                    @include('shop::checkout.cart.mini-cart')
                    <span class="text-[10px] text-gray-400 group-hover:text-[#00c6ff]">Cart</span>
                </div>
            @endif

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.after') !!}
            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.before') !!}

            <!-- Profile -->
            <x-shop::dropdown position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                <x-slot:toggle>
                    <div class="flex flex-col items-center gap-0.5 group cursor-pointer">
                        <span class="icon-users text-2xl text-white group-hover:text-[#00c6ff]" role="button" aria-label="Profile" tabindex="0"></span>
                        <span class="text-[10px] text-gray-400 group-hover:text-[#00c6ff]">
                            @auth('customer') {{ auth()->guard('customer')->user()->first_name }} @else Account @endauth
                        </span>
                    </div>
                </x-slot>

                @guest('customer')
                    <x-slot:content>
                        <div class="grid gap-2.5">
                            <p class="font-dmserif text-xl">@lang('shop::app.components.layouts.header.welcome-guest')</p>
                            <p class="text-sm">@lang('shop::app.components.layouts.header.dropdown-text')</p>
                        </div>
                        <p class="mt-3 w-full border border-zinc-200"></p>
                        <div class="mt-6 flex gap-4">
                            <a href="{{ route('shop.customer.session.create') }}" class="primary-button m-0 mx-auto block w-max rounded-2xl px-7 text-center text-base ltr:ml-0 rtl:mr-0">
                                @lang('shop::app.components.layouts.header.sign-in')
                            </a>
                            <a href="{{ route('shop.customers.register.index') }}" class="secondary-button m-0 mx-auto block w-max rounded-2xl border-2 px-7 text-center text-base ltr:ml-0 rtl:mr-0">
                                @lang('shop::app.components.layouts.header.sign-up')
                            </a>
                        </div>
                    </x-slot>
                @endguest

                @auth('customer')
                    <x-slot:content class="!p-0">
                        <div class="grid gap-2.5 p-5 pb-0">
                            <p class="font-dmserif text-xl">@lang('shop::app.components.layouts.header.welcome')' {{ auth()->guard('customer')->user()->first_name }}</p>
                            <p class="text-sm">@lang('shop::app.components.layouts.header.dropdown-text')</p>
                        </div>
                        <p class="mt-3 w-full border border-zinc-200"></p>
                        <div class="mt-2.5 grid gap-1 pb-2.5">
                            <a class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100" href="{{ route('shop.customers.account.profile.index') }}">@lang('shop::app.components.layouts.header.profile')</a>
                            <a class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100" href="{{ route('shop.customers.account.orders.index') }}">@lang('shop::app.components.layouts.header.orders')</a>
                            @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                <a class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100" href="{{ route('shop.customers.account.wishlist.index') }}">@lang('shop::app.components.layouts.header.wishlist')</a>
                            @endif
                            @auth('customer')
                                <x-shop::form method="DELETE" action="{{ route('shop.customer.session.destroy') }}" id="customerLogout" />
                                <a class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100" href="{{ route('shop.customer.session.destroy') }}" onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                                    @lang('shop::app.components.layouts.header.logout')
                                </a>
                            @endauth
                        </div>
                    </x-slot>
                @endauth
            </x-shop::dropdown>

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.after') !!}
        </div>
    </div>

    <!-- Category Menu Bar - Amazon Style -->
    <div class="w-full bg-[#1e293b] px-[60px] max-1180:px-8">
        <v-desktop-category>
            <div class="flex items-center gap-5 py-2">
                <span class="shimmer h-5 w-16 rounded" role="presentation"></span>
                <span class="shimmer h-5 w-16 rounded" role="presentation"></span>
                <span class="shimmer h-5 w-16 rounded" role="presentation"></span>
            </div>
        </v-desktop-category>
    </div>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-desktop-category-template">
        <div class="flex items-center gap-1 py-1.5" v-if="isLoading">
            <span class="shimmer h-5 w-16 rounded" role="presentation"></span>
            <span class="shimmer h-5 w-16 rounded" role="presentation"></span>
            <span class="shimmer h-5 w-16 rounded" role="presentation"></span>
        </div>

        <div class="flex items-center" v-else>
            <div
                class="group relative flex h-[44px] items-center px-1"
                v-for="category in categories"
            >
                
                    :href="category.url"
                    class="inline-block px-4 py-1 text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 rounded transition-colors"
                >
                    @{{ category.name }}
                </a>

                <div
                    class="pointer-events-none absolute top-[44px] z-[1] max-h-[580px] w-max max-w-[1260px] translate-y-1 overflow-auto border border-[#1e293b] bg-[#0f172a] p-9 opacity-0 shadow-[0_6px_6px_1px_rgba(0,0,0,.5)] transition duration-300 ease-out group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100 ltr:-left-2 rtl:-right-2"
                    v-if="category.children.length"
                >
                    <div class="flex justify-between gap-x-[70px]">
                        <div
                            class="grid w-full min-w-max max-w-[150px] flex-auto grid-cols-[1fr] content-start gap-5"
                            v-for="pairCategoryChildren in pairCategoryChildren(category)"
                        >
                            <template v-for="secondLevelCategory in pairCategoryChildren">
                                <p class="font-medium text-white">
                                    <a :href="secondLevelCategory.url" class="hover:text-[#00c6ff]">
                                        @{{ secondLevelCategory.name }}
                                    </a>
                                </p>
                                <ul class="grid grid-cols-[1fr] gap-3" v-if="secondLevelCategory.children.length">
                                    <li class="text-sm font-medium text-gray-400" v-for="thirdLevelCategory in secondLevelCategory.children">
                                        <a :href="thirdLevelCategory.url" class="hover:text-[#00c6ff]">
                                            @{{ thirdLevelCategory.name }}
                                        </a>
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-desktop-category', {
            template: '#v-desktop-category-template',
            data() {
                return { isLoading: true, categories: [] }
            },
            mounted() { this.get(); },
            methods: {
                get() {
                    this.$axios.get("{{ route('shop.api.categories.tree') }}")
                        .then(response => {
                            this.isLoading = false;
                            this.categories = response.data.data;
                        }).catch(error => console.log(error));
                },
                pairCategoryChildren(category) {
                    return category.children.reduce((result, value, index, array) => {
                        if (index % 2 === 0) result.push(array.slice(index, index + 2));
                        return result;
                    }, []);
                }
            },
        });
    </script>
@endPushOnce

{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}