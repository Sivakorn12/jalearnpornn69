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

class FacultyController extends Controller
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
        $faculty = DB::table('faculty')
                 ->get();
        $data = array(
            'faculty' => $faculty
        );
        return view('officer/faculty/index',$data);
    }

    public function save(Request $req){
        //dd($req->all());
        if($req->faculty_id != ''){
            // update
            DB::table('faculty')
                ->where('faculty_ID',$req->faculty_id)
                ->update([
                    'faculty_name' => $req->faculty_name
                ]);
            return redirect('control/faculty/')
                ->with('successMessage','แก้ไขข้อมูลคณะสำเร็จ');
        }
        else{
            // new
            DB::table('faculty')
                ->insert([
                    'faculty_name' => $req->faculty_name
                ]);
            return redirect('control/faculty/')
                ->with('successMessage','เพิ่มข้อมูลคณะสำเร็จ');
        }
    }

    public function delete($id){
        DB::table('faculty')->where('faculty_ID',$id)->delete();
        return redirect('control/faculty');
    }
}
