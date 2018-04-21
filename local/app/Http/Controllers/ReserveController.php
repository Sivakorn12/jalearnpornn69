<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\func as func;

class ReserveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $dataRoom = DB::table('meeting_room')
            ->join('meeting_type', 'meeting_room.meeting_type_ID', '=', 'meeting_type.meeting_type_ID')
            ->select('meeting_ID', 'meeting_name', 'meeting_size', 'meeting_pic', 'meeting_buiding', 'meeting_status', 'meeting_type_name')
            ->get();

        $data = array(
            'rooms' => $dataRoom
        );
        return view('ReserveRoom/index', $data);
    }

    public function ReservrRoom($id)
    {
        $resultData = func::selectReserve($id);

        $data = array(
            'rooms' => $resultData
        );
        return view('ReserveRoom/reserveOnID', $data);
    }
}
