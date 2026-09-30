<?php

namespace App\Http\Controllers;

use App\Models\MasterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MasterItemsController extends Controller
{
    public function index()
    {
        return view('master_items.index.index');
    }

    public function search(Request $request)
    {
        $data_search = MasterItem::query();

        if ($request->filled('kode')) {
            $data_search->where('kode', $request->kode);
        }

        if ($request->filled('nama')) {
            $data_search->where('nama', 'LIKE', '%' . $request->nama . '%');
        }

        $hargamin = $request->filled('hargamin') ? (int) $request->hargamin : null;
        $hargamax = $request->filled('hargamax') ? (int) $request->hargamax : null;

        // jika terbalik, tukar otomatis
        if ($hargamin !== null && $hargamax !== null && $hargamin > $hargamax) {
            [$hargamin, $hargamax] = [$hargamax, $hargamin];
        }

        if ($hargamin !== null) {
            $data_search->where('harga_beli', '>=', $hargamin);
        }

        if ($hargamax !== null) {
            $data_search->where('harga_beli', '<=', $hargamax);
        }

        $data = $data_search
            ->select('kode', 'nama', 'jenis', 'harga_beli', 'laba', 'supplier', 'foto')
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $item = [];
        } else {
            $item = MasterItem::find($id);
        }
        $data['item'] = $item;
        $data['method'] = $method;
        return view('master_items.form.index', $data);
    }

    public function singleView($kode)
    {
        $data['data'] = MasterItem::where('kode', $kode)->first();
        return view('master_items.single.index', $data);
    }

    public function formSubmit(Request $request, $method, $id = 0)
    {
        $request->validate([
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ], [
            'foto.mimes' => 'Foto harus berformat JPG atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
            'foto.uploaded' => 'Foto gagal diunggah, ukuran file mungkin terlalu besar.',
        ]);

        if ($method == 'new') {
            $data_item = new MasterItem;
            $kode = MasterItem::count('id');
            $kode = $kode + 1;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);
            sleep(3);
        } else {
            $data_item = MasterItem::find($id);
            $kode = $data_item->kode;
        }

        $data_item->nama = $request->nama;
        $data_item->harga_beli = $request->harga_beli;
        $data_item->laba = $request->laba;
        $data_item->kode = $kode;
        $data_item->supplier = $request->supplier;
        $data_item->jenis = $request->jenis;

        if ($request->hasFile('foto')) {
            // hapus foto lama jika ada
            if ($data_item->foto) {
                Storage::disk('public')->delete($data_item->foto);
            }
            $data_item->foto = $this->convertToWebp($request->file('foto'));
        }

        $data_item->save();

        return redirect('master-items');
    }

    public function delete($id)
    {
        MasterItem::find($id)->delete();
        return redirect('master-items');
    }

    public function updateRandomData()
    {
        $data = MasterItem::get();
        foreach ($data as $item) {
            $kode = $item->id;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);

            $item->harga_beli = rand(100, 1000000);
            $item->laba = rand(10, 99);
            $item->kode = $kode;
            $item->supplier = $this->getRandomSupplier();
            $item->jenis = $this->getRandomJenis();
            $item->save();
        }
    }

    private function getRandomSupplier()
    {
        $array = ['Tokopaedi', 'Bukulapuk', 'TokoBagas', 'E Commurz', 'Blublu'];
        $random = rand(0, 4);
        return $array[$random];
    }

    private function getRandomJenis()
    {
        $array = ['Obat', 'Alkes', 'Matkes', 'Umum', 'ATK'];
        $random = rand(0, 4);
        return $array[$random];
    }

    private function convertToWebp($file, $maxWidth = 1000, $quality = 80)
    {
        $mime = $file->getMimeType();

        if ($mime === 'image/jpeg') {
            $image = imagecreatefromjpeg($file->getRealPath());

            // perbaiki orientasi foto dari HP (EXIF)
            if (function_exists('exif_read_data')) {
                $exif = @exif_read_data($file->getRealPath());
                if (!empty($exif['Orientation'])) {
                    $image = match ($exif['Orientation']) {
                        3 => imagerotate($image, 180, 0),
                        6 => imagerotate($image, -90, 0),
                        8 => imagerotate($image, 90, 0),
                        default => $image,
                    };
                }
            }
        } else {
            $image = imagecreatefrompng($file->getRealPath());
            imagepalettetotruecolor($image);
            imagealphablending($image, false);
            imagesavealpha($image, true); // pertahankan transparansi PNG
        }

        // perkecil jika lebih lebar dari $maxWidth (rasio tetap)
        if (imagesx($image) > $maxWidth) {
            $image = imagescale($image, $maxWidth);
        }

        ob_start();
        imagewebp($image, null, $quality);
        $binary = ob_get_clean();
        imagedestroy($image);

        $path = 'items/' . Str::uuid() . '.webp';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }
}
