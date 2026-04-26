@php
    $formatMoney = static function ($value) {
        if ($value === null || $value === '') {
            return '-';
        }

        $number = round((float) $value);
        $negative = $number < 0 ? '-' : '';
        $digits = (string) abs($number);
        $lastThree = substr($digits, -3);
        $remaining = substr($digits, 0, -3);

        if ($remaining !== false && $remaining !== '') {
            $lastThree = ',' . $lastThree;
            $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
        }

        return $negative . ($remaining ?: '') . $lastThree;
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 24px 28px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
        }

        .heading {
            margin-bottom: 18px;
        }

        .title {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .subtitle {
            font-size: 13px;
            margin: 0;
            color: #374151;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 8px 6px;
            vertical-align: middle;
        }

        th {
            background: #f3f4f6;
            font-size: 11px;
            font-weight: 700;
            text-align: center;
        }

        td {
            font-size: 11px;
        }

        .left {
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .total-row td {
            background: #f9fafb;
            font-weight: 700;
        }

        .small {
            display: block;
            font-size: 10px;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="heading">
        <p class="title">{{ $title }}</p>
        <p class="subtitle">{{ $clientName }} - {{ $clientReference }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Company</th>
                <th style="width: 18%;">Product Type</th>
                <th style="width: 12%;">Policy Number</th>
                <th style="width: 11%;">Inception Date</th>
                <th style="width: 11%;">Inception Value</th>
                @foreach ($periodLabels as $label)
                    <th style="width: 8.25%;">
                        Value
                        <span class="small">{{ $label }}</span>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td class="left">{{ $row['company'] }}</td>
                    <td class="left">{{ $row['product_type'] }}</td>
                    <td class="center">{{ $row['policy_number'] }}</td>
                    <td class="center">{{ $row['inception_date'] }}</td>
                    <td class="right">{{ $formatMoney($row['inception_value']) }}</td>
                    @foreach ($row['period_values'] as $value)
                        <td class="right">{{ $formatMoney($value) }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 5 + count($periodLabels) }}" class="center">
                        No policy data is available for the selected investor.
                    </td>
                </tr>
            @endforelse

            @if (count($rows) > 0)
                <tr class="total-row">
                    <td colspan="4" class="left">Total</td>
                    <td class="right">{{ $formatMoney($totals['inception_value']) }}</td>
                    @foreach ($totals['period_values'] as $value)
                        <td class="right">{{ $formatMoney($value) }}</td>
                    @endforeach
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
