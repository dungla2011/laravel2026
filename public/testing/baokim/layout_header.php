<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Thanh Toán BaoKim'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 800px;
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 25px;
        }
        .header h1 {
            font-size: 1.8rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            margin-bottom: 8px;
        }
        .header .lead {
            font-size: 0.95rem;
        }
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .icon-large {
            font-size: 2.5rem;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .content-card h2 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .btn-primary-custom {
            padding: 10px 30px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 25px;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary-custom:hover {
            transform: scale(1.05);
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .alert-custom {
            border-radius: 10px;
            padding: 12px 15px;
            margin: 15px 0;
            font-size: 0.9rem;
        }
        .amount-display {
            font-size: 1.8rem;
            font-weight: bold;
            color: #667eea;
            margin: 15px 0;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 3px solid #667eea;
            padding: 12px;
            margin: 12px 0;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px;
            margin: 15px 0;
            font-size: 0.75rem;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-wallet"></i> <?php echo $pageTitle ?? 'Thanh Toán'; ?></h1>
            <?php if (isset($pageDescription)): ?>
            <p class="lead"><?php echo $pageDescription; ?></p>
            <?php endif; ?>
        </div>
