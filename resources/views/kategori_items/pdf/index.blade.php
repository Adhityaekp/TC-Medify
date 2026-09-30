<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kategori {{ $data->kode }}</title>
    <style>
        @page {
            margin: 30px 40px 60px 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        h2 {
            margin: 0 0 15px 0;
        }

        table.info td {
            padding: 2px 6px 2px 0;
        }

        table.list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table.list th,
        table.list td {
            border: 1px solid #999;
            padding: 5px 7px;
            text-align: left;
        }

        table.list th {
            background: #eee;
        }

        .text-center {
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -35px;
            left: 0;
            right: 0;
            font-size: 10px;
            color: #555;
            border-top: 1px solid #999;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <h2>Detail Kategori Item</h2>

    <table class="info">
        <tr>
            <th align="left">Kode</th>
            <td>:</td>
            <td>{{ $data->kode }}</td>
        </tr>
        <tr>
            <th align="left">Nama</th>
            <td>:</td>
            <td>{{ $data->nama }}</td>
        </tr>
        <tr>
            <th align="left">Jumlah Item</th>
            <td>:</td>
            <td>{{ $data->items->count() }}</td>
        </tr>
    </table>

    <table class="list">
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Kode</th>
                <th>Nama Item</th>
                <th>Jenis</th>
                <th>Supplier</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data->items as $i => $mi)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $mi->kode }}</td>
                    <td>{{ $mi->nama }}</td>
                    <td>{{ $mi->jenis }}</td>
                    <td>{{ $mi->supplier }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada item di kategori ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <footer>Dicetak pada: {{ $printed_at }}</footer>
</body>

</html>
