<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Work Order Report</title>
    <style>
        /* 210 × 297 mm */
        /* 8.27 × 11.69 inches */
        /* 595 × 842 points */
        /* 2480 x 3508 pixels */
        /* 1240 x 1754 pixels */
        body {
            font-family: sans-serif;
            font-size: 9pt;
            position: relative;
        }

        .page-break {
            page-break-after: always;
        }

        .header {
            text-align: center;
            margin-bottom: 10pt;
            font-size: 13pt;
            position: relative;
        }

        .page-header__content {
            max-width: 500pt;
            margin: auto;
        }

        .logo-image {
            height: 50pt;
            width: 85pt;
            position: absolute;
            left: 0;
            top: 0;
        }

        table {
            table-layout: fixed;
            width: 100%;
        }

        td {
            white-space: normal;
        }

        .border-table {
            border-collapse: collapse;
        }

        table.border-table, .border-table td, .border-table th {
            border: 1px solid black;
        }

        .border-table th {
            font-weight: bold;
            text-align: center;
        }

        .border-table td, th {
            padding: 5pt 10pt;
        }

        .border-table td > span {
            white-space: pre;
        }

        .border-table td > * {
            display: inline-block;
        }

        /* Report item */
        .header-2 {
            font-weight: bold;
            color: rgb(26, 26, 26);
        }

        .img-view {
            max-width: 160px;
            max-height: 120px;
            margin: 5px;
        }
    </style>
</head>
<body>
<script type="text/php">
    if ( isset($pdf) ) {
        $font = $fontMetrics->getFont("helvetica", "bold");
        $pdf->page_text(290, 760, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
    }

</script>

<div class="header">
    <img class="logo-image" src="img/pdf/logo.png"/>
    <div class="page-header__content" style="font-weight: bold;">
        DEPARTMENT OF PLANNING &amp; INVESTMENT<br>
        GOVERNMENT OF ARUNACHAL PRADESH<br>
        BLOCK NO. 1, CIVIL SECRETARIAT<br>
        ITANAGAR
    </div>
    <h2>GEOTAGGED REPORT</h2>
</div>

<h3>No.: {{ $funding_name."/".$department_name."/".$construction['id'] }}</h3>
<h3>Date of Geo-tagging: {{ $ORDER_DATE }}</h3>
<h3>Project Details</h3>

<table class="border-table">
    <tr>
        <td colspan="2">
            <b>Department Name:</b>
            <span>{{ array_key_exists('name', $construction['department']) ? trim(preg_replace('/\r\n|\r|\n/', ' ', $construction['department']['name'])) : '' }}</span>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div style="margin-top: 2px"><b>Project Name:</b></div>
            {{ trim(preg_replace('/\r\n|\r|\n/', ' ', $construction['display_name'])) }}
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div><b>Allocation Order No.:</b></div>
            {{ trim(preg_replace('/\r\n|\r|\n/', ' ', $display_name)) }}
        </td>
    </tr>
    <tr>
        <td>
            <b>Funding Agency:</b>
            <span>{{ isset($construction['funding_agency']) && array_key_exists('name', $construction['funding_agency']) ? trim(preg_replace('/\r\n|\r|\n/', ' ', $construction['funding_agency']['name'])) : '' }}</span>
        </td>
        <td>
            <b>Type:</b>
            <span>{{ array_key_exists('name', $construction['structure']) ?  trim(preg_replace('/\r\n|\r|\n/', ' ', $construction['structure']['name'])) : '' }}</span>
        </td>
    </tr>
</table>
<h3>Geo Tagging Information</h3>
<table class="border-table">
    <tr>
        <th>District Name</th>
        <th>Circle Name</th>
        <th colspan="2">Geo Coordinates</th>
    </tr>
    <tr>
        <td style="text-align: center">{{ array_key_exists('dist_name', $construction['area']) ?
            $construction['area']['dist_name'] : '' }}
        </td>
        <td style="text-align: center">{{ array_key_exists('name', $construction['area']) ?
            $construction['area']['name'] : '' }}
        </td>
        <td>
            <b>Latitude:</b>
            <span>{{ $construction['latitude'] }}</span>
        </td>
        <td>
            <b>Longitude:</b>
            <span>{{ $construction['longitude'] }}</span>
        </td>
    </tr>
</table>

<h3>Project Description</h3>
<table style="border: 1px solid black;">
    @foreach ($reports as $report)
        <tr style="border: 1px solid black;">
            <td>
                {{-- First Item --}}
                <table>
                    <tr>
                        <th colspan="4">
                            <div>VISIT - {{ $loop->index + 1 }} : {{$report['local_time']}}</div>
                        </th>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="header-2">Description</div>
                            {{ trim(str_replace(array("\n","\r"), ' ', $report['display_description']))}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @if (!empty($report['image1']))
                                <img class="img-view" src="{{ $report['image1'] }}" alt="Image 1">
                            @endif
                        </td>
                        <td>
                            @if (!empty($report['image2']))
                                <img class="img-view" src="{{ $report['image2'] }}" alt="Image 2">
                            @endif
                        </td>
                        <td>
                            @if (!empty($report['image3']))
                                <img class="img-view" src="{{ $report['image3'] }}" alt="Image 3">
                            @endif
                        </td>
                        <td>
                            @if (!empty($report['image4']))
                                <img class="img-view" src="{{ $report['image4'] }}" alt="Image 4">
                            @endif
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    @endforeach
</table>
<h3>Field Executive Details</h3>
<table class="border-table">
    <tr>
        <td>
            <b>Name: </b>
            @if (!empty($construction['user']))
                <span>{{ $construction['user']['name'] }}</span>
            @endif
        </td>
        <td>
            <b>Mobile No.: </b>
            @if (!empty($construction['user']))
                <span>{{ $construction['user']['mobile'] }}</span>
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>Email ID: </b>
            @if (!empty($construction['user']) && !empty($construction['user']['email']))
                <span>{{ $construction['user']['email'] }}</span>
            @endif
        </td>
    </tr>
</table>

<div style="margin-top: 10pt; font-style: italic;">*** This Report is electronically generated and no signature is
    required.
</div>
<div style="margin-top: 30pt;  font-size: smaller">{{ "Report Generation Date: $TIME_NOW"}}</div>
</body>
</html>
