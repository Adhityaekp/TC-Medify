<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>

<script>
    var start_date = '';
    var end_date = '';
    var data_per_fetch = 500;
    var data_fetched = 0;

    $(document).ready(function() {
        $('#table').DataTable({
            searching: false,
            order: [
                [0, 'desc']
            ],
            columnDefs: [{
                orderable: false,
                targets: [1, 7]
            }],
        });
        getData()
    });

    $('.btn-get-data').click(function() {
        getData()
    })

    function getData() {
        $('#loading-filter').show();
        var dataTableObj = $('#table').DataTable();
        dataTableObj.clear().draw();

        $.ajax({
            url: '{{ url('master-items/search') }}',
            dataType: 'json',
            tryCount: 0,
            retryLimit: 3,
            data: {
                kode: $('#filter-kode').val(),
                nama: $('#filter-nama').val(),
                hargamin: $('#filter-harga-min').val(),
                hargamax: $('#filter-harga-max').val()
            },
            success: function(results) {
                var rows = [];

                $.each(results.data, function(index, item) {
                    var harga_jual = Math.round(item.harga_beli + item.harga_beli * item.laba /
                    100);
                    var kode = item.kode;

                    var foto = item.foto ?
                        `<img src="{{ asset('storage') }}/` + item.foto +
                        `" width="50" class="img-thumbnail">` :
                        '-';

                    var html = `<a href="{{ url('master-items/view/') }}/` + kode +
                        `" class="btn btn-primary">View</a>`;

                    rows.push([
                        kode,
                        foto,
                        item.nama,
                        item.jenis,
                        item.harga_beli,
                        harga_jual,
                        item.supplier,
                        html
                    ]);
                });

                // tambahkan semua baris sekaligus, draw hanya sekali
                dataTableObj.rows.add(rows).draw();
                $('#loading-filter').hide();
            },
            error: function() {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    $.ajax(this);
                    return;
                }
                alert('Terjadi kesalahan server, tidak dapat mengambil data');
                $('#loading-filter').hide();
            }
        });
    }

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
</script>
