<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إشعار تسجيل طالب جديد</title>
</head>
<body>
    <h2>إشعار تسجيل طالب جديد</h2>
    
    <p>تم تسجيل طالب جديد في النظام:</p>
    
    <ul>
        <li><strong>الاسم: </strong> {{ $user->name }}</li>
        <li><strong>البريد الإلكتروني: </strong> {{ $user->email }}</li>
        <li><strong>رقم الهاتف: </strong> {{ $user->phone }}</li>
        <li><strong>تاريخ التسجيل: </strong> {{ now()->format('Y-m-d H:i') }}</li>
    </ul>

    <p>يمكنك مراجعة الطلب من لوحة التحكم.</p>
    
    <p>شكرًا لكم!</p>
</body>
</html>