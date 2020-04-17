<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Session;
use Auth;
use DB;

class petugasc extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_petugas' => 'required|string',
            'alamat' => 'required|string',
            'telp' => 'required',
            'nik' => 'required|max:16',
            'username' => 'required',
            'password' => 'required',
            'level' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'nama_petugas' => $request->get('nama_petugas'),
            'alamat' => $request->get('alamat'),
            'telp' => $request->get('telp'),
            'nik' => $request->get('nik'),
            'username' => $request->get('username'),
            'password' => Hash::make($request->get('password')),
            'level' => $request->get('level')
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }
    public function get(request $r){
        $user = DB::table('petugas')->where('id','=',$r->id,'or','nama','like','%'.$r->nama.'%','or',
                                             'telp','like','%'.$r->telp.'%','or','alamat','like','%'.$r->alamat.'%')
                                    ->select('petugas.*')
                                    ->get();
        $pencarian = $r->all(); 
        $hasil = array();
        foreach($user as $u){
            $hasil[] = array(
                'nama' => $u->nama,
                'telepon' => $u->telp,
                'alamat' => $u->alamat
            );
        }
        return response()->json(compact('pencarian','hasil'));  
    }
    public function update(request $r,$id){

          $user = User::where('id',$id)->update([
              'nama' => $r->nama,
              'alamat' => $r->alamat,
              'telp' => $r->telp,
              'nik' => $r->nik,
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

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }
}

