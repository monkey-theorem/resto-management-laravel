<?php

namespace App\Http\Controllers;
use App\Masakan;
use App\Kategori;
use Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasakanController extends Controller
{
    public function daftar(Request $req)
    {
    	// $data = Masakan::where('nama_masakan','like',"%{$req->keyword}%")->paginate(10);
    	// return view('admin.pages.masakan.daftar', ['data'=>$data]);
        $data = Masakan::join('kategori','kategori.id','masakan.kategori_id')
            ->where('nama_masakan','like',"%{$req->keyword}%")
            ->select('masakan.*','nama_kategori')
            ->orderBy('updated_at','desc')
            ->paginate(10);
            return view('admin.pages.masakan.daftar', ['data'=>$data]);

    }

     public function add()
    {
    	return view('admin.pages.masakan.add');
    }

    public function save(Request $req)
    {
        //Ambil ID Terakhir
        $id = Masakan::getId();
        foreach ($id as $value);
        $idLama = $value->id;
        $idBaru = $idLama + 1;

        $kode_mkn = 'MKN'.$idBaru;
        
    	\Validator::make($req->all(), [
    		'nama_masakan'=>'required|between:3,100',
    		'harga'=>'required|numeric',
            'gambar'=>'required|image',
    		'status_masakan'=>'required',
    	])->validate();

        $filename = rand(1,999).'_'.str_replace(' ', '', $req->gambar->getClientOriginalName());

        $req->file('gambar')->storeAs('/public/gambar', $filename);

    	$result = new Masakan;
        $result->kode_masakan = $kode_mkn.sprintf("%02s", $req->kode_masakan);
        $result->kategori_id = $req->kategori_id;
    	$result->nama_masakan = $req->nama_masakan;
        $result->gambar = $filename;
        $result->harga = $req->harga;
    	$result->status_masakan = $req->status_masakan;

    	if ($result->save()) {
            alert()->success('Data Berhasil Tersimpan ke Database.', 'Tersimpan!')->autoclose(4000);
    		return redirect('/admin/masakan');
    	} else {
    		return back()->with('result','fail');
    	}
    	
    }

    public function edit($id)
    {
        $data = Masakan::where('id',$id)->first();
        return view('admin.pages.masakan.edit', ['rc'=>$data]);
    }

    public function update (Request $req)
    {
        $filename = rand(1,999).'_'.str_replace(' ', '', $req->gambar->getClientOriginalName());
        $req->file('gambar')->storeAs('/public/gambar', $filename);

        \Validator::make($req->all(), [
            'nama_masakan'=>'required',
            'harga'=>'numeric',
            'status_masakan'=>'required',
            'gambar'=>'required|image','.$req->id.',
        ])->validate();

        $field = [
                'nama_masakan'=>$req->nama_masakan,
                'harga'=>$req->harga,
                'status_masakan'=>$req->status_masakan,
                'gambar'=> $filename,
            ];
 
        $result = Masakan::where('id',$req->id)->update($field);

        if ($result) {
            alert()->success('Berhasil Mengupdate Data.', 'Terupdate!')->autoclose(4000);
            return redirect('/admin/masakan');
        } else {
            return back()->with('result','fail');
        }


    }


    public function delete(Request $req)
    {
        $result = Masakan::find($req->id);

        if ($result->delete() ){
            alert()->success('Data Berhasil Terhapus dari Database.', 'Terhapus!')->autoclose(3000);
            return redirect('/admin/masakan/');
        }
        
    }

     public function updateIMG (Request $req)
    {
        $result = Masakan::find($req->id);
        \Validator::make($req->all(), [
            'gambar'=>'image',
        ])->validate();

        
        if($req->file('gambar') == "")
            {
                $update->gambar = $update->gambar;
            } 
            else
            {
                $file       = $req->file('gambar');
                $fileName   = $file->getClientOriginalName();
                $req->file('gambar')->move('public/gambar', $fileName);
                $update->gambar = $fileName;
            }

            return $update;
        // if ($update) {
        //     alert()->success('Berhasil Mengupdate Gambar.', 'Terupdate!')->autoclose(4000);
        //     return redirect('/admin/masakan');
        // } else {
        //     return back()->with('result','fail');
        // }
    }

    //READ KATEGORI
    public function daftarKategori(Request $req)
    {
        $data = Kategori::where('nama_kategori','like',"%{$req->keyword}%")->orderBy('updated_at','desc')->paginate(10);
        return view('admin.pages.masakan.kategori.daftar', ['data'=>$data]);

    }

    public function addKategori()
    {
        return view('admin.pages.masakan.kategori.add');
    }

    public function saveKategori(Request $req)
    {
        
        \Validator::make($req->all(), [
            'nama_kategori'=>'required|between:3,100',
        ])->validate();

        $result = new Kategori;
        
        $result->nama_kategori  = $req->nama_kategori ;

        if ($result->save()) {
            alert()->success('Data Berhasil Tersimpan ke Database.', 'Tersimpan!')->autoclose(4000);
            return redirect('/admin/masakan/kategori');
        } else {
            return back()->with('result','fail');
        }
        
    }

    public function editKategori($id)
    {
        $data = Kategori::where('id',$id)->first();
        return view('admin.pages.masakan.kategori.edit', ['rc'=>$data]);
    }

    public function updateKategori(Request $req)
    {
        \Validator::make($req->all(), [
            'nama_kategori'=>'required',
        ])->validate();

        $field = [
                'nama_kategori'=>$req->nama_kategori,
            ];
           
        $result = Kategori::where('id',$req->id)->update($field);
        
        if ($result) {
            alert()->success('Berhasil Mengupdate Data.', 'Terupdate!')->autoclose(4000);
            return redirect('/admin/masakan/kategori');
        } else {
            return back()->with('result','fail');
        }
    }

    public function deleteKategori(Request $req)
    {
        $result = Kategori::find($req->id);

        if ($result->delete() ){
            alert()->success('Data Berhasil Terhapus dari Database.', 'Terhapus!')->autoclose(3000);
            return redirect('/admin/masakan/kategori');
        }
        
    }


}
