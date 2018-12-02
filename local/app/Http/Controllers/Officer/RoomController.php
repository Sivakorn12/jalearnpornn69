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
use App\Models\Md_RoomOpenTime;

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
                 ->join('building as b','meeting_room.meeting_buiding','=','b.building_id')
                 ->get();
        $data = array(
            'rooms' => $rooms
        );
        return view('officer/room/index',$data);
    }

    public function Form($id =''){
        $room_open_time = officer::setDefaultRoomOpenTime();
        $day = officer::getShortDayThai();
        if($id==''){
            return view('officer/room/Form',[
                'form'=>'success',
                'action' => 'add',
                'room_open_time' => $room_open_time,
                'day' => $day
            ]);
        }
        else{
            $meeting_room = DB::table('meeting_room')
                        ->where('meeting_ID',$id)
                        ->first();
            
            $room_open = DB::table('room_open_time')
                            ->where('meeting_ID',$id)
                            ->orderBy('day_id')
                            ->get();
            foreach($room_open as $open){
                $room_open_time[($open->day_id-1)]["open_time"] = $open->open_time;
                $room_open_time[($open->day_id-1)]["close_time"] = $open->close_time;
                $room_open_time[($open->day_id-1)]["open_flag"] = $open->open_flag;
            }
            
            return view('officer/room/Form',[
                'room' => $meeting_room ,
                'form'=>'warning',
                'action' => 'update',
                'room_open_time' => $room_open_time,
                'day' => $day
            ]);
        }
    }

    public function add(Request $request){
        $msg = [
            'room_name.required' => "กรุณาระบุชื่อห้องประชุม",
            "type.required" => "กรุณาระบุประเภทห้องประชุม",
            'room_size.required' => "กรุณาระบุขนาดห้องประชุม",
            'room_building.required' => "กรุณาระบุอาคาร"
          ];
    
          $rule = [
            'room_name' => 'required',
            'type' => 'required',
            'room_size' => 'required|Numeric',
            'room_building' => 'required'
          ];

          $regex='/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/';
          $resultUrl = preg_match($regex, $request->est_link);
    
          $validator = Validator::make($request->all(),$rule,$msg);

          if(!$resultUrl) {
            $validator->getMessageBag()->add('est_link', 'ลิ้งประเมินไม่ถูกต้อง');
            return redirect('control/room/form')
                        ->withErrors($validator)
                        ->withInput($request->input());
          }
    
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
                    "estimate_link" => isset($request->est_link)? $request->est_link : null,
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

                officer::setRoomOpenAllDay($request,$id);

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
        //dd($request->all());
        $msg = [
            'room_name.required' => "กรุณาระบุชื่อห้องประชุม",
            "type.required" => "กรุณาระบุประเภทห้องประชุม",
            'room_size.required' => "กรุณาระบุขนาดห้องประชุม",
            'room_building.required' => "กรุณาระบุอาคาร",
          ];
    
          $rule = [
            'room_name' => 'required',
            'type' => 'required',
            'room_size' => 'required|Numeric',
            'room_building' => 'required',
          ];

          $regex='/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/';
          $resultUrl = preg_match($regex, $request->est_link);
    
          $validator = Validator::make($request->all(),$rule,$msg);
          
          if(!$resultUrl) {
            $validator->getMessageBag()->add('est_link', 'ลิ้งประเมินไม่ถูกต้อง');
            return redirect('control/room/form')
                        ->withErrors($validator)
                        ->withInput($request->input());
          }
    
          if ($validator->passes()) {
            try{
                DB::beginTransaction();
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
                    "estimate_link" => isset($request->est_link)? $request->est_link : null,
                    "meeting_pic" =>$picTxt,
                    "meeting_buiding" =>$request->room_building,
                    "meeting_status" =>1,
                ]);

                if(isset($request->hdnEq)){
                    if($request->changeEq == 'yes'){
                        DB::table('equipment_in')->where('meeting_ID',$request->id)->delete();
                        for($i = 0 ; $i < count($request->hdnEq);$i++){
                            $temp = explode(",",$request->hdnEq[$i]);
                            DB::table('equipment_in')->insert([
                                "meeting_ID" =>$request->id,
                                "em_in_count" =>$temp[1],
                                "em_in_name" =>$temp[0],
                            ]);
                        }
                    }
                }
                officer::setRoomOpenAllDay($request,$request->id);
                officer::checkreserv($request->id);
                DB::commit();

                return redirect('control/room/')
                        ->with('successMessage','แก้ไขห้องสำเร็จ');
              }catch (Exception $e) {
                DB::rollBack();
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
        if(isset($meeting_room->meeting_pic) and $meeting_room->meeting_pic != ""){
            $images = explode(',', $meeting_room->meeting_pic);
            for($i=0;$i<count($images);$i++){
                officer::deleteFile('asset/rooms/'.$images[$i]);
            }
        }

        DB::table('meeting_room')->where('meeting_ID',$id)->delete();
        return redirect('control/room/');
    }

    public function checkreserv(){
        $date = date('Y-m-d');
        $room_id = 6;

        //dd($date);
        $data_reserv = DB::table('booking')
                            ->join('detail_booking as dtb','booking.booking_ID','=','dtb.booking_ID')
                            ->where('booking.checkin','>=',$date)
                            ->where('dtb.meeting_ID',$room_id)
                            ->get();
        $data_room_open = Md_RoomOpenTime::where('meeting_ID',$room_id)->get()->toArray();
        foreach($data_reserv as $key => $drs){
            $day_id = (date("N", strtotime($drs->checkin." 00:00:00"))+1);
            $time_start = date("H:i:s", strtotime($drs->detail_timestart));
            $time_end = date("H:i:s", strtotime($drs->detail_timeout));

            echo "check in at :".$drs->checkin." day: ".(date("N", strtotime($drs->checkin." 00:00:00"))+1)." time st:".$time_start."<br>";
            if($data_room_open[$day_id-1]["open_time"] > $time_start ){
                $old_status = DB::table('booking')->select('status_ID')->where('booking_ID',$drs->booking_ID)->first();
                if(isset($old_status) and $old_status->status_ID ==1 ){
                    officer::cancelBorrowEquipment($drs->booking_ID);
                }

                DB::table('booking')
                ->where('booking_ID',$drs->booking_ID)
                ->update([
                    'status_ID' => 4
                ]);
                echo "canceled reserve.<br>";
            }
        }
        //dd($data_reserv);
    }
}
