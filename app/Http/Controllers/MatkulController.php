<?php

namespace App\Http\Controllers;

use App\Models\Matkul;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class MatkulController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'kode_mk' => 'required|unique:matkul',
            'nama_matkul' => 'required',
            'sks' => 'required',
            'semester' => 'required',
            'jadwal' => 'required',
            'ruangan' => 'required'
        ]);

        $matkul = Matkul::create([
            'kode_mk' => $request->kode_mk,
            'nama_matkul' => Crypt::encrypt($request->nama_matkul),
            'sks' => Crypt::encrypt($request->sks),
            'semester' => Crypt::encrypt($request->semester),
            'jadwal' => Crypt::encrypt($request->jadwal),
            'ruangan' => Crypt::encrypt($request->ruangan)
        ]);
        //tambahan
        $decrypt = array(
            'kode_mk' => $matkul->kode_mk,
            'nama_matkul' => Crypt::decrypt($matkul->nama_matkul),
            'sks' => Crypt::decrypt($matkul->sks),
            'semester' => Crypt::decrypt($matkul->semester),
            'jadwal' => Crypt::decrypt($matkul->jadwal),
            'ruangan' => Crypt::decrypt($matkul->ruangan)
        );
        //tambahan
        if ($matkul) {
            return response()->json([
                'status' => true,
                'message' => 'Mata Kuliah Berhasil ditambahkan',
                'data' => $decrypt
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Error',
            'data' => ''
        ], 402);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'kode_mk' => 'required',
            'nama_matkul' => 'required',
            'sks' => 'required',
            'semester' => 'required',
            'jadwal' => 'required',
            'ruangan' => 'required'
        ]);

        $matkul = Matkul::find($id);
        if ($matkul) {
            $matkul->kode_mk = $request->kode_mk;
            $matkul->nama_matkul = Crypt::encrypt($request->nama_matkul);
            $matkul->sks = Crypt::encrypt($request->sks);
            $matkul->semester = Crypt::encrypt($request->semester);
            $matkul->jadwal = Crypt::encrypt($request->jadwal);
            $matkul->ruangan = Crypt::encrypt($request->ruangan);
            $matkul->save();
            $decrypt =array(
                'kode_mk' => $matkul->kode_mk,
                'nama_matkul' => Crypt::decrypt($matkul->nama_matkul),
                'sks' => Crypt::decrypt($matkul->sks),
                'semester' => Crypt::decrypt($matkul->semester),
                'jadwal' => Crypt::decrypt($matkul->jadwal),
                'ruangan' => Crypt::decrypt($matkul->ruangan)
            );
            
            return response()->json([
                'status' => true,
                "message" => "Mata Kuliah Berhasil diubah!",
                "data" => $decrypt
            ], 200);
        }
        return response()->json([
            'status' => false,
            "message" => "Error!",
            "data" => ''
        ], 404);
    }

    public function destroy($id)
    {
        $matkul = Matkul::find($id);

        if ($matkul) {
            $matkul->delete();
            return response()->json([
                'status' => true,
                "message" => "Mata Kuliah berhasil dihapus!",
            ], 200);
        }
        return response()->json([
            'status' => false,
            "message" => "Error!",
            "data" => ''
        ], 404);
    }

    public function find($id)
    {
        $matkul = Matkul::find($id);
        $decrypt =array(
            'kode_mk' => Crypt::decrypt($matkul->kode_mk),
            'nama_matkul' => Crypt::decrypt($matkul->nama_matkul),
            'sks' => Crypt::decrypt($matkul->sks),
            'semester' => Crypt::decrypt($matkul->semester),
            'jadwal' => Crypt::decrypt($matkul->jadwal),
            'ruangan' => Crypt::decrypt($matkul->ruangan)
        );
        if ($matkul) {
            return response()->json([
                'status' => true,
                "message" => "Data ditemukan !",
                "data" => $decrypt
            ], 200);
        }
        return response()->json([
            'status' => false,
            "message" => "Data tidak ditemukan!",
            "data" => ''
        ], 404);
    }

    public function show()
    {
        $matkul = [];
        foreach(Matkul::all() as $key) {
            $data = [
                'kode_mk' => Crypt::decrypt($key->kode_mk),
                'nama_matkul' => Crypt::decrypt($key->nama_matkul),
                'sks' => Crypt::decrypt($key->sks),
                'semester' => Crypt::decrypt($key->semester),
                'jadwal' => Crypt::decrypt($key->jadwal),
                'ruangan' => Crypt::decrypt($key->ruangan)
            ];
            array_push($matkul,$data);
        }
        $matkul = Matkul::all();
        return response()->json([
            'status' => true,
            "message" => "Data tersedia !",
            "data" => $matkul,
        ], 200);
    }
}
