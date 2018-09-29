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
use App\func as func;

class ReservationController extends Controller
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
        $dataRoom = DB::table('meeting_room')
            ->join('meeting_type', 'meeting_room.meeting_type_ID', '=', 'meeting_type.meeting_type_ID')
            ->select('meeting_ID', 'meeting_name', 'meeting_size', 'meeting_pic', 'meeting_buiding', 'meeting_status', 'meeting_type_name')
            ->get();

        $data = array(
            'rooms' => $dataRoom
        );
        return view('officer/reservation/index', $data);
    }

    public function Form($id =''){
        $resultData = func::selectReserve($id);
        $imgs_room = array();
        $imgs_room = explode(',', $resultData->meeting_pic);

        $data = array(
            'rooms' => $resultData,
            'imgs' => $imgs_room
        );
        return view('officer/reservation/reserveOnID', $data);
    }

    public function CHECK_DATE_RESERVE (Request $req) {
        $temp_date = explode('-', $req->date);
        $date_select = ($temp_date[2] - 543).'-'.$temp_date[1].'-'.$temp_date[0];
        $check_weekend = date_create($date_select);
        $check_weekend = date_format($check_weekend, 'r');
        $date_now = date('Y-m-d');
        $constant_cancel_timeuse = ['1', '1', '1', '1', '1', '1', '1', '1'];
        $dataHolidays = DB::table('holiday')
                    ->where('holiday_start', '<=', $date_select)
                    ->where('holiday_end', '>=', $date_select)
                    ->first();

        if ($date_now > $date_select) {
            return response()->json(['error'=> 'ไม่สามารถจองห้องได้', 'constant_time' => $constant_cancel_timeuse]);
        } else {
            if (substr($check_weekend, 0, 3) == 'Sat' || substr($check_weekend, 0, 3) == 'Sun' || isset($dataHolidays)) {
                return response()->json(['error'=> 'ไม่สามารถจองห้องในวันหยุดได้', 'constant_time' => $constant_cancel_timeuse]);
            } else {
                $time_use = func::GET_TIMEUSE ($date_select, $req->roomid);
                return response()->json(['time_use'=> $time_use]);
            }
        }
    }

    public function reserveForm($id, $timeReserve, $timeSelect){
        $dataRoom = DB::table('meeting_room')
            ->where('meeting_ID', $id)
            ->first();
        
        $dataTimeReserve = DB::table('detail_booking')
            ->where('meeting_ID', $id)
            ->get();
        
        $time_reamain = func::CHECK_TIME_REAMAIN ($id, $timeReserve, $timeSelect);
        $temp_date = explode('-', $timeSelect);
        $date_select = ($temp_date[2] - 543).'-'.$temp_date[1].'-'.$temp_date[0];
        $data = array(
            'room' => $dataRoom,
            'time_reserve' => $timeReserve.':00',
            'time_remain' => $time_reamain,
            'checkin' =>$date_select,
        );
        return view('officer/reservation/Form', $data);
    }

    public function confirm(Request $req){
        //dd($req);
        $msg = [
            'detail_topic.required' => "กรุณาระบุหัวข้อการประชุม",
            'detail_count.required' => "กรุณาระบุจำนวนผู้เข้าร่วมประชุม",
            'contract_name.required' => "กรุณาระบุชื่อผู้จองห้องประชุม",
            'user_tel.required' => "กรุณาระบุเบอร์โทรศัพท์ผู้จองห้องประชุม",
            'contract_file.required' => "กรุณาแนบเอกสารหลักฐานการติดต่อจองห้องประชุม",
            'detail_count.numeric' => "จำนวนผู้เข้าร่วมประชุมต้องเป็นหมายเลข",
          ];
    
          $rule = [
            'detail_topic' => 'required',
            'detail_count' => 'required|numeric',
            'contract_name' => 'required',
            'user_tel' => 'required',
            'contract_file' => 'required',
          ];
          $validator = Validator::make($req->all(),$rule);
    
          if ($validator->passes()) {
            // insert booking
            $bookid = DB::table('booking')->insertGetId([
                'status_ID' => 1,
                'section_ID' => $req->section_id,
                'institute_ID' => isset($req->institute_id)? $req->institute_id : null,
                'user_ID' => Auth::user()->id,
                'booking_name' => $req->contract_name,
                'booking_phone' => $req->user_tel,
                'booking_date' => date('Y-m-d H:i:s'),
                'approve_date' => date('Y-m-d H:i:s'),
                'checkin' => $req->checkin,
                
            ]);
            // insert detail booking
            $time_start = date($req->checkin.' '.$req->time_reserve.':00');
            $time_out = date($req->checkin.' '.(substr($req->time_reserve, 0, 2) + $req->time_use).':00');
            DB::table('detail_booking')
                    ->insert([
                        'booking_ID' => $bookid,
                        'meeting_ID' => $req->meeting_id,
                        'detail_topic' => $req->detail_topic,
                        'detail_timestart' => $time_start,
                        'detail_timeout' => $time_out,
                        'detail_count' => $req->detail_count
            ]);
            // check Do you borrow equipment?
            if (isset($req->hdnEq)) {
                for($index = 0 ; $index < count($req->hdnEq); $index++){
                    $temp = explode(",",$req->hdnEq[$index]);
                    $data_em = DB::table('equipment')
                                ->where('em_name', $temp[0])
                                ->first();

                    $data_id_equipment[$index] = $data_em->em_ID;
                    $data_count_equipment[$index] = $temp[1];
                }
                func::SET_DATA_BORROW($data_id_equipment, $data_count_equipment, $bookid, $req->checkin);
            }
            // check have file
            $files = $req->file('contract_file');
            if(!empty($req->file('contract_file'))){
                foreach($files as $key => $file){
                    $fileType = explode('.',$file->getClientOriginalName());
                    $fileType = $fileType[count($fileType)-1];
                    $fileFullName = date('U').'-doc'.($key+1).".".$fileType;
                    $file->move('asset/documents',$fileFullName);
                    DB::table('document')->insert([
                        'institute_ID'=>isset($req->institute_id)? $req->institute_id : null,
                        'section_ID' => isset($req->section_id)? $req->section_id : null,
                        'document_file' => $fileFullName,
                        'booking_id' => $bookid
                    ]);
                }
            }
            return redirect('control/reservation/')
                        ->with('successMessage','จองห้องเรียบร้อย');
          }else{
            return redirect()->back()->withInput($req->input());
          }
    }
}
