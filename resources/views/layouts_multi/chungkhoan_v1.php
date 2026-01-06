<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCA Calculator - Vàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container-main {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            padding: 30px;
        }
        .input-group-custom {
            margin-bottom: 20px;
        }
        .input-group-custom label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .input-group-custom input {
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 14px;
        }
        .input-group-custom input:focus {
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
        }
        .btn-calculate {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 5px;
            width: 100%;
            margin-bottom: 20px;
        }
        .btn-calculate:hover {
            opacity: 0.9;
            color: white;
        }
        table {
            margin-top: 30px;
            border-collapse: collapse;
        }
        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        thead th {
            padding: 15px;
            font-weight: 600;
            text-align: center;
            border: none;
        }
        tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
            text-align: center;
            font-family: 'Courier New', monospace;
        }
        tbody tr:hover {
            background-color: #f5f5f5;
        }
        .stt {
            text-align: center;
            color: #666;
        }
        .price-cell {
            color: #d32f2f;
            font-weight: 600;
        }
        .lot-cell {
            color: #1976d2;
            font-weight: 600;
        }
        .loss-cell {
            color: #f57c00;
            font-weight: 600;
        }
        .loss-alert {
            background-color: #ffebee;
            color: #c62828;
            font-weight: 700;
        }
        .info-section {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .input-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .table-responsive {
            max-height: 50vh;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        #dcaTable {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container-main" style="max-width: 1200px; margin: 0 auto;">
        <h1>📊 DCA Calculator - Vàng</h1>
        
        <div class="info-section">
            <p><strong>Mục đích:</strong> Tính toán phương pháp DCA (Dollar Cost Averaging) để xem khi nào margin call, với bước giá giảm liên tục</p>
        </div>

        <div class="input-row">
            <div class="input-group-custom">
                <label for="priceStep">Bước Giá (USD)</label>
                <input type="number" id="priceStep" value="6" step="0.1" min="0.1" placeholder="Nhập bước giá">
            </div>
            <div class="input-group-custom">
                <label for="startPrice">Giá Vàng Khởi Đầu (USD)</label>
                <input type="number" id="startPrice" value="4400" step="1" min="0" placeholder="Nhập giá vàng">
            </div>
            <div class="input-group-custom">
                <label for="startLot">Số Lot Khởi Đầu</label>
                <input type="number" id="startLot" value="0.01" step="0.01" min="0.01" placeholder="Nhập số lot">
            </div>
            <div class="input-group-custom">
                <label for="steps">Số Bước (Giảm)</label>
                <input type="number" id="steps" value="200" step="1" min="1" placeholder="Số bước giảm giá">
            </div>
        </div>

        <!-- <button class="btn btn-lg btn-calculate" onclick="calculateDCA()">🔄 Tính Toán</button> -->

        <div class="table-responsive">
            <table class="table table-striped" id="dcaTable">
                <thead>
                    <tr>
                        <th style="width: 80px;">Số lệnh</th>
                        <th style="width: 150px;">Giá (USD)</th>
                        <th style="width: 150px;">Lệch Giá</th>
                        <th style="width: 150px;">Số Lot</th>
                        <th style="width: 150px;">Số Lot Luỹ Kế</th>
                        <th style="width: 180px;">Lỗ Luỹ Kế (USD)</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="6" class="text-center text-muted">Nhấn nút "Tính Toán" để bắt đầu</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function calculateDCA() {
            const priceStep = parseFloat(document.getElementById('priceStep').value) || 6;
            const startPrice = parseFloat(document.getElementById('startPrice').value) || 4400;
            const startLot = parseFloat(document.getElementById('startLot').value) || 0.01;
            const steps = parseInt(document.getElementById('steps').value) || 200;

            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = '';

            let totalLot = startLot; // Bắt đầu với 1 lệnh mua ở giá startPrice
            let totalCost = startPrice * startLot;
            let totalLoss = 0;

            for (let i = 1; i <= steps; i++) {
                const currentPrice = startPrice - (priceStep * i);
                
                if (currentPrice <= 0) break;

                const lotQuantity = startLot;
                const costAtThisPrice = currentPrice * lotQuantity;
                
                totalLot += lotQuantity;
                totalCost += costAtThisPrice;
                
                // Lỗ luỹ kế = (Giá hiện tại - Giá trung bình) × Tổng lot
                const avgPrice = totalCost / totalLot;
                totalLoss = (currentPrice - avgPrice) * totalLot;

                const row = document.createElement('tr');
                const isAlert = totalLoss < -5000; // Cảnh báo nếu lỗi > 5000 USD
                
                if (isAlert) {
                    row.classList.add('loss-alert');
                }

                // Lệch giá = Giá vàng ban đầu - Giá hiện tại
                const priceDifference = startPrice - currentPrice;

                row.innerHTML = `
                    <td class="stt">${i}</td>
                    <td class="price-cell">${currentPrice.toFixed(2)}</td>
                    <td class="price-cell">${priceDifference.toFixed(2)}</td>
                    <td class="lot-cell">${(i * startLot).toFixed(4)}</td>
                    <td class="lot-cell">${totalLot.toFixed(4)}</td>
                    <td class="loss-cell">${totalLoss.toFixed(2)}</td>
                `;

                tableBody.appendChild(row);
            }

            if (tableBody.children.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Không có dữ liệu - Kiểm tra lại các giá trị</td></tr>';
            }
        }

        // Tính toán tự động khi load trang
        window.addEventListener('load', () => {
            calculateDCA();
        });

        // Tính toán khi thay đổi input
        document.getElementById('priceStep').addEventListener('input', calculateDCA);
        document.getElementById('startPrice').addEventListener('input', calculateDCA);
        document.getElementById('startLot').addEventListener('input', calculateDCA);
        document.getElementById('steps').addEventListener('input', calculateDCA);
    </script>
</body>
</html>
