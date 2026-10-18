
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Approval | Mahwi POS</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: Arial, Helvetica, sans-serif;
            color: #17211d;
            background: #f4f7f5;
        }

        .approval-card {
            width: 100%;
            max-width: 510px;
            padding: 48px 36px;
            text-align: center;
            background: #ffffff;
            border: 1px solid #e5ebe7;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(20, 50, 35, 0.08);
        }

        .brand {
            margin-bottom: 38px;
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .brand span {
            color: #15945b;
        }

        /* Circular loading indicator */
        .loader {
            width: 112px;
            height: 112px;
            margin: 0 auto 30px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: conic-gradient(
                from 0deg,
                #15945b 0deg,
                #15945b 240deg,
                #e5eee8 240deg,
                #e5eee8 360deg
            );
            animation: rotate 1.6s linear infinite;
        }

        .loader::before {
            content: "";
            position: absolute;
            inset: 7px;
            border-radius: 50%;
            background: #ffffff;
        }

        .loader-center {
            position: relative;
            z-index: 1;
            width: 74px;
            height: 74px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #e9f7ef;
            color: #15945b;
            font-size: 30px;
            animation: pulse 1.6s ease-in-out infinite;
        }

        @keyframes rotate {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(0.91);
            }
        }

        .eyebrow {
            margin-bottom: 10px;
            color: #15945b;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        h1 {
            margin: 0 0 14px;
            font-size: clamp(24px, 5vw, 31px);
            letter-spacing: -1px;
        }

        .description {
            margin: 0 auto 28px;
            max-width: 370px;
            color: #69756e;
            font-size: 14px;
            line-height: 1.8;
        }

        .shop-details {
            padding: 18px;
            margin-bottom: 24px;
            text-align: left;
            background: #f8faf9;
            border: 1px solid #edf1ee;
            border-radius: 14px;
        }

        .detail-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .detail-label {
            color: #7a857e;
            font-size: 13px;
        }

        .detail-value {
            font-size: 13px;
            font-weight: 700;
            text-align: right;
            overflow-wrap: anywhere;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 11px;
            color: #946200;
            background: #fff2ce;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            background: #e4a313;
            border-radius: 50%;
            animation: blink 1.4s ease-in-out infinite;
        }

        @keyframes blink {
            50% {
                opacity: 0.35;
            }
        }

        .rejection {
            margin: 0 0 20px;
            padding: 14px;
            border-radius: 12px;
            background: #fff1f0;
            color: #b42318;
            font-size: 13px;
            line-height: 1.6;
            text-align: left;
        }

        .hint {
            margin: 0 0 25px;
            color: #7a857e;
            font-size: 12px;
            line-height: 1.7;
        }

        .signout {
            display: inline-block;
            padding: 12px 24px;
            border: 1px solid #dce5df;
            border-radius: 10px;
            background: #ffffff;
            color: #26382d;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
        }

        .signout:hover {
            background: #f2f7f4;
            border-color: #15945b;
        }

        .footer {
            margin-top: 30px;
            color: #a0aaa3;
            font-size: 11px;
        }

        @media (max-width: 480px) {
            .approval-card {
                padding: 36px 22px;
            }

            .loader {
                width: 96px;
                height: 96px;
            }

            .loader-center {
                width: 62px;
                height: 62px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
            }
        }
    </style>
</head>

<body>
    <main class="approval-card">
        <div class="brand">mahwi<span>POS</span></div>

        <div class="loader" role="status"
             aria-label="Waiting for shop approval">
            <div class="loader-center">&#9203;</div>
        </div>

        <div class="eyebrow">Account verification</div>

        <h1>Shop Awaiting Approval</h1>

        <p class="description">
            Your shop is waiting for review by the Mahwi System Administrator.
            Once approved, you will be able to access your business dashboard.
        </p>

        @if(isset($shop) && $shop)
            <section class="shop-details">
                <div class="detail-row" style="margin-bottom: 16px;">
                    <span class="detail-label">Shop name</span>
                    <span class="detail-value">
                        {{ $shop->business_name ?? $shop->name ?? 'Your shop' }}
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Approval status</span>
                    <span class="status">
                        <span class="status-dot"></span>
                        {{ ucfirst($shop->status ?? 'pending') }}
                    </span>
                </div>
            </section>

            @if(!empty($shop->rejection_reason))
                <div class="rejection">
                    <strong>Administrator's feedback</strong><br>
                    {{ $shop->rejection_reason }}
                </div>
            @endif
        @endif

        <p class="hint">
            Please keep your account active and check back after the
            administrator has reviewed your shop.
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="signout">Sign Out</button>
        </form>

        <div class="footer">
            &copy; {{ date('Y') }} Mahwi POS. Your business backbone.
        </div>
    </main>
</body>
</html>
