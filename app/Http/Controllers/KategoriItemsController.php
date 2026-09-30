<?php

namespace App\Http\Controllers;

use App\Models\KategoriItem;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KategoriItemsController extends Controller
{
    public function index()
    {
        return view('kategori_items.index.index');
    }

    public function search(Request $request)
    {
        $data_search = KategoriItem::query();

        if ($request->filled('kode')) {
            $data_search->where('kode', 'LIKE', '%' . $request->kode . '%');
        }

        if ($request->filled('nama')) {
            $data_search->where('nama', 'LIKE', '%' . $request->nama . '%');
        }

        $data = $data_search
            ->select('id', 'kode', 'nama')
            ->withCount('items')
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
            $item = null;
            $selected_items = [];
        } else {
            $item = KategoriItem::findOrFail($id);
            $selected_items = $item->items()->pluck('master_items.id')->toArray();
        }

        $data['item'] = $item;
        $data['method'] = $method;
        $data['master_items'] = MasterItem::select('id', 'kode', 'nama')->orderBy('nama')->get();
        $data['selected_items'] = $selected_items;

        return view('kategori_items.form.index', $data);
    }

    public function singleView($kode)
    {
        $data['data'] = KategoriItem::where('kode', $kode)
            ->with('items')
            ->firstOrFail();

        return view('kategori_items.single.index', $data);
    }

    public function formSubmit(Request $request, $method, $id = 0)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'integer|exists:master_items,id',
        ]);

        if ($method == 'new') {
            $kategori = new KategoriItem;

            // withTrashed agar kode tidak bentrok dengan kategori yang sudah dihapus
            $next = KategoriItem::withTrashed()->max('id') + 1;
            $kategori->kode = 'KTG' . str_pad($next, 4, '0', STR_PAD_LEFT);
        } else {
            $kategori = KategoriItem::findOrFail($id);
        }

        $kategori->nama = $request->nama;
        $kategori->save();

        // sinkronkan item: yang tidak dipilih dilepas, yang baru dipilih ditambahkan
        $kategori->items()->sync($request->item_ids ?? []);

        return redirect('kategori-items');
    }

    public function delete($id)
    {
        $kategori = KategoriItem::findOrFail($id);
        $kategori->items()->detach();
        $kategori->delete();

        return redirect('kategori-items');
    }

    public function downloadPdf($kode)
    {
        $data = KategoriItem::where('kode', $kode)
            ->with('items')
            ->firstOrFail();

        $pdf = Pdf::loadView('kategori_items.pdf.index', [
            'data' => $data,
            'printed_at' => now()->format('d-m-Y H:i:s'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('kategori-' . $data->kode . '.pdf');
    }
}