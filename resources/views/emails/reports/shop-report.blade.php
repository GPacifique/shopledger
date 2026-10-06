<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $delivery['title'] }}
    </title>

</head>


<body
    style="
        margin: 0;
        padding: 0;
        background: #f8fafc;
        font-family: Arial, Helvetica, sans-serif;
        color: #1e293b;
    "
>

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="background:#f8fafc; padding:30px 15px;"
>

    <tr>

        <td align="center">

            <table
                width="100%"
                cellpadding="0"
                cellspacing="0"
                border="0"
                style="
                    max-width:680px;
                    background:#ffffff;
                    border:1px solid #e2e8f0;
                    border-radius:12px;
                    overflow:hidden;
                "
            >

                {{-- Header --}}
                <tr>

                    <td
                        style="
                            padding:28px;
                            background:#166534;
                            color:#ffffff;
                        "
                    >

                        <div
                            style="
                                font-size:24px;
                                font-weight:bold;
                                margin-bottom:8px;
                            "
                        >
                            MahWi
                        </div>

                        <div
                            style="
                                font-size:18px;
                                font-weight:bold;
                            "
                        >
                            {{ $delivery['title'] }}
                        </div>

                        <div
                            style="
                                margin-top:8px;
                                font-size:14px;
                                color:#dcfce7;
                            "
                        >
                            {{ $delivery['period']['label'] }}
                        </div>

                    </td>

                </tr>


                {{-- Introduction --}}
                <tr>

                    <td style="padding:28px 28px 10px 28px;">

                        <p
                            style="
                                margin:0 0 8px 0;
                                font-size:16px;
                            "
                        >
                            Hello,
                        </p>

                        <p
                            style="
                                margin:0;
                                color:#64748b;
                                line-height:1.6;
                            "
                        >
                            Here is the
                            <strong>
                                {{ strtolower($delivery['title']) }}
                            </strong>
                            for
                            <strong>
                                {{ $delivery['shop']->name }}
                            </strong>.
                        </p>

                    </td>

                </tr>


                @php
                    $summary = $delivery['report']['summary'] ?? [];

                    $sales = (float) ($summary['sales'] ?? 0);
                    $purchases = (float) ($summary['purchases'] ?? 0);
                    $expenses = (float) ($summary['expenses'] ?? 0);
                    $otherIncome = (float) ($summary['other_income'] ?? 0);
                    $grossProfit = (float) ($summary['gross_profit'] ?? 0);
                    $netProfit = (float) ($summary['net_profit'] ?? 0);

                    $orders = (int) ($summary['orders'] ?? 0);
                    $productsSold = (float) ($summary['products_sold'] ?? 0);
                @endphp


                {{-- Summary --}}
                <tr>

                    <td style="padding:20px 28px;">

                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                        >

                            <tr>

                                <td
                                    width="50%"
                                    style="
                                        padding:12px;
                                        border:1px solid #e2e8f0;
                                        background:#f8fafc;
                                    "
                                >

                                    <div
                                        style="
                                            font-size:12px;
                                            color:#64748b;
                                        "
                                    >
                                        SALES
                                    </div>

                                    <div
                                        style="
                                            margin-top:5px;
                                            font-size:20px;
                                            font-weight:bold;
                                        "
                                    >
                                        {{ number_format($sales) }}
                                        RWF
                                    </div>

                                </td>


                                <td width="10"></td>


                                <td
                                    width="50%"
                                    style="
                                        padding:12px;
                                        border:1px solid #e2e8f0;
                                        background:#f8fafc;
                                    "
                                >

                                    <div
                                        style="
                                            font-size:12px;
                                            color:#64748b;
                                        "
                                    >
                                        ORDERS
                                    </div>

                                    <div
                                        style="
                                            margin-top:5px;
                                            font-size:20px;
                                            font-weight:bold;
                                        "
                                    >
                                        {{ number_format($orders) }}
                                    </div>

                                </td>

                            </tr>


                            <tr>
                                <td colspan="3" style="height:10px;"></td>
                            </tr>


                            <tr>

                                <td
                                    width="50%"
                                    style="
                                        padding:12px;
                                        border:1px solid #e2e8f0;
                                        background:#f8fafc;
                                    "
                                >

                                    <div
                                        style="
                                            font-size:12px;
                                            color:#64748b;
                                        "
                                    >
                                        GROSS PROFIT
                                    </div>

                                    <div
                                        style="
                                            margin-top:5px;
                                            font-size:20px;
                                            font-weight:bold;
                                        "
                                    >
                                        {{ number_format($grossProfit) }}
                                        RWF
                                    </div>

                                </td>


                                <td width="10"></td>


                                <td
                                    width="50%"
                                    style="
                                        padding:12px;
                                        border:1px solid #e2e8f0;
                                        background:#f8fafc;
                                    "
                                >

                                    <div
                                        style="
                                            font-size:12px;
                                            color:#64748b;
                                        "
                                    >
                                        NET RESULT
                                    </div>

                                    <div
                                        style="
                                            margin-top:5px;
                                            font-size:20px;
                                            font-weight:bold;
                                        "
                                    >
                                        {{ number_format($netProfit) }}
                                        RWF
                                    </div>

                                </td>

                            </tr>

                        </table>

                    </td>

                </tr>


                {{-- Financial Details --}}
                <tr>

                    <td style="padding:0 28px 20px 28px;">

                        <h3
                            style="
                                margin:0 0 12px 0;
                                font-size:16px;
                            "
                        >
                            Financial Summary
                        </h3>


                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                        >

                            <tr>

                                <td
                                    style="
                                        padding:10px 0;
                                        border-bottom:1px solid #e2e8f0;
                                    "
                                >
                                    Sales
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:10px 0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-weight:bold;
                                    "
                                >
                                    {{ number_format($sales) }} RWF
                                </td>

                            </tr>


                            <tr>

                                <td
                                    style="
                                        padding:10px 0;
                                        border-bottom:1px solid #e2e8f0;
                                    "
                                >
                                    Purchases
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:10px 0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-weight:bold;
                                    "
                                >
                                    {{ number_format($purchases) }} RWF
                                </td>

                            </tr>


                            <tr>

                                <td
                                    style="
                                        padding:10px 0;
                                        border-bottom:1px solid #e2e8f0;
                                    "
                                >
                                    Expenses
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:10px 0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-weight:bold;
                                    "
                                >
                                    {{ number_format($expenses) }} RWF
                                </td>

                            </tr>


                            <tr>

                                <td
                                    style="
                                        padding:10px 0;
                                        border-bottom:1px solid #e2e8f0;
                                    "
                                >
                                    Other Income
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:10px 0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-weight:bold;
                                    "
                                >
                                    {{ number_format($otherIncome) }} RWF
                                </td>

                            </tr>


                            <tr>

                                <td
                                    style="
                                        padding:10px 0;
                                        border-bottom:1px solid #e2e8f0;
                                    "
                                >
                                    Products Sold
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:10px 0;
                                        border-bottom:1px solid #e2e8f0;
                                        font-weight:bold;
                                    "
                                >
                                    {{ number_format($productsSold, 2) }}
                                </td>

                            </tr>


                            <tr>

                                <td
                                    style="
                                        padding:12px 0;
                                        font-weight:bold;
                                    "
                                >
                                    Net Result
                                </td>

                                <td
                                    align="right"
                                    style="
                                        padding:12px 0;
                                        font-weight:bold;
                                    "
                                >
                                    {{ number_format($netProfit) }} RWF
                                </td>

                            </tr>

                        </table>

                    </td>

                </tr>


                {{-- Daily Breakdown --}}
                @if(
                    isset($delivery['report']['daily_breakdown'])
                    && $delivery['report']['daily_breakdown']->isNotEmpty()
                )

                    <tr>

                        <td style="padding:0 28px 25px 28px;">

                            <h3
                                style="
                                    margin:0 0 12px 0;
                                    font-size:16px;
                                "
                            >
                                Daily Breakdown
                            </h3>


                            <table
                                width="100%"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                style="font-size:13px;"
                            >

                                <tr
                                    style="
                                        background:#f8fafc;
                                        font-weight:bold;
                                    "
                                >

                                    <td style="padding:9px;">
                                        Date
                                    </td>

                                    <td
                                        align="right"
                                        style="padding:9px;"
                                    >
                                        Sales
                                    </td>

                                    <td
                                        align="right"
                                        style="padding:9px;"
                                    >
                                        Orders
                                    </td>

                                    <td
                                        align="right"
                                        style="padding:9px;"
                                    >
                                        Net
                                    </td>

                                </tr>


                                @foreach(
                                    $delivery['report']['daily_breakdown']
                                    as $day
                                )

                                    <tr>

                                        <td
                                            style="
                                                padding:9px;
                                                border-bottom:1px solid #e2e8f0;
                                            "
                                        >
                                            {{ $day['date']->format('d/m/Y') }}
                                        </td>

                                        <td
                                            align="right"
                                            style="
                                                padding:9px;
                                                border-bottom:1px solid #e2e8f0;
                                            "
                                        >
                                            {{ number_format($day['sales']) }}
                                        </td>

                                        <td
                                            align="right"
                                            style="
                                                padding:9px;
                                                border-bottom:1px solid #e2e8f0;
                                            "
                                        >
                                            {{ number_format($day['orders']) }}
                                        </td>

                                        <td
                                            align="right"
                                            style="
                                                padding:9px;
                                                border-bottom:1px solid #e2e8f0;
                                            "
                                        >
                                            {{ number_format($day['net_profit']) }}
                                        </td>

                                    </tr>

                                @endforeach

                            </table>

                        </td>

                    </tr>

                @endif


                {{-- Footer --}}
                <tr>

                    <td
                        style="
                            padding:25px 28px;
                            background:#f8fafc;
                            border-top:1px solid #e2e8f0;
                        "
                    >

                        <p
                            style="
                                margin:0 0 8px 0;
                                font-size:13px;
                                color:#64748b;
                            "
                        >
                            This report was generated by
                            <strong>MahWi</strong>.
                        </p>

                        <p
                            style="
                                margin:0;
                                font-size:12px;
                                color:#94a3b8;
                            "
                        >
                            Please review the figures in your MahWi
                            dashboard for complete transaction details.
                        </p>

                    </td>

                </tr>

            </table>

        </td>

    </tr>

</table>

</body>

</html>
