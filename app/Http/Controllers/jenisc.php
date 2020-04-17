<?php

namespace App\Http\Controllers;

use App\jenis_mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Session;
use Auth;
use DB;

class jenisc extends Controller
{
    public function add(request $r){
        if(Auth::user()->level == 1){
         $validator=Validator::make($r->all(), [
            'nama_jenis' => 'required|string',
         ]);
         if($validator->fails()){
             return response()->json('invalid input');
         }
         $mobil = jenis_mobil::create([
            'nama_jenis' => $r->nama_jenis,
         ]);
         if($mobil){
             $status = "berhasil tambah";
             return response()->json(compact('status'));
         }
         else{
             $status = "gagal tambah";
             return response()->json(compact('status'));
         }
     }
     else{
         return response('anda bukan admin');
     }
     }
     public function update(request $r){
     if(Auth::user()->level == 1){
         $validator=Validator::make($r->all(), [
             'nama_awal' => 'required|string',
             'nama_baru' => 'required'
         ]);
         $jenis = DB::table('jenis')->where('nama_jenis','like','%'.$r->nama_awal.'%')
         ->select('jenis.*')
         ->first();
         $mobil = jenis_mobil::where('id',$jenis->id)->update([
             'nama_jenis' => $r->nama_baru,
         ]);
         if($mobil){
             $status = "berhasil update";
             return response()->json(compact('status'));
         }
         else{
             $status = "gagal update";
             return response()->json(compact('status'));
         }
     }
     else{
         return response('anda bukan admin');
     }
     }

     public function get(){
     if(Auth::user()->level == 1){
         $mobil = DB::table('jenis')->select('jenis.nama_jenis')->get();
         $jenis_mobil = array();
         foreach($mobil as $m){
             $jenis_mobil[] = array(
                 'nama jenis' => $m->nama_jenis
             );
         }
         return response()->json(compact('jenis_mobil'));
        }
     else{
         return response('anda bukan admin');
     }
     }

     public function delete(request $r){
        if(Auth::user()->level == 1){
            $delete = jenis_mobil::where('id',$r->id)->delete();
            if($delete){
                $status = "berhasil hapus";
                return response()->json(compact('status'));
            }
            else{
                $status = "gagal hapus";
                return response()->json(compact('status'));
            }
        }
        else{
            return response('anda bukan admin');
        }
        }
 
}
