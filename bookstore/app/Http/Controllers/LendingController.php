<?php

namespace App\Http\Controllers;

use App\Models\Copy;
use App\Models\Lending;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LendingController extends Controller
{
    //
    public function index(){
        $lendings =  Lending::all();
        return $lendings;
    }

    public function show ($user_id, $copy_id, $start)
    {
        $lending = Lending::where('user_id', $user_id)->where('copy_id', $copy_id)->where('start', $start)->get();
        return $lending[0];
    }
    public function destroy($user_id, $copy_id, $start)
    {
        LendingController::show($user_id, $copy_id, $start)->delete();
    }

    public function store(Request $request)
    {
        $lending = new Lending();
        $lending->user_id = $request->user_id;
        $lending->copy_id = $request->copy_id;
        $lending->start = $request->start;
        $lending->end = $request->end;
        $lending->extension = $request->extension;
        $lending->notice = $request->notice;
        $lending->save();
    }

    public function update(Request $request, $user_id, $copy_id, $start)
    {
        $lending = LendingController::show($user_id, $copy_id, $start);
        $lending->user_id = $request->user_id;
        $lending->copy_id = $request->copy_id;
        $lending->start = $request->start;
        $lending->end = $request->end;
        $lending->extension = $request->extension;
        $lending->notice = $request->notice;
        $lending->save();
    }

    public function userLendingsList()
    {
        $user = Auth::user();	//bejelentkezett felhasználó
        $lendings = Lending::with('user_c')->where('user_id','=', $user->id)->get();
        return $lendings;
    }

    public function userLendingsCount()
    {
        $user = Auth::user();	//bejelentkezett felhasználó
        $lendings = Lending::with('user_c')->where('user_id','=', $user->id)->distinct('copy_id')->count();
        return $lendings;
    }

    public function lengthen($copy_id, $start)
    {
        //könyv meghosszabbítása
        $user = Auth::user();
        //könyv lekérdezése
        $book = DB::table('lendings as l')
        ->select('c.book_id')
        ->join('copies as c' ,'l.copy_id','=','c.copy_id') //kapcsolat leírása, akár több join is lehet
        ->where('l.user_id', $user->id) 	//esetleges szűrés
        ->where('l.copy_id', $copy_id)
        ->where('l.start', $start)
        ->get()
        ->value('book_id');

        //return $book;
        
        $lending = LendingController::show($user->id, $copy_id, $start);
        //ha nincs rá előjegyzés: count... 
        $recordNumbers = LendingController::reserved($book);
        if ($recordNumbers == 0)
            {//meghosszabbítja
                $lending->extension = 1;
                $lending->save();
            }
    }

    public function reserved($book_id)
    {
        //hány db foglalás van az adott könyvre - nem példányra, amin nincs értesítés
        return DB::table('reservations as r')->where('r.message', 0)->where('r.book_id', $book_id)->count();
    }

    //view-k:
    public function newView()
    {
        //új rekord(ok) rögzítése
        $users = User::all();
        $copies = Copy::all();
        return view('lending.new', ['users' => $users, 'copies' => $copies]);
    }
}
