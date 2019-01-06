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

class SectionController extends Controller
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
        $section = DB::table('section')
                 ->join('department as d','section.department_ID','=','d.department_ID')
                 ->join('faculty as f','f.faculty_ID','=','d.faculty_ID')
                 ->get();
        $dep = DB::table('department')->get();
        $data = array(
            'section' => $section,
            'department' => $dep
        );
        return view('officer/section/index',$data);
    }

    public function save(Request $req){
        //dd($req->all());
        if($req->section_id != ''){
            // update
            DB::table('section')
                ->where('section_ID',$req->section_id)
                ->update([
                    'section_name' => $req->section_name,
                    'department_id' => $req->department_id
                ]);
            return redirect('control/section/')
                ->with('successMessage','แก้ไขข้อมูลสาขาวิชาสำเร็จ');
        }
        else{
            // new
            DB::table('section')
                ->insert([
                    'section_name' => $req->section_name,
                    'department_id' => $req->department_id
                ]);
            return redirect('control/section/')
                ->with('successMessage','เพิ่มข้อมูลสาขาวิชาสำเร็จ');
        }
    }

    public function delete($id){
        if(officer::useSectionInBooking($id)){
            return redirect('control/section')
                ->with('errorMessage','ไม่สามารถลบข้อมูลสาขาวิชาได้เนื่องจากมีการอ้างอิงถึงข้อมูล');
        }
        DB::table('section')->where('section_ID',$id)->delete();
        return redirect('control/section');
    }
}
