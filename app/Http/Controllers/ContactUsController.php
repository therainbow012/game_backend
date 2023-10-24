<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function index(Request $request) {
        $contactData = new Contact;

        $contactData = $contactData->orderBy('id', 'DESC')->paginate(env('ITEMS_PER_PAGE'));
        return view('contact.index',
        [
            'contactData' => $contactData,
            'searchData' => $request->search,
            'uri' => \Request::route()->uri
       ]);
    }
}
