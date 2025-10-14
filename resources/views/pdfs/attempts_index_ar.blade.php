<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <style>
        html,
        body {
            direction: rtl;
            text-align: right;
            font-family: "notokufi", sans-serif;
            font-size: 13px;
            line-height: 1.45;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            vertical-align: top;
        }

        thead {
            display: table-header-group;
        }

        th {
            background: #f5f5f5;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <h2 style="margin:0 0 6px 0;">نتائج الامتحانات</h2>
    <div style="font-size: 11px; color: #666">تم الإنشاء: {{ $generated_at->toDateTimeString() }}</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>الامتحان</th>
                <th>وقت البدء</th>
                <th>وقت التسليم</th>
                <th>النسبة %</th>
                <th>العلامة النهائية</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attempts as $a)
                <tr>
                    <td>{{ $a->id }}</td>
                    <td>{{ optional($a->exam)->title ?? '-' }}</td>
                    <td>{{ optional($a->started_at)->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>{{ optional($a->submitted_at)->format('Y-m-d H:i') ?? '-' }}</td>
                    <td class="center">{{ $a->score ?? '-' }}</td>
                    <td class="center">{{ optional($a->marksReport)->marks ?? ($a->mark ?? '-') }}</td>
                    <td class="center">
                        @if (optional($a->marksReport)->official)
                            {{ optional($a->marksReport)->marks >= (optional($a->exam)->pass_threshold ?? 50) ? 'ناجح' : 'راسب' }}
                        @else
                            {{ $a->passed ? 'ناجح' : 'راسب' }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
