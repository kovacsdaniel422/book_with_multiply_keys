<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Copy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CopyController extends Controller
{
    //
    public function index(){
        $copies =  Copy::all();
        return $copies;
    }
    
    public function show($id)
    {
        $copies = Copy::find($id);
        return $copies;
    }
    public function destroy($id)
    {
        Copy::find($id)->delete();
    }
    public function store(Request $request)
    {
        $copy = new Copy();
        $copy->book_id = $request->book_id;
        $copy->hardcovered = $request->hardcovered;
        $copy->publication = $request->publication;
        $copy->status = 0;
        $copy->save(); 
    }

    public function update(Request $request, $id)
    {
        //a book_id ne változzon! mert akkor már másik példányról van szó
        $copy = Copy::find($id);
        $copy->hardcovered = $request->hardcovered;
        $copy->publication = $request->publication;
        $copy->status = $request->status;
        $copy->save();        
    }

    public function copies_pieces($title)
    {	
        $copies = Book::with('copy_c')->where('title','=', $title)->count();
        return $copies;
    }

    public function more_lendings($db){
        //bejelentkezett felh azon kölcsönzései a példány kódjával, ahol a példányt legalább 2 $db -szer kikölcsönözte 
        $user = Auth::user();
        $lendings = DB::table('lendings as l')
        ->selectRaw('count(l.copy_id) as number_of_copies, l.copy_id')
        ->join('copies as c', 'l.copy_id','=','c.copy_id')
        ->where('l.user_id', $user->id)
        ->groupBy('l.copy_id')
        ->having('number_of_copies', '>=', $db)
        ->get();

        return $lendings;
    }

    //view-k:

    public function newView()
    {
        //új rekord(ok) rögzítése
        $books = Book::all();
        return view('copy.new', ['books' => $books]);
    }

    public function editView($id)
    {
        $books = Book::all();
        $copy = Copy::find($id);
        return view('copy.edit', ['books' => $books, 'copy' => $copy]);
    }

    public function listView()
    {
        $copies = Copy::all();
        //copy mappában list blade
        return view('copy.list', ['copies' => $copies]);
    }
}
