<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>


# LaraGigs
Laragigs a website for posting jobs aka gigs built with laravel.
The Project is based on a youtube tutorial by Traversy Media I made Changes to the project that I will discuss later. 

## :man_technologist: Built With
* Laravel
* Blade
* MySQL 
* BootStrap

<br/>

## To Try this App you need to:

1. Clone This Project Repo.
2. In your Terminal run ```composer install ```.
3. Create a .env file in the base directory.
4. Copy .env.example file to .env .
5. Open your .env file and change the database name (DB_DATABASE) to whatever you have, username (DB_USERNAME) and password (DB_PASSWORD) field correspond to your configuration.
6. Run the following commands:
```shell
  php artisan key:generate
  php artisan migrate 
  php artisan serve
```
7. Go to http://localhost:8000/
<br/>

## App Preview
![laragigs](https://user-images.githubusercontent.com/78625404/175166679-5300724a-9bd9-438c-8e32-652fbb3808d3.png)

<br />

## My Changes
1. Added Query Sort By Company
```blade
@props(['company'])
<li
    class="flex items-center justify-center bg-black text-white rounded-xl py-1 px-3 mr-2 text-xs"
>
    <a href="/?company={{ $company->id }}">
      {{ $company->company_name}}
    </a>
</li> 
```
2. Added Tags to sort listings by company.
```blade
<ul class="flex mt-4 ml-4">
  @foreach($companies as $company)
    <x-all-companies :company="$company"/>
  @endforeach
</ul>
```
3. Removed The ability To change the company name. 
4. Changed Key value pairs. reason updated the database relationship schema.
```blade
     <div class="text-xl font-bold mb-4">{{ $company->company_name }}</div>
      .
      .
      .
    @if ( auth()->user() != null && auth()->user()->id == $listings->user_id)        
```
5. Hide the register button for signed useres
```blade
        @auth
        @else
            <a
                href="/register"
                class="inline-block border-2 border-white text-white py-2 px-4 rounded-xl uppercase mt-2 hover:text-black hover:border-black"
                >Sign Up to List a Gig</a
            >
        @endauth
```
6. Updated the register fields.
```blade
    <label
      for="company"
      class="inline-block text-lg mb-2"
      >Company Name</label
    >
        <input
            type="text"
            class="border border-gray-200 rounded p-2 w-full"
            name="company"
            value="{{old('company')}}"
            placeholder="Leave Empty if your only looking for a job"
        />
        @error('company')
        <p class="text-red-500 text-xs mt-1">
            {{ $message }}
        </p>
        @enderror
    <div class="mb-6 mt-6">
```
7. changed seeders to controll genrated data.
```php
    use App\Models\Company;
    
    $company = Company::factory(2)->create();
    $user = User::factory()->create([
      'name' => 'Jhon Doe',
      'email' => 'jhon@gmail.com',
      'company_id' => $company->id
    ]);
    Listing::factory(6)->create([
      'user_id' => $user->id,
      'company_id' => $company->id
    ]);
```
8. Normalization
```php
  // in the database/migrations/2022_04_29_223742_create_listings_table.php 
  $table->foreignId('company_id');
  // added database/migrations/2022_05_03_174038_create_company_table.php
  <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}; 

  // in the database/migrations/2014_10_12_000000_create_users_table.php
  // company id for user is nullable if only he/she wants to only see listings
  $table->string('company_id')->nullable();
```
9. Added Relationships
```php
  // app/Models/Company.php
  <?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // relationship with user
    public function user() {
        return $this->hasMany(User::class, 'company_id');
    }
    public function listing() {
        return $this->hasMany(Listing::class, 'company_id');
    }
} 

  // It's factory
  return [
    'company_name' => 'joTek'
   ];
  // app/Models/Listing.php
  // Company Filter
  if($filters['company'] ?? false) {
    $query->where('company_id', '=', request('company'));
  }
  // relationship
   public function company() {
     return $this->belongsTo(Company::class, 'comapny_id');
  }
```
10. Changed The register Logic.
```php
  use App\Models\Company;
    public function store(Request $request) {
      $company_name = $request->company;
      if(!$company_name == null){

        $company = Company::where('company_name', '=', $company_name)
        ->get();

        if(count($company) == 0) {
          $company = Company::create([
            'company_name' => $company_name
          ]);
          $company = $company->id;
        } else {
          $company = $company[0]->id;
        }
      }

      $formFields = $request->validate([
        'name' => ['required', 'min:3'],
        'company_id' => ['nullable'],
        'email' => ['required', 'email', Rule::unique('users', 'email')],
        'password' => 'required|confirmed|min:6'
      ]);

      // Hash Password
      $formFields['password'] = bcrypt($formFields['password']);

      $formFields['company_id'] = $company_name != null ? $company : null;
      // crete user
      $user = User::create($formFields);
```
11. Sceham Changes
```php
<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Company;
use App\Models\Listing;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    // show all listings
    public function index() {

        $listings = Listing::latest()->filter(
          request(['tag', 'search', 'company'])
        )->paginate(6);
        
        $companies = Company::all();
        foreach($listings as $listing) {
          foreach($companies as $company) {
            if($company->id == $listing->company_id)
              $listing['company'] = $company->company_name;
          }
        }
        return view('listings.index', [
          'listings' => $listings,
          'companies' => $companies
        ]);
    }
    
    // show single listing
    public function show(Listing $listing) {
        $company = Company::find($listing->company_id);
        return view('listings.show', [
          'listings' => $listing,
          'company' => $company
        ]); 
    }

    //show create form
    public function create() {
      if(User::find(auth()->id())->company == null) {
        return back()->with('message', 'You can\'t post a gig unless you are a company');
      }

      return view('listings.create');
    }

    // store listing data
    public function store(Request $request) {
      $formFields = $request->validate([
        'title' => 'required',
        'location' => 'required',
        'website' => 'required',
        'email' => ['required', 'email'],
        'tags' => 'required',
        'description' => 'required'
      ]);

      if($request->hasFile('logo')) {
        $formFields['logo'] = $request->file('logo')->store(
          'logos', 'public'
        );
      }

      $formFields['company_id'] = auth()->user()->company_id;
      $formFields['user_id'] = auth()->id();

      Listing::create($formFields);

      return redirect('/')->with(
        'message',
        'Listing Created successfully!'
      );
    }
    
    // show edit form
    public function edit(Listing $listing) {
      return view('listings.edit', ['listing' => $listing]);
    }

     // update listing data
    public function update(Request $request, Listing $listing) {
      
      // make sure user is owner
      if($listing->user_id != auth()->id()) {
        abort(403, 'Un Authoraized Action');
      }

      $formFields = $request->validate([
        'title' => 'required',
        'location' => 'required',
        'website' => 'required',
        'email' => ['required', 'email'],
        'tags' => 'required',
        'description' => 'required'
      ]);

      if($request->hasFile('logo')) {
        $formFields['logo'] = $request->file('logo')->store(
          'logos', 'public'
        );
      }
      $listing->update($formFields);
      return back()->with('message', 'Listing Updated successfully');
    }

    // delete listing
    public function destroy(Listing $listing) {
      if($listing->user_id != auth()->id()) {
        abort(403, 'Un Authoraized Action');
      }
      $listing->delete();
      return redirect('/')->with('message', 'Listing Deleted successfully');
    }

    // manage listing
    public function manage() {      
      // found this method with tinker will use it to hide the warrning
      $user = User::find(auth()->user()->id);
      
      return view('listings.manage',[
        'listings' => $user->listings
      ]);
    }
}
    
```

## :fire: The Goal
**Deploy The APP**

## Acknowledgements
[Traversy Media](https://www.youtube.com/watch?v=MYyJ4PuL4pY&t=7021s)
