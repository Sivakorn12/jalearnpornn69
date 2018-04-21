<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\func as func;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dataRoom = DB::table('meeting_room')
            ->join('meeting_type', 'meeting_room.meeting_type_ID', '=', 'meeting_type.meeting_type_ID')
            ->select('meeting_ID', 'meeting_name', 'meeting_size', 'meeting_pic', 'meeting_buiding', 'meeting_status', 'meeting_type_name')
            ->get();

        $typeRoom = DB::table('meeting_type')->get();
        $sizeRoom = DB::table('meeting_room')
                            ->select('meeting_size')
                            ->groupBy('meeting_size')
                            ->get();

        $data = array(
            'rooms' => $dataRoom,
            'types' => $typeRoom,
            'sizes' => $sizeRoom
        );

        return view('home', $data);
    }
    
    public function searchType(Request $req)
    {
        $data = $req->type;
        $resultData = func::queryData($data, 'meeting_type_name');

        return response()->json(['res'=> $resultData]);
    }

    public function searchSize(Request $req)
    {
        $data = $req->size;
        $resultData = func::queryData($data, 'meeting_size');

        return response()->json(['res'=> $resultData]);
    }
}
