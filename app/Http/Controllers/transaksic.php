<?php

namespace App\Http\Controllers;

use App\transaksi;
use App\detail_transaksi;
use App\mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Session;
use Auth;
use DB;

class transaksic extends Controller
{
    public function add(request $r){
        if(Auth::user()->level == 2){
            $validator = Validator::make($r->all(), [
                'id_pelanggan' => 'required',
                'plat_nomer' => 'required',
                'nama_mobil' => 'required',
                'lama_pinjam' => 'required',
            ]);
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
            $tgl_pinjam = Date('Y-m-d H:i:s');
            $tgl_kembali = date('Y-m-d H:i:s', strtotime('+'.$r->lama_pinjam.' days',strtotime($tgl_pinjam)));
            $id_petugas = Auth::user()->id;
            $sql = DB::table('mobil')->where('plat_nomer','like','%'.$r->plat_nomer.'%','and','nama_mobil','like','%'.$r->nama_mobil.'%')
                   ->select('mobil.*')
                   ->first();
            if($sql->status == "dipinjam" || $sql->status ==  "tidak tersedia"){
                return response("mobil sedang dipinjam atau tidak tersedia");
            }
            else{
            $transaksi = transaksi::create([
                'tanggal_transaksi' => $tgl_pinjam,
                'id_pelanggan' => $r->id_pelanggan,
                'id_petugas' => $id_petugas,
                'tanggal_kembali' => $tgl_kembali,
            ]);
            $idnya = db::table('transaksi')->where('tanggal_transaksi','=',$tgl_pinjam,'and','tanggal_kembali','=',$tgl_kembali,
                   'and','id_pelanggan','=',$r->id_pelanggan)->select('transaksi.id')->first();
            $harga_sewa = 5000000;
            $denda = 500000;
            $lama = $r->lama_pinjam;
            $total = $lama*$harga_sewa;
            $detail_trans = detail_transaksi::create([
                'id_trans' => $idnya->id,
                'id_mobil' => $sql->id,
                'harga_sewa_per_hari' => $harga_sewa,
                'total_harga' => $total,
                'denda' => $denda,
                'status' => "dipinjam"
            ]);
            $mobil = mobil::where('id',$sql->id)->update([
                'status' => "dipinjam"
            ]);
            if($detail_trans){
                $status = "berhasil transaksi";
                return response()->json(compact('status'));
            }
            else{
                $status = "gagal transaksi";
                return response()->json(compact('status'));
            }
        }
        }
        else{
            return response('anda bukan petugas');
        }
    }

    public function kembali(request $r){
        if(Auth::user()->level == 2){
            $tgl = date('Y-m-d H:i:s');
            $validator = Validator::make($r->all(), [
                'id_pelanggan' => 'required',
                'plat_nomer' => 'required',
                'nama_mobil' => 'required',
                'id_trans' => 'required'
            ]);
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
            $sql_mobil = DB::table('mobil')->where('plat_nomer','like','%'.$r->plat_nomer.'%','and','nama_mobil','like','%'.$r->nama_mobil.'%')
                         ->select('mobil.*')
                         ->first();
            $sql_trans = DB::table('transaksi')->where('id','=',$r->id_trans)
                         ->select('transaksi.*')
                         ->first();
            $update = transaksi::where('id',$r->id_trans)->update([
                'tanggal_dikembalikan' => date('Y-m-d H:i:s')
            ]);
            if($sql_trans->tanggal_kembali >= $tgl){
                $sql_detail = DB::table('detail_transaksi')->where('id_trans','=',$r->id_trans,'and','status','like','%belum kembali%')->update([
                          'total_keterlambatan' => 0,
                          'total_denda' => 0,
                          'status' => "kembali"
            ]) ;
            $mobel = mobil::where('id',$sql_mobil->id)->update([
                'status' => 'tersedia',
                ]);
                if($sql_detail){
                    $status = "berhasil kembali";
                    return response()->json(compact('status'));
                }
                else{
                    $status = "gagal kembali";
                    return response()->json(compact('status'));
                }
            }
            else{
                $tgl_kembali = $sql_trans->tanggal_kembali;
                $terlambat = $tgl-$tgl_kembali;
                $denda = $terlambat * 500000;
                $sql_detail = DB::table('detail_transaksi')->where('id_trans','=',$r->id_trans,'and','status','like','%belum kembali%')->update([
                    'total_keterlambatan' => $terlambat,
                    'total_denda' => $denda,
                    'status' => "kembali"
                ]);
                $mobel = mobil::where('id',$sql_mobil->id)->update([
                    'status' => 'tersedia',
                    ]);
                if($sql_detail){
                    $status = "berhasil kembali";
                    return response()->json(compact('status'));
                }
                else{
                    $status = "gagal kembali";
                    return response()->json(compact('status'));
                }
            }
        }
        else{
            return response('anda bukan petugas');
        }
    }

    public function get(request $r){
        if(Auth::user()->level == 2){
            $validator = Validator::make($r->all(), [
                'tgl_awal' => 'required',
                'tgl_akhir' => 'required',
            ]);
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
            $sql = DB::table('transaksi')->join('petugas','transaksi.id_petugas','=','petugas.id')
                   ->join('pelanggan','transaksi.id_pelanggan','=','pelanggan.id')
                   ->where('transaksi.tanggal_transaksi','<=',$r->tgl_akhir)
                   ->where('transaksi.tanggal_transaksi','>=',$r->tgl_awal)
                   ->select('transaksi.id','transaksi.tanggal_transaksi','transaksi.tanggal_kembali','transaksi.tanggal_dikembalikan','pelanggan.nama_pelanggan','petugas.nama_petugas')
                   ->get();
            $hasil = array();
            foreach($sql as $s){
                $sql2 = DB::table('detail_transaksi')->join('mobil','detail_transaksi.id_mobil','=','mobil.id')
                        ->where('detail_transaksi.id_trans','=',$s->id)
                        ->select('detail_transaksi.*','mobil.nama_mobil','mobil.plat_nomer')
                        ->first();
                $hasil2 = array();
               
                    $hasil2[] = array(
                        'nama mobil' => $sql2->nama_mobil,
                        'plat nomer' => $sql2->plat_nomer,
                        'harga sewa per hari' => $sql2->harga_sewa_per_hari,
                        'total harga sewa' => $sql2->total_harga,
                        'total keterlambatan' => ''.$sql2->total_keterlambatan.' hari',
                        'total denda' => 'rp'.$sql2->total_denda.'',
                        'status' => $sql2->status
                    );
                
                $hasil[] = array(
                    'tanggal transaksi' => $s->tanggal_transaksi,
                    'nama pelanggan' => $s->nama_pelanggan,
                    'petugas yang melayani' => $s->nama_petugas,
                    'tanggal wajib kembali' => $s->tanggal_kembali,
                    'tanggal dikembalikan' => $s->tanggal_dikembalikan,
                    'detail transaksi' => $hasil2
                );   
            }
            $pencarian = $r->all();
            return response()->json(compact('pencarian','hasil'));
        }
        else{
            return response('anda bukan petugas');
        }
    }
}
