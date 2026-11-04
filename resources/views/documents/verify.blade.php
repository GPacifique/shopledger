<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document verification</title>
    <style>
        body { margin: 0; padding: 32px 16px; background: #f3f6f3; color: #23332f; font: 15px Arial, sans-serif; }
        main { max-width: 520px; margin: 40px auto; padding: 28px; background: #fff; border: 1px solid #e3e9e5; }
        h1 { margin-top: 0; color: #173a34; font-size: 22px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 11px 0; border-bottom: 1px solid #e9eeeb; text-align: left; }
        th { width: 38%; color: #71807a; font-weight: normal; }
    </style>
</head>
<body>
    <main>
        <h1>Document verified</h1>
        <table>
            <tr><th>Type</th><td>{{ $documentType }}</td></tr>
            <tr><th>Document number</th><td>{{ $documentNumber }}</td></tr>
            <tr><th>Date</th><td>{{ $documentDate }}</td></tr>
            <tr><th>Status</th><td>{{ $documentStatus }}</td></tr>
        </table>
    </main>
</body>
</html>
