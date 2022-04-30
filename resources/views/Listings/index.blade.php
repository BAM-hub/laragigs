<x-layout>
@include('partials._hero')
@include('partials._search')


  @unless (count($listings) != 0)
  <p>No Listings Found</p>
  @endunless
  
  @foreach($listings as $listing)
    <x-listing-card :listing="$listing" />
  @endforeach
</x-layout>