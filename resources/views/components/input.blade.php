@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-araucaria-500 dark:focus:border-araucaria-600 focus:ring-araucaria-500 dark:focus:ring-araucaria-600 rounded-lg shadow-sm']) !!}>
