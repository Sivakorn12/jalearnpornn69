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
                $pic = array();
                $files = $request->file('room_image');
                if(!empty($request->file('room_image'))){
                    foreach($files as $key => $file){
                        $fileType = explode('.',$file->getClientOriginalName());
                        $fileType = $fileType[count($fileType)-1];
                        $fileFullName = date('U').'-room'.($key+1).".".$fileType;
                        array_push($pic,$fileFullName);
                        $file->move('asset/rooms',$fileFullName);
                    }
                }
                $picTxt = implode(",",$pic);
                try{
                $id = DB::table('meeting_room')->insertGetId([
                    "meeting_type_ID" =>$request->type,
                    "provision" =>$request->provision,
                    "meeting_name" =>$request->room_name,
                    "meeting_size" =>$request->room_size,
                    "meeting_pic" =>$picTxt,
                    "meeting_buiding" =>$request->room_building,
                    "meeting_status" =>1,
                ]);

                if(isset($request->hdnEq)){
                    for($i = 0 ; $i < count($request->hdnEq);$i++){
                        $temp = explode(",",$request->hdnEq[$i]);
                        DB::table('equipment_in')->insert([
                            "meeting_ID" =>$id,
                            "em_in_count" =>$temp[1],
                            "em_in_name" =>$temp[0],
                        ]);
                    }
                }

                
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
                $pic = array();
                $files = $request->file('room_image');
                if(!empty($request->file('room_image'))){

                    // add new file
                    foreach($files as $key => $file){
                        $fileType = explode('.',$file->getClientOriginalName());
                        $fileType = $fileType[count($fileType)-1];
                        $fileFullName = date('U').'-room'.($key+1).".".$fileType;
                        array_push($pic,$fileFullName);
                        $file->move('asset/rooms',$fileFullName);
                    }
                    // delete old file
                    $images = explode(',', $request->oldpic);
                    for($i=0;$i<count($images);$i++){
                        officer::deleteFile('asset/rooms/'.$images[$i]);
                    }
                    $picTxt = implode(",",$pic);
                }
                else{
                    $picTxt = $request->oldpic;
                }
                DB::table('meeting_room')
                    ->where('meeting_ID',$request->id)
                    ->update([
                    "meeting_type_ID" =>$request->type,
                    "provision" =>$request->provision,
                    "meeting_name" =>$request->room_name,
                    "meeting_size" =>$request->room_size,
                    "meeting_pic" =>$picTxt,
                    "meeting_buiding" =>$request->room_building,
                    "meeting_status" =>1,
                ]);
            
                
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
        // delete old file
        $images = explode(',', $meeting_room->meeting_pic);
        for($i=0;$i<count($images);$i++){
            officer::deleteFile('asset/rooms/'.$images[$i]);
        }

        DB::table('meeting_room')->where('meeting_ID',$id)->delete();
        return redirect('control/room/');
    }
}
