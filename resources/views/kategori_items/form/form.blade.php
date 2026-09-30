<form method="POST">
    @csrf

    @if ($method == 'edit')
        <div class="form-group">
            <label>Kode Kategori</label>
            <input type="text" class="form-control" readonly value="{{ $item->kode ?? '' }}">
        </div>
    @endif

    <div class="form-group">
        <label>Nama</label>
        <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" required
            value="{{ old('nama', $item->nama ?? '') }}">
        @error('nama')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label>Item dalam Kategori</label>
        <input type="text" class="form-control form-control-sm mb-2" id="cari-item"
            placeholder="Cari item berdasarkan kode atau nama...">

        @php $checked = old('item_ids', $selected_items); @endphp
        <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;" id="daftar-item">
            @forelse ($master_items as $mi)
                <div class="form-check item-row" data-cari="{{ strtolower($mi->kode . ' ' . $mi->nama) }}">
                    <input class="form-check-input" type="checkbox" name="item_ids[]" value="{{ $mi->id }}"
                        id="item-{{ $mi->id }}" @checked(in_array($mi->id, $checked))>
                    <label class="form-check-label" for="item-{{ $mi->id }}">
                        {{ $mi->kode }} - {{ $mi->nama }}
                    </label>
                </div>
            @empty
                <span class="text-muted">Belum ada Master Item.</span>
            @endforelse
        </div>
        <small class="form-text text-muted">Centang item yang masuk kategori ini. Satu item boleh masuk beberapa
            kategori.</small>
        @error('item_ids.*')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <button class="btn btn-primary mt-3">Submit</button>
</form>

<script>
    document.getElementById('cari-item').addEventListener('input', function() {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#daftar-item .item-row').forEach(function(row) {
            row.style.display = row.dataset.cari.includes(q) ? '' : 'none';
        });
    });
</script>
