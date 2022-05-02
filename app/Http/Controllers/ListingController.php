<?php

namespace App\Http\Controllers;


use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    // show all listings
    public function index() {
        return view('listings.index', [
          'listings' => Listing::latest()->filter(
            request(['tag', 'search'])
            )->paginate(6)
        ]);
    }
    
    // show single listing
    public function show(Listing $listing) {
        return view('listings.show', [
          'listings' => $listing
        ]); 
    }

    //show create form
    public function create() {
      return view('listings.create');
    }

    // store listing data
    public function store(Request $request) {
      $formFields = $request->validate([
        'title' => 'required',
        'company' => ['required', Rule::unique(
            'listings',
            'company'
            )
        ],
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
        'company' => 'required',
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
      $user = \App\Models\User::find(auth()->user()->id);
      
      return view('listings.manage',[
        'listings' => $user->listings
      ]);
    }
}