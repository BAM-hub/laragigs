<?php

use App\Http\Controllers\ListingController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Models\Listing;
use Illuminate\View\View;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//All listings
Route::get('/', [ListingController::class, 'index']);

//Single Listing
@Route::get('/listing/{listing}', [ListingController::class, 'show']);