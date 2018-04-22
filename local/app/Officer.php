<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Officer extends Model
{
    //
    public static function getStatusBooking($id){
        $status = DB::table('status_room')
                  ->where('status_ID',$id)
                  ->first();
        return $status->status_name;
    }

}
