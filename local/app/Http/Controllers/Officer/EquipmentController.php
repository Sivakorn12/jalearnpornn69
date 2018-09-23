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

class EquipmentController extends Controller
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
        $equipments = DB::table('equipment')
                 ->get();
        $data = array(
            'equipments' => $equipments
        );
        return view('officer/equipment/index',$data);
    }

    public function Form($id =''){
        if($id==''){
            return view('officer/equipment/Form',[
                'form'=>'success',
                'action' => 'add'
            ]);
        }
        else{
            $equipment = DB::table('equipment')
                        ->where('em_ID',$id)
                        ->first();
            return view('officer/equipment/Form',[
                'equipment' => $equipment ,
                'form'=>'warning',
                'action' => 'update'
            ]);
        }
    }

    public function add(Request $request){
        //dd($request);
        $msg = [
            'em_name.required' => "กรุณาระบุชื่ออุปกรณ์",
            "em_count.required" => "กรุณาระบุจำนวนอุปกรณ์",
          ];
    
          $rule = [
            'em_name' => 'required',
            'em_count' => 'required',
          ];
    
          $validator = Validator::make($request->all(),$rule,$msg);
    
          if ($validator->passes()) {
              try{
                $newid = '10001';
                $last = DB::table('equipment')->orderBy('em_ID','desc')->first();
                if($last!=null) $newid = substr($last->em_ID,0,5)+1;
                DB::table('equipment')->insert([
                    "em_ID" =>$newid,
                    "em_name" =>$request->em_name,
                    "em_count" =>$request->em_count,
                ]);
                
                return redirect('control/equipment/')
                        ->with('successMessage','เพิ่มอุปกรณ์สำเร็จ');
              }catch (Exception $e) {
                return redirect('control/equipment/')
                        ->with('errorMesaage',$e);
              }
          }else{
            return redirect('control/equipment/form')
                        ->withErrors($validator)
                        ->withInput($request->input());
          }
    }

    public function update(Request $request){
        //dd($request);
        $msg = [
            'em_name.required' => "กรุณาระบุชื่ออุปกรณ์",
            "em_count.required" => "กรุณาระบุจำนวนอุปกรณ์",
          ];
    
          $rule = [
            'em_name' => 'required',
            'em_count' => 'required',
          ];
    
          $validator = Validator::make($request->all(),$rule,$msg);
    
          if ($validator->passes()) {
              try{
                DB::table('equipment')
                    ->where('em_ID',$request->id)
                    ->update([
                        "em_name" =>$request->em_name,
                        "em_count" =>$request->em_count,
                    ]);
                
                return redirect('control/equipment/')
                        ->with('successMessage','แก้ไขอุปกรณ์สำเร็จ');
              }catch (Exception $e) {
                return redirect('control/equipment/')
                        ->with('errorMesaage',$e);
              }
          }else{
            return redirect('control/equipment/edit/'.$request->id)
                        ->withErrors($validator)
                        ->withInput($request->input());
          }
    }

    public function delete($id){
        DB::table('equipment')->where('em_ID',$id)->delete();
        return redirect('control/equipment/');
    }
}
