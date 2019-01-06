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

class BuildingController extends Controller
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
        $building = DB::table('building')
                 ->get();
        $data = array(
            'buildings' => $building
        );
        return view('officer/building/index',$data);
    }

    public function save(Request $req){
        //dd($req->all());
        if($req->building_id != ''){
            // update
            DB::table('building')
                ->where('building_id',$req->building_id)
                ->update([
                    'building_name' => $req->building_name
                ]);
            return redirect('control/building/')
                ->with('successMessage','แก้ไขข้อมูลอาคารสำเร็จ');
        }
        else{
            // new
            DB::table('building')
                ->insert([
                    'building_name' => $req->building_name
                ]);
            return redirect('control/building/')
                ->with('successMessage','เพิ่มข้อมูลอาคารสำเร็จ');
        }
    }

    public function delete($id){
        if(officer::isUseBuilding($id)){
            return redirect('control/building')
                ->with('errorMessage','ไม่สามารถลบข้อมูลอาคารได้เนื่องจากมีการอ้างอิงถึงข้อมูล');
        }
        DB::table('building')->where('building_id',$id)->delete();
        return redirect('control/building');
    }
}
