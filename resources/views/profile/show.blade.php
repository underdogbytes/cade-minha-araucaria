<x-app-layout>
    @include('profile.partials.header', [ 'user' => Auth::user()])

    @include('profile.partials.settings')
</x-app-layout>
