<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    @foreach ($observations ?? [] as $item)
    <a href="{{ route('observations.show', $item->id) }}" class="block">
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm mb-4"
            style="max-width: 20rem; height: auto">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div>
                    <img
                        src="{{ $item->photo_path }}"
                        alt=""
                        style="width: 20rem; height: auto; object-fit: cover;"
                    />
                    <div class="mt-2">
                        <span
                            class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $item->observed_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @endforeach
</div>