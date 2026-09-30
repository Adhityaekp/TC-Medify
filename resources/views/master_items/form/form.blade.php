<form method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method == 'edit')
        <div class="form-group">
            <label>Kode Barang</label>
            <input type="text" class="form-control" name="kode_barang" required readonly value="{{ $item->kode ?? '' }}">
        </div>
    @endif

    <div class="form-group">
        <label>Nama</label>
        <input type="text" class="form-control" name="nama" required value="{{ $item->nama ?? '' }}">
    </div>

    <div class="form-group">
        <label>Harga Beli</label>
        <input type="text" inputmode="numeric" class="form-control" id="harga_beli_display" placeholder="Rp 0"
            autocomplete="off" required>
        {{-- nilai asli (angka) yang dikirim ke server --}}
        <input type="hidden" name="harga_beli" id="harga_beli"
            value="{{ old('harga_beli', $item->harga_beli ?? '') }}">
    </div>

    <div class="form-group">
        <label>Laba (dalam persen)</label>
        <input type="number" min="0" class="form-control" name="laba" id="laba" required
            value="{{ old('laba', $item->laba ?? '') }}">
    </div>

    <div class="alert alert-info py-2 mt-3">
        Preview Harga Jual: <strong id="preview_harga_jual">Rp 0</strong>
        <small class="d-block text-muted">Harga Beli + (Harga Beli x Laba / 100)</small>
    </div>

    @php $selected = $item->supplier ?? ''; @endphp
    <div class="form-group">
        <label>Supplier</label>
        <select class="form-control" required name="supplier">
            <option @if ($selected == '') selected @endif value="">--Pilih--</option>
            <option @if ($selected == 'Tokopaedi') selected @endif>Tokopaedi</option>
            <option @if ($selected == 'Bukulapuk') selected @endif>Bukulapuk</option>
            <option @if ($selected == 'TokoBagas') selected @endif>TokoBagas</option>
            <option @if ($selected == 'E Commurz') selected @endif>E Commurz</option>
            <optio @if ($selected == 'Blublu') selected @endif>Blublu</option>
        </select>
    </div>

    @php $selected = $item->jenis ?? ''; @endphp
    <div class="form-group">
        <label>Jenis</label>
        <select class="form-control" required name="jenis">
            <option @if ($selected == '') selected @endif value="">--Pilih--</option>
            <option @if ($selected == 'Obat') selected @endif>Obat</option>
            <option @if ($selected == 'Alkes') selected @endif>Alkes</option>
            <option @if ($selected == 'Matkes') selected @endif>Matkes</option>
            <optio @if ($selected == 'Umum') selected @endif>Umum</option>
                <optio @if ($selected == 'ATK') selected @endif>ATK</option>
        </select>
    </div>

    <div class="form-group">
        <label>Kategori</label>

        @php $checked = old('kategori_ids', $selected_kategoris); @endphp
        <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
            @forelse ($kategoris as $kt)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="kategori_ids[]" value="{{ $kt->id }}"
                        id="kategori-{{ $kt->id }}" {{ in_array($kt->id, $checked) ? 'checked' : '' }}>
                    <label class="form-check-label" for="kategori-{{ $kt->id }}">
                        {{ $kt->kode }} - {{ $kt->nama }}
                    </label>
                </div>
            @empty
                <span class="text-muted">
                    Belum ada kategori. <a href="{{ url('kategori-items/form/new') }}">Buat kategori</a>
                </span>
            @endforelse
        </div>
        <small class="form-text text-muted">Boleh memilih lebih dari satu kategori.</small>

        @error('kategori_ids.*')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label>Foto</label>
        <input type="file" class="form-control @error('foto') is-invalid @enderror" name="foto" id="foto"
            accept=".jpg,.jpeg,.png">

        <small class="form-text text-muted">
            Format: JPG atau PNG. Ukuran maksimal 2 MB.
        </small>

        @error('foto')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror

        @if (!empty($item->foto))
            <div class="mt-2">
                <img src="{{ asset('storage/' . $item->foto) }}" width="120" alt="Foto">
                <small class="d-block text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
            </div>
        @endif
    </div>

    <button class="btn btn-primary mt-3">Submit</button>

</form>
<script>
    document.getElementById('foto').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        const allowed = ['image/jpeg', 'image/png'];
        if (!allowed.includes(file.type)) {
            alert('Foto harus berformat JPG atau PNG.');
            this.value = '';
        } else if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran foto maksimal 2 MB.');
            this.value = '';
        }
    });

    (function() {
        const display = document.getElementById('harga_beli_display');
        const hidden = document.getElementById('harga_beli');
        const laba = document.getElementById('laba');
        const preview = document.getElementById('preview_harga_jual');
        const nf = new Intl.NumberFormat('id-ID');

        function rupiah(n) {
            return 'Rp ' + nf.format(n);
        }

        function updatePreview() {
            const beli = parseInt(hidden.value) || 0;
            const persen = parseFloat(laba.value) || 0;
            preview.textContent = rupiah(Math.round(beli + beli * persen / 100));
        }

        // saat mengetik: ambil angkanya saja, simpan ke hidden, tampilkan berformat
        display.addEventListener('input', function() {
            const digits = this.value.replace(/\D/g, '');
            hidden.value = digits;
            this.value = digits ? rupiah(parseInt(digits)) : '';
            updatePreview();
        });

        laba.addEventListener('input', updatePreview);

        // saat halaman dibuka (mode edit atau setelah gagal validasi)
        if (hidden.value) display.value = rupiah(parseInt(hidden.value));
        updatePreview();
    })();
</script>
