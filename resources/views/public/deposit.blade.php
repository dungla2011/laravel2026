@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
    Nạp Tiền - Deposit Money
@endsection

@section("og_title")
    Nạp Tiền Vào Tài Khoản
@endsection

@section("css")

<style>
    .deposit-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 80vh;
        padding: 40px 0;
    }
    .deposit-header {
        text-align: center;
        color: white;
        margin-bottom: 50px;
    }
    .deposit-header h1 {
        font-size: 3rem;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    .deposit-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .deposit-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    }
    .deposit-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .deposit-name {
        font-size: 1.8rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
    }
    .deposit-description {
        color: #666;
        margin-bottom: 20px;
    }
    .btn-deposit {
        width: 100%;
        padding: 15px;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 50px;
        border: none;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transition: all 0.3s ease;
    }
    .btn-deposit:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    .modal-content {
        border-radius: 20px;
    }
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px 20px 0 0;
    }
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 40px;
        font-weight: bold;
        border-radius: 50px;
        color: white;
    }
    .btn-submit:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
</style>
@endsection

@section("content")
<div class="deposit-container">
    <div class="container">


        <div class="row justify-content-center my-10">
            <div class="col-md-6">
                <div class="deposit-card">
                    <div class="text-center">
                        <h3 class="deposit-name">Nạp Tiền Vào Tài Khoản</h3>
                    </div>

                    <form id="depositForm">
                        <div class="mb-4">
                            <label for="amountMoney" class="form-label">
                                <i class="fas fa-coins"></i> Số Tiền Nạp <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-lg" id="amountMoney"
                                   name="total_amount" required min="5000" step="1000"
                                   placeholder="Nhập số tiền (VNĐ)">
                            <small class="text-muted">Số tiền tối thiểu: 5,000 VNĐ</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Chọn nhanh:</label>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary" onclick="setAmount(10000)">
                                    10,000 VNĐ
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="setAmount(50000)">
                                    50,000 VNĐ
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="setAmount(100000)">
                                    100,000 VNĐ
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="setAmount(200000)">
                                    200,000 VNĐ
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="setAmount(500000)">
                                    500,000 VNĐ
                                </button>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-primary" onclick="showDepositModal()">
                                <i class="fas fa-credit-card"></i> Tiếp Tục Nạp Tiền
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="buyModal" tabindex="-1" aria-labelledby="buyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buyModalLabel">
                    <i class="fas fa-shopping-cart"></i> Xác Nhận Nạp Tiền
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="buyForm" method="GET" action="{{ route('deposit.payment') }}">
                    <input type="hidden" id="mrcOrderId" name="mrc_order_id" value="">
                    <input type="hidden" id="amountMoneyHidden" name="total_amount" value="">
                    <input type="hidden" id="description" name="description" value="">

                    <div class="mb-3">
                        <label for="displayAmount" class="form-label">
                            <i class="fas fa-money-bill-wave"></i> Số Tiền Nạp
                        </label>
                        <input type="text" class="form-control" id="displayAmount" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="customerEmail" class="form-label">
                            <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" id="customerEmail" name="customer_email"
                               value="{{ auth()->user()->email ?? '' }}"
                               placeholder="Nhập email của bạn">
                    </div>

                    <div class="mb-3">
                        <label for="customerPhone" class="form-label">
                            <i class="fas fa-phone"></i> Số Điện Thoại <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="customerPhone" name="customer_phone"
                               value="0912345678"
                               placeholder="Nhập số điện thoại" pattern="[0-9]{10,11}">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-submit btn-primary">
                            <i class="fas fa-credit-card"></i> Xác Nhận Thanh Toán
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function setAmount(amount) {
        document.getElementById('amountMoney').value = amount;
    }

    function showDepositModal() {
        const amount = parseInt(document.getElementById('amountMoney').value);

        if (!amount || amount < 5000) {
            alert('Vui lòng nhập số tiền tối thiểu 5,000 VNĐ!');
            return;
        }

        const modal = new bootstrap.Modal(document.getElementById('buyModal'));

        // Generate unique order ID
        const timestamp = Math.floor(Date.now() / 1000);
        const randomNum = Math.floor(Math.random() * 10000);
        const userId = '{{ auth()->id() ?? 1 }}';
        const mrcOrderId = `${userId}.${timestamp}.${randomNum}`;

        // Fill form
        document.getElementById('displayAmount').value = amount.toLocaleString('vi-VN') + ' VNĐ';
        document.getElementById('amountMoneyHidden').value = amount;
        document.getElementById('mrcOrderId').value = mrcOrderId;
        document.getElementById('description').value = `Nạp tiền ${amount.toLocaleString('vi-VN')} VNĐ`;

        modal.show();
    }

    // Form validation
    document.getElementById('buyForm').addEventListener('submit', function(e) {
        const email = document.getElementById('customerEmail').value;
        const phone = document.getElementById('customerPhone').value;

        if (!email || !phone) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin!');
            return false;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Email không hợp lệ!');
            return false;
        }

        // Validate phone format
        const phoneRegex = /^[0-9]{10,11}$/;
        if (!phoneRegex.test(phone)) {
            e.preventDefault();
            alert('Số điện thoại không hợp lệ (10-11 số)!');
            return false;
        }
    });
</script>
@endsection
