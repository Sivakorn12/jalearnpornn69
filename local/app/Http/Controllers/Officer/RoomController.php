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

    public function Form($id =''){
        if($id==''){
            return view('officer/room/Form',[
                'form'=>'success',
                'action' => 'add'
            ]);
        }
        else{
            $meeting_room = DB::table('meeting_room')
                        ->where('meeting_ID',$id)
                        ->first();
            return view('officer/room/Form',[
                'room' => $meeting_room ,
                'form'=>'warning',
                'action' => 'update'
            ]);
        }
    }

    public function add(Request $request){
        //dd($request);
        $msg = [
            'room_name.required' => "กรุณาระบุชื่อห้องประชุม",
            "type.required" => "กรุณาระบุประเภทห้องประชุม",
            'room_size.required' => "กรุณาระบุขนาดห้องประชุม",
            'room_building.required' => "กรุณาระบุอาคาร",
          ];
    
          $rule = [
            'room_name' => 'required',
            'type' => 'required',
            'room_size' => 'required',
            'room_building' => 'required',
          ];
    
          $validator = Validator::make($request->all(),$rule,$msg);
    
          if ($validator->passes()) {
              try{
                $file = $request->file('room_image');
                if(!empty($request->file('room_image'))){
                    $pic = $file->getClientOriginalName();
                }
                DB::table('meeting_room')->insert([
                    "meeting_type_ID" =>$request->type,
                    "provision" =>$request->provision,
                    "meeting_name" =>$request->room_name,
                    "meeting_size" =>$request->room_size,
                    "meeting_pic" =>$pic,
                    "meeting_buiding" =>$request->room_building,
                    "meeting_status" =>1,
                ]);
                
                $file->move('asset/rooms',$file->getClientOriginalName());
                return redirect('control/room/')
                        ->with('successMessage','เพิ่มห้องสำเร็จ');
              }catch (Exception $e) {
                return redirect('control/room/')
                        ->with('errorMesaage',$e);
              }
              

          }else{
            return redirect('control/room/form')
                        ->withErrors($validator)
                        ->withInput($request->input());
          }
    }

    public function update(Request $request){
        //dd($request);
        $msg = [
            'room_name.required' => "กรุณาระบุชื่อห้องประชุม",
            "type.required" => "กรุณาระบุประเภทห้องประชุม",
            'room_size.required' => "กรุณาระบุขนาดห้องประชุม",
            'room_building.required' => "กรุณาระบุอาคาร",
          ];
    
          $rule = [
            'room_name' => 'required',
            'type' => 'required',
            'room_size' => 'required',
            'room_building' => 'required',
          ];
    
          $validator = Validator::make($request->all(),$rule,$msg);
    
          if ($validator->passes()) {
            try{
                $file = $request->file('room_image');
                if(!empty($request->file('room_image'))){
                    $pic = $file->getClientOriginalName();
                }
                else{
                    $pic = $request->oldpic;
                }
                DB::table('meeting_room')
                    ->where('meeting_ID',$request->id)
                    ->update([
                    "meeting_type_ID" =>$request->type,
                    "provision" =>$request->provision,
                    "meeting_name" =>$request->room_name,
                    "meeting_size" =>$request->room_size,
                    "meeting_pic" =>$pic,
                    "meeting_buiding" =>$request->room_building,
                    "meeting_status" =>1,
                ]);
                if(!empty($request->file('room_image'))){
                    $filename='asset/rooms/'.$request->oldpic;
                    officer::deleteFile($filename);
                    $file->move('asset/rooms',$file->getClientOriginalName());
                }
                
                return redirect('control/room/')
                        ->with('successMessage','แก้ไขห้องสำเร็จ');
              }catch (Exception $e) {
                return redirect('control/room/')
                        ->with('errorMesaage',$e);
              }
              
          }else{
            return redirect('control/room/edit/'.$request->id)
                        ->withErrors($validator)
                        ->withInput($request->input());
          }
    }

    public function delete($id){
        $meeting_room = DB::table('meeting_room')
                        ->where('meeting_ID',$id)
                        ->first();
        $filename='asset/rooms/'.$meeting_room->meeting_pic;
        officer::deleteFile($filename);

        DB::table('meeting_room')->where('meeting_ID',$id)->delete();
        return redirect('control/room/');
    }
}
