<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Providers') }}
        </h2>
    </x-slot>

    <div class="container py-12">
        <div class="row">
            <div class="d-flex mt-5 mb-5 ">
                <div class="ms-auto">
                    <form action="{{ route('index') }}" method="GET">
                        @csrf
                        <label class="text-white" for="search">Search: </label>
                        <input type="text" name="search" id="search"></div>
                    </form>
            </div>
            @foreach ($providers as $provider)
                <div class="col-md-4 col-sm-6 col-12 mb-4 mt-5">
                    <div class="card text-center border border-primary shadow-lg text-white bg-dark">
                        <div class="card-body">
                            <h5 class="card-title">{{ $provider->company_name }}</h5>
                            <i>
                            <p class="card-text">{{ $provider->user->email }}</p>
                            @if($provider->user?->phone)
                            <p class="card-text">{{ $provider->user->phone }}</p>
                            @else
                            <p class="card-text">&nbsp;</p>
                            @endif
                            </i>
                            <a href="{{ route('booking.index', $provider->id) }}" class="btn btn-primary">Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    <div class="mt-5">
        {{ $providers->links() }}
    </div>
    </div>

</x-app-layout>
