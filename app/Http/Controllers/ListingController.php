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
        // dd($listings->items);
        // $ids = [];
        // foreach($listings as $listing) {
        //   if(!in_array($listing->company_id, $ids)) {
        //     array_push($ids, $listing->company_id);
          
        //   }
        //   // dd($listing);
        // }
        $companies = Company::all();
        // $companies = Company::whereIn('id', $ids)->get();
        // dd($companies);
        foreach($listings as $listing) {
          foreach($companies as $company) {
            if($company->id == $listing->company_id)
              $listing['company'] = $company->company_name;
          }
        }
      
        //dd($ids);
        // dd($companies);
        // dd($listings);

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
        // 'company' => ['required', Rule::unique(
        //     'listings',
        //     'company'
        //     )
        // ],
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
      // $formFields['company'] = User::find(auth()->id())->company;

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
        // 'company' => 'required',
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

      // this method is giving me a warrning but it still works
      // dd(auth()->user()->listings()->get());
      
      // found this method with tinker will use it to hide the warrning
      $user = User::find(auth()->user()->id);
      
      return view('listings.manage',[
        'listings' => $user->listings
      ]);
    }
}