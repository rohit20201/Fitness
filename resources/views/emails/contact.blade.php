<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $details['title'] }}</h2>
        </div>

        <p>Hello,</p>

        <p>{{ $details['body'] }}:</p>

        <ul>
            <li><strong>Name:</strong> {{ $details['data']['name'] }}</li>
            <li><strong>Email:</strong> {{ $details['data']['email'] }}</li>
            <li><strong>Phone:</strong> {{ $details['data']['phone'] }}</li>
            <li><strong>City:</strong> {{ $details['data']['city'] }}</li>
            <li><strong>Message:</strong> {{ $details['data']['message'] }}</li>
        </ul>

        <p>Thank you!</p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Fitness</p>
        </div>
    </div>
</body>
</html>