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

class ReturnEquipController extends Controller
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
        return view('officer/return-eq/index');
    }

    public function viewdetailBorrow(Request $req){
        if(isset($req->id)){
            $html = officer::modalViewDetailBorrow($req->id,$req->type);
            return response()->json(['html'=>$html]);
        }
    }

    public function confirm($id){
        try{
            $datas = DB::table('detail_borrow')
                    ->join('equipment','equipment.em_ID','=','detail_borrow.equiment_ID')
                    ->where('detail_borrow.borrow_ID',$id)
                    ->get();
            // update status borrow
            DB::table('borrow_booking')
                ->where('borrow_ID', $id)
                ->update([
                    'borrow_status' => 1,
                ]);
            foreach($datas as $key => $data){
                // update status borrow
                DB::table('equipment')
                    ->where('em_ID', $data->em_ID)
                    ->update([
                        'em_count' => ($data->em_count-$data->borrow_count),
                    ]);
            }
            return redirect('control/return-eq/')
                ->with('successMessage','ยืนยันการยืมเรียบร้อย');
        }catch (Exception $e) {
            return redirect('control/return-eq/')
                    ->with('errorMesaage',$e);
        }
    }

    public function cancel($id){
        DB::table('borrow_booking')
                ->where('borrow_ID', $id)
                ->update([
                    'borrow_status' => 2,
                ]);
        return redirect('control/return-eq/')
                ->with('successMessage','ยกเลิกการยืมเรียบร้อย');
    }

    public function confirmReturn($id){
        try{
            $datas = DB::table('detail_borrow')
                    ->join('equipment','equipment.em_ID','=','detail_borrow.equiment_ID')
                    ->where('detail_borrow.borrow_ID',$id)
                    ->get();
            // insert return booking table
            $return_id=DB::table('return_booking')->insertGetId([
                            'staff_ID'=>Auth::user()->id,
                            'booking_ID' => officer::getBookingIDbyBorrow($id),
                            'return_date' => date('Y-m-d')
                        ]);
            foreach($datas as $key => $data){
                // update em_count
                DB::table('equipment')
                    ->where('em_ID', $data->em_ID)
                    ->update([
                        'em_count' => ($data->em_count+$data->borrow_count),
                    ]);

                DB::table('detail_return')->insert([
                    'return_ID'=>$return_id,
                    'equiment_ID' => $data->em_ID,	
                    'return_count' => $data->em_count
                ]);
            }
            return redirect('control/return-eq/')
                ->with('successMessage','คืนอุปกรณ์เรียบร้อย');
        }catch (Exception $e) {
            return redirect('control/return-eq/')
                    ->with('errorMesaage',$e);
        } 
    }
}
