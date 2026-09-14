<div class="min-h-screen lg:grid lg:grid-cols-2">
    {{-- Left panel — decorative image (hidden on mobile) --}}
    <div class="hidden lg:relative lg:flex lg:flex-col lg:justify-end lg:items-start lg:p-12">
        {{-- Background image --}}
        <img
            src="{{ asset('images/auth-bg.jpeg') }}"
            alt=""
            class="absolute inset-0 h-full w-full object-cover brightness-50"
        />

        {{-- Gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>

        {{-- Branding text over the image --}}
        <div class="relative z-10 mb-8">
            <h1 class="font-display text-4xl font-bold text-white leading-relaxed">
                <!-- Use box-decoration-clone to keep the padding nice on line breaks -->
                <span class="bg-black/50 px-3 py-1 rounded box-decoration-clone">Cadê minha</span><br>
                <span class="bg-black/50 px-3 py-1 rounded box-decoration-clone inline-block">Araucária?</span>
            </h1>
            <p class="mt-4 text-lg text-araucaria-200/80 max-w-sm">
                <span class="bg-black/40 px-2 py-0.5 rounded box-decoration-clone">
                    Mapeando, monitorando e protegendo nossas araucárias, juntos. 🌲
                </span>
            </p>
            <p class="mt-3 text-sm text-araucaria-200/80 max-w-sm">
                <span class="bg-black/30 px-2 py-0.5 rounded">
                    Créditos foto: Monica Siqueira
                </span>
            </p>
        </div>
    </div>

    {{-- Right panel — form --}}
    <div class="flex min-h-screen flex-col items-center justify-center px-6 py-12 bg-white dark:bg-gray-900">
        <div class="w-full max-w-md">
            {{-- Logo --}}
            <div class="mb-8 flex justify-center">
                {{ $logo }}
            </div>

            {{-- Form content --}}
            {{ $slot }}
        </div>
    </div>
</div>
