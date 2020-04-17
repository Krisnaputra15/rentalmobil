<?php

namespace App\Http\Controllers;

use App\pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Session;
use Auth;
use DB;

class pelangganc extends Controller
{
    public function add(request $request){
    if(Auth::user()->level == 1){
    $validator = Validator::make($request->all(), [
        'nama' => 'required|string',
        'alamat' => 'required|string',
        'telp' => 'required',
        'nik' => 'required|max:16',
        'username' => 'required',
        'password' => 'required',
    ]);

    if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
    }

    $user = pelanggan::create([
        'nama_pelanggan' => $request->get('nama'),
        'alamat' => $request->get('alamat'),
        'telp' => $request->get('telp'),
        'nik' => $request->get('nik'),
        'username' => $request->get('username'),
        'password' => $request->get('password'),
    ]);

    if($user){
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
            $validator = Validator::make($r->all(), [
                'id' => 'required',
                'nama' => 'required|string',
                'alamat' => 'required|string',
                'telp' => 'required',
                'nik' => 'required|max:16',
                'username' => 'required',
                'password' => 'required',
                
            ]);
        
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
        
            $user = pelanggan::where('id',$r->id)->update([
                'nama_pelanggan' => $r->get('nama'),
                'alamat' => $r->get('alamat'),
                'telp' => $r->get('telp'),
                'nik' => $r->get('nik'),
                'username' => $r->get('username'),
                'password' => Hash::make($r->get('password')),
    
            ]);
        
            if($user){
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

    public function get(request $r){
        if(Auth::user()->level == 1){
            $validator = Validator::make($r->all(), [
                'id' => 'required',
            ]);
        
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
        
            $user = DB::table('pelanggan')->where('id',$r->id)
                    ->select('pelanggan.*')
                    ->first();
            $data_pelanggan[] = array(
                'nama pelanggan' => $user->nama_pelanggan,
                'alamat' => $user->alamat,
                'nomer telepon' => $user->telp,
                'NIK' => $user->nik
            );
        
            if($user){
                return response()->json(compact('data_pelanggan'));
            }
            else{
                $status = "gagal mencari data pelanggan";
                return response()->json(compact('status'));
            }
            }
            else{
                return response('anda bukan admin');
            }
    }

    public function delete(request $r){
        if(Auth::user()->level == 1){
            $delete = pelanggan::where('id',$r->id)->delete();
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
