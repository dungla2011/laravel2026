<?php
$uid = getCurrentUserId();
$siteId = \App\Models\SiteMng::getSiteId();
setLogFile("/var/glx/weblog/baokim_$siteId.log");
$params = request()->all();
$domain = \LadLib\Common\UrlHelper1::getDomainHostName();

?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')
    {{
    \App\Models\SiteMng::getTitle()
    }}
@endsection

@section('meta-description')
    <?php
    \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section('content')

    <style>
        .pricing-container {
            background: white;
            padding: 30px 10px;
            border-radius: 12px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15) !important;
        }

        .free_count{
            margin: 0px 2px;
            color: white;
            background-color: #ccc;
            border-radius: 5px;
            padding: 1px 8px    ;
        }

        .vip_count{
            margin: 0px 2px;
            border-radius: 5px;
            color: white;
            background-color: orange;
            padding: 1px 8px    ;
        }

        .pricing-header {
            text-align: center;

        }

        .pricing-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .pricing-header p {
            color: #718096;
            font-size: 1rem;
        }

        .current-limit {
            background: #f7fafc;
            color: #4a5568;
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            border: 1px solid #e2e8f0;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            /*max-width: 1200px;*/
            margin: 0 auto;
        }

        .pricing-card {
            background: white;
            border: 1px solid #eee!important;
            border-radius: 12px;
            padding: 20px 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .pricing-card:hover {
            border-color: #4a5568;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .pricing-card.selected {
            border-color: #2d3748;
            background: #f7fafc;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .pricing-card.free-card {
            border-color: #cbd5e0;
            background: #f7fafc;
        }

        .pricing-card.free-card:hover {
            border-color: #a0aec0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .pricing-card.popular {
            border-color:orangered;
            position: relative;
            background: #f7fafc;
        }

        .popular-badge {
            position: absolute;
            top: -12px;
            right: 15px;
            background: orange;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .package-icon {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 12px;
        }

        .package-name {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
            text-align: center;
        }

        .package-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
            text-align: center;
        }

        .pricing-card.free-card .package-price {
            color: #4a5568;
        }

        .package-period {
            color: #a0aec0;
            font-size: 0.8rem;
            margin-bottom: 15px;
            text-align: center;
        }

        .package-features {
            list-style: none;
            padding: 0;
            margin: 15px 0 0 0;
        }

        .package-features li {
            padding: 3px 0;
            color: #4a5568;
            font-size: 0.8rem;
            display: flex;
            /*align-items: center;*/
        }

        .package-features li::before {
            content: '✓';
            color: #2d3748;
            font-weight: bold;
            margin-right: 8px;
        }

        .btn-register {
            width: 100%;
            padding: 12px 20px;
            margin: 5px 0;     /* Margin trên dưới */
            background: orange;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-register:hover {
            /*background: #1a202c;*/
            transform: translateY(-2px);
        }

        .pricing-card.free-card .btn-register {
            background: #718096;
        }

        .pricing-card.free-card .btn-register:hover {
            background: #4a5568;
        }

        .pricing-card.popular .btn-register {
            background: royalblue;

        }

        .pricing-card.popular .btn-register:hover {
            background: royalblue;
        }

        .select_product input {
            display: none;
        }

        @media (max-width: 768px) {
            .pricing-grid {
                grid-template-columns: 1fr;
            }

            .pricing-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>

    <div class="container mt-5">

        <div class="pricing-container1">

            <?php
            if(request('post') == 'vps')
            {
                ?>
                @include("orderitem.glxv3.post-vps")
                <?php
            }
            else
            if(request('cat') == 'vps')
            {
            ?>

                @include("orderitem.glxv3.vps")

            <?php
            }
            ?>

        </div>
    </div>


    <script>
        // Handle Free package click
        function handleFreePackage(event) {
            event.preventDefault();
            event.stopPropagation();
            alert('{{ __('monitor.free_package_message') }}');
            return false;
        }

        // Handle package selection and submit
        function selectPackage(productId) {
            // Select the radio button
            $('#input_prod_' + productId).prop('checked', true);

            // Highlight the card
            $(".pricing-card:not(.free-card)").removeClass("selected");
            $("[data-card-id='" + productId + "']").addClass("selected");

            // Submit the form
            $('#form-action').submit();
        }

        window.addEventListener('load', function () {
            // Click vào card để highlight (không submit)
            $(".pricing-card:not(.free-card)").on("click", function (e) {
                // Nếu click vào button thì không xử lý ở đây
                if ($(e.target).hasClass('btn-register')) {
                    return;
                }

                // Remove selected class from all paid cards
                $(".pricing-card:not(.free-card)").removeClass("selected");

                // Add selected class to clicked card
                $(this).addClass("selected");

                // Check the radio button inside this card
                $(this).find('input[type="radio"]').prop('checked', true);
            });

            // Auto-select first paid card on load
            $(".pricing-card:not(.free-card)").first().addClass("selected");
            $(".pricing-card:not(.free-card)").first().find('input[type="radio"]').prop('checked', true);
        });
    </script>

@endsection
