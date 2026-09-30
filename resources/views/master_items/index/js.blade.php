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
                targets: [1, 4, 8]
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
                kategori_id: $('#filter-kategori').val(),
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

                    var kategori = item.kategoris.length ?
                        item.kategoris.map(function(k) {
                            return $('<span class="badge bg-primary me-1"></span>').text(k.nama)
                                .prop('outerHTML');
                        }).join('') :
                        '-';

                    rows.push([
                        kode,
                        foto,
                        item.nama,
                        item.jenis,
                        kategori,
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
</script>
