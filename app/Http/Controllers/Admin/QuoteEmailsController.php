<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmailQuote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Infrastructure\Repositories\Admin\EmailQuoteRepository;

class QuoteEmailsController extends Controller
{
    protected $repo;

    public function __construct(EmailQuoteRepository $repo)
    {
        $this->repo = $repo;
    }

    public function show()
     {
         return view('vendor.backpack.quote-email', ['emails' => EmailQuote::all()]);
     }

     public function save(Request $request)
     {
         try {
             $this->repo->update($request->all());
         } catch (\Throwable $e) {

         }

         return response(['message' => 'ok'], 200);
     }

     public function delete($id)
     {
        $this->repo->delete($id);
     }
}
