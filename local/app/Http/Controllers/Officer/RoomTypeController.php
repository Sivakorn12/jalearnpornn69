<?php

namespace App\Http\Controllers\Officer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Session;
use Auth;
use DB;
use \Input as Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Officer as officer;

class RoomTypeController extends Controller
{
    //
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
        $meeting_type = DB::table('meeting_type')
                 ->get();
        $data = array(
            'meeting_types' => $meeting_type
        );
        return view('officer/meeting_type/index',$data);
    }

    public function save(Request $req){
        //dd($req->all());
        if($req->roomtype_id != ''){
            // update
            DB::table('meeting_type')
                ->where('meeting_type_ID',$req->roomtype_id)
                ->update([
                    'meeting_type_name' => $req->roomtype
                ]);
            return redirect('control/roomtype/')
                ->with('successMessage','แก้ไขประเภทห้องสำเร็จ');
        }
        else{
            // new
            $mt = DB::table('meeting_type')->orderBy('meeting_type_ID','desc')->first();
            $id = (isset($mt)) ? ($mt->meeting_type_ID + 1) : 10000;
            DB::table('meeting_type')
                ->insert([
                    'meeting_type_ID' => $id,
                    'meeting_type_name' => $req->roomtype
                ]);
            return redirect('control/roomtype/')
                ->with('successMessage','เพิ่มประเภทห้องสำเร็จ');
        }
    }

    public function delete($id){
        if(officer::isUseRoomType($id)){
            return redirect('control/roomtype')
                ->with('errorMessage','ไม่สามารถลบข้อมูลประเภทห้องได้เนื่องจากมีการอ้างอิงถึงข้อมูล');
        }
        DB::table('meeting_type')->where('meeting_type_ID',$id)->delete();
        return redirect('control/roomtype');
    }
}
