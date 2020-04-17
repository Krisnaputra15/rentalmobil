<?php

namespace App\Http\Controllers;

use App\mobil;
use App\jenis_mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use DB;

class mobilc extends Controller
{
    public function add(request $r){
       if(Auth::user()->level == 1){
        $validator=Validator::make($r->all(), [
           'nama_mobil' => 'required|string',
           'plat_nomer' => 'required|string',
           'kondisi' => 'required',
           'status' => 'required',
           'jenis_mobil' => 'required'
        ]);
        if($validator->fails()){
            return response()->json('invalid input');
        }
        $jenis = DB::table('jenis')->where('nama_jenis','like','%'.$r->jenis_mobil.'%')
                 ->select('jenis.*')
                 ->first();
        $mobil = mobil::create([
            'nama_mobil' => $r->nama_mobil,
            'plat_nomer' => $r->plat_nomer,
            'kondisi' => $r->kondisi,
            'status' => $r->status,
            'id_jenis' => $jenis->id
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
    public function update(request $r,$id){
    if(Auth::user()->level == 1){
        $validator=Validator::make($r->all(), [
            
            'nama_mobil' => 'required|string',
            'plat_nomer' => 'required|string',
            'kondisi' => 'required',
            'status' => 'required',
            'jenis_mobil' => 'required'
        ]);
        $jenis = DB::table('jenis')->where('nama_jenis','like','%'.$r->jenis_mobil.'%')
        ->select('jenis.*')
        ->first();
        $id = $r->id;
        $mobil = mobil::where('id',$id)->update([
            'nama_mobil' => $r->nama_mobil,
            'plat_nomer' => $r->plat_nomer,
            'kondisi' => $r->kondisi,
            'status' => $r->status,
            'id_jenis' => $jenis->id
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

    public function get(Request $r){
    if(Auth::user()->level == 1){
        if($r->jenis != null){
            $jenis = DB::table('jenis')->where('nama_jenis','like','%'.$r->jenis_mobil.'%')
            ->select('jenis_mobil.*')
            ->first();
            $mobil = DB::table('mobil')->join('jenis','mobil.id_jenis','=','jenis.id')
            ->where('id','=',$r->id,'or','nama_mobil','like','%'.$r->nama_mobil.'%','or',
              'plat_nomer','like','%'.$r->plat.'%','or','kondisi','like','%'.$r->kondisi.'%',
              'or','status','like','%'.$r->status.'%','or','id_jenis','=',$jenis->id)
            ->select('mobil.*','jenis.*')
            ->get();
        }
        $mobil = DB::table('mobil')->join('jenis','jenis.id','=','mobil.id_jenis')
                 ->where('nama_mobil','like','%'.$r->nama_mobil.'%','or',
                   'plat_nomer','like','%'.$r->plat.'%','or','kondisi','like','%'.$r->kondisi.'%',
                   'or','status','like','%'.$r->status.'%')
                 ->select('mobil.nama_mobil','mobil.plat_nomer','mobil.status','mobil.kondisi','jenis.*')
                 ->get();
                                
        $hasil = array();
        foreach($mobil as $m){
            $hasil[] = array(
                'nama mobil' => $m->nama_mobil,
                'plat nomer' => $m->plat_nomer,
                'kondisi' => $m->kondisi,
                'status' => $m->status,
                'jenis mobil' => $m->nama_jenis
            );
        }
        if($mobil){
            $result[] = array(
                'pencarian' => $r->all(),
                'hasil' => $hasil
            );
            return response()->json(compact('result'));
        }
        else{
            return response('pencarian gagal');
        }
    }
    else{
        return response('anda bukan admin');
    }
    }

    public function delete($id){
    if(Auth::user()->level == 1){
        $delete = mobil::where('id',$id)->delete();
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
