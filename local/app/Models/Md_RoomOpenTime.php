<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Md_RoomOpenTime extends Model
{
    //
    protected $table 		= 'room_open_time';
	public $timestamps 		= false;
    protected $primaryKey 	= 'room_open_time_id';
    
    protected $fillable = [
        'day_id',
        'open_time',
        'close_time',
        'open_flag',
        'meeting_ID'
    ];

}
