<?php

namespace App\Http\Controllers\Officer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Auth;
use DB;
use \Input as Input;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    //
    public function downloadFile(Request $req){
        if(isset($req->filename)){
            $file = Storage::disk('document')->getDriver()->getAdapter()->applyPathPrefix($req->filename);
            return response()->download($file ,$req->filename);
        } 
        else return false;
    }
}
