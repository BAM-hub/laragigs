@props(['company'])
<li
    class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
>
    <a href="/?company={{ $company->id }}">
      {{ $company->company_name}}
    </a>
</li>