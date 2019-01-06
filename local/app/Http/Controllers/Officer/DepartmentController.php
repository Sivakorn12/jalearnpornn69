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

class DepartmentController extends Controller
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
        $dep = DB::table('department')
                 ->join('faculty as f','f.faculty_ID','=','department.faculty_ID')
                 ->get();
        $faculty = DB::table('faculty')->get();
        $data = array(
            'department' => $dep,
            'faculty' => $faculty
        );
        return view('officer/department/index',$data);
    }

    public function save(Request $req){
        //dd($req->all());
        if($req->department_id != ''){
            // update
            DB::table('department')
                ->where('department_ID',$req->department_id)
                ->update([
                    'department_name' => $req->department_name,
                    'faculty_ID' => $req->faculty_id
                ]);
            return redirect('control/department/')
                ->with('successMessage','แก้ไขข้อมูลภาควิชาสำเร็จ');
        }
        else{
            // new
            DB::table('department')
                ->insert([
                    'department_name' => $req->department_name,
                    'faculty_ID' => $req->faculty_id
                ]);
            return redirect('control/department/')
                ->with('successMessage','เพิ่มข้อมูลภาควิชาสำเร็จ');
        }
    }

    public function delete($id){
        $sec = DB::table('section')->where('department_ID',$id)->get()->toArray();
        
        if(sizeof($sec)>0 or officer::useDepartmentInBooking($id)){
            return redirect('control/department/')
                ->with('errorMessage','ไม่สามารถลบข้อมูลภาควิชาได้เนื่องจากมีการอ้างอิงถึงข้อมูล');
        }
        else{
            DB::table('department')->where('department_ID',$id)->delete();
            return redirect('control/department');
        }
        
    }
}
