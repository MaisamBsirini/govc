<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الشكاوى</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            text-align: right;
            margin: 20px;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            direction: rtl;
            text-align: right;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px 10px;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        footer {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
            color: gray;
        }
    </style>
</head>
<body>
    <h2>📋 تقرير الشكاوى</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>المستخدم</th>
                <th>القسم</th>
                <th>النوع</th>
                <th>الوصف</th>
                <th>الحالة</th>
                <th>تاريخ الإنشاء</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($complaints as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->user->name ?? 'غير معروف' }}</td>
                    <td>{{ $c->department }}</td>
                    <td>{{ $c->type }}</td>
                    <td>{{ $c->description }}</td>
                    <td>{{ $c->status }}</td>
                    <td>{{ $c->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        تم توليد هذا التقرير بواسطة نظام الشكاوى الحكومي - {{ now()->format('Y-m-d H:i') }}
    </footer>
</body>
</html>
