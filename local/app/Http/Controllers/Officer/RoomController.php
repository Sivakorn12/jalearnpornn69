<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use DB;
use \Input as Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Officer as officer;

class RoomController extends Controller
{
    public function __construct(){
        
        //$this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if(empty(Auth::user())) return redirect('/');
            if(Auth::user()->user_status!="superuser"){
                return redirect('/');
            }  
            return $next($request);
        });
        date_default_timezone_set("Asia/Bangkok");
        
    }

    public function index(){
        $rooms = DB::table('meeting_room')
                 ->join('meeting_type','meeting_room.meeting_type_ID','=','meeting_type.meeting_type_ID')
                 ->get();
        $data = array(
            'rooms' => $rooms
        );
        return view('officer/room/index',$data);
    }
}
