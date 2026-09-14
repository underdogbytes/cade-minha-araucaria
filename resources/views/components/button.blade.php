<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-araucaria-700 dark:bg-araucaria-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-araucaria-800 dark:hover:bg-araucaria-500 focus:bg-araucaria-800 dark:focus:bg-araucaria-500 active:bg-araucaria-900 dark:active:bg-araucaria-700 focus:outline-none focus:ring-2 focus:ring-araucaria-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
