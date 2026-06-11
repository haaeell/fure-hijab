<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            color: #1f2937;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.45;
        }

        .header {
            border-bottom: 2px solid #81C784;
            margin-bottom: 18px;
            padding-bottom: 12px;
        }

        .brand {
            color: #2D5A27;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        h1 {
            color: #111827;
            font-size: 22px;
            margin: 4px 0 8px;
        }

        .meta {
            color: #6b7280;
            font-size: 10px;
        }

        .meta span {
            display: inline-block;
            margin-right: 16px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background: #E8F5E9;
            color: #2D5A27;
            font-size: 9px;
            padding: 8px 6px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            border-bottom: 1px solid #e5e7eb;
            padding: 7px 6px;
            vertical-align: top;
        }

        tr:nth-child(even) td {
            background: #f9fafb;
        }

        .empty {
            color: #6b7280;
            padding: 32px;
            text-align: center;
        }

        .footer {
            color: #9ca3af;
            font-size: 9px;
            margin-top: 16px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="brand">FURE</div>
        <h1>{{ $title }}</h1>
        <div class="meta">
            <span>Periode: {{ $filters['start_date'] }} - {{ $filters['end_date'] }}</span>
            <span>Status: {{ $filters['status'] }}</span>
            <span>Dibuat: {{ $generatedAt }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach ($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $value)
                        <td>{{ is_numeric($value) ? number_format((float) $value, 0, ',', '.') : $value }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td class="empty" colspan="{{ count($headings) }}">Belum ada data untuk filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Total baris: {{ count($rows) }}</div>
</body>

</html>
