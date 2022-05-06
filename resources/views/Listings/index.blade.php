<x-layout>
@include('partials._hero')
@include('partials._search')

<div class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4">
  @unless (count($listings) != 0)
  <p>No Listings Found</p>
  @endunless
  
  @foreach($listings as $listing)
    <x-listing-card :listing="$listing" />
  @endforeach
</div>

<ul class="flex mt-4 ml-4">
  @foreach($companies as $company)
    <x-all-companies :company="$company"/>
  @endforeach
</ul>

<div class="mt-6 p-4">
  {{ $listings->links() }}
</div>
</x-layout>