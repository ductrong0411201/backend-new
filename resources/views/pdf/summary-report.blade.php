<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Project Report MIS</title>
  <style>
    /* 210 × 297 mm */
    /* 8.27 × 11.69 inches */
    /* 595 × 842 points */
    /* 2480 x 3508 pixels */
    /* 1240 x 1754 pixels */
    body {
      font-family: sans-serif;
      font-size: 9pt;
    }


    .page-header {
      text-align: center;
      margin-bottom: 10pt;
      font-size: 13pt;
      position: relative;
    }

    .page-header__content {
      max-width: 500pt;
      margin: auto;
    }

    .title-text {
      font-size: 16pt;
      margin-top: 40pt;
      font-weight: bold;
    }

    .logo-image {
      height: 50pt;
      width: 85pt;
      position: absolute;
      left: 0pt;
      top: 0pt;
    }

    table {
      table-layout: fixed;
      width: 100%;
    }

    .border-table {
      border-collapse: collapse;
    }

    table.border-table,
    .border-table td,
    .border-table th {
      border: 1px solid black;
    }

    .border-table th {
      font-weight: bold;
      text-align: center;
    }

    .border-table td,
    th {
      padding: 5pt 10pt;
    }

    .border-table td > span {
      white-space: pre;
    }

    .border-table td > * {
      display: inline-block;
    }

    .images-container img {
      width: 100%;
      height: 100pt;
    }

    .images-container img:not(:last-child) {
      margin-right: 5pt;
    }
  </style>
</head>

<body style="padding: 10px 10px">
<script type="text/php">
        if ( isset($pdf) ) {
        $font = $fontMetrics->getFont("helvetica", "bold");
        $pdf->page_text(290, 760, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
    }





</script>
<div class="page-header">
  <img class="logo-image" src="img/pdf/logo.png"/>
  <div class="page-header__content" style="font-weight: bold;">
    DEPARTMENT OF PLANNING &amp; INVESTMENT<br>
    GOVERNMENT OF ARUNACHAL PRADESH<br>
    BLOCK NO. 1, CIVIL SECRETARIAT<br>
    ITANAGAR
  </div>
  <div class="page-header__content"
       style="color: white; background-color: #1a237e; padding: 10px; margin-top: 30px; margin-bottom: 25px">
    MIS Report
  </div>
</div>
<div style="margin-top: 10px; margin-left: 12px">
  Unique Project ID: <b> {{ $projectid }}</b>
</div>
<div style="margin-top: 10px">
  1. Name of the Project:<b> {{ $name }}</b>
</div>
<div style="margin-top: 10px">
  2. Allocation Order No:<b> {{ $order['display_name'] }}</b>
</div>
<div style="margin-top: 10px">
  3. Department: <b> {{ isset($department) ? $department['name'] : '' }}</b>
</div>
<div style="margin-top: 10px">
  4. Estimated Cost of the Project: <b> {{$estimated_cost}}</b>
  <div style="margin-top: 10px; margin-left: 12px">
    Share Ratio (Central/State): <b>{{ $central_share }} - {{ $state_share }}</b>
  </div>
  <div style="margin-top: 10px; margin-left: 12px">
    Share Amount (Central/State): <b>{{ $central_share * $estimated_cost / 100 .' Lakhs' }} - {{ $state_share *
      $estimated_cost / 100 .' Lakhs'}}</b>
  </div>
</div>

<div style="margin-top: 10px">
  5. Source of Funding
  <div style="margin-left: 30pt; margin-top: 10px">a) Central:
    @foreach($central_funding_agencies as $funding)
      <b> {{ $funding['name']. '; ' }}</b>
    @endforeach
  </div>
  <div style="margin-left: 30pt; margin-top: 10px">a) State:
    @foreach($state_funding_agencies as $funding)
      <b> {{ $funding['name'] . '; ' }}</b>
    @endforeach
  </div>
  <div style="margin-top: 10px">
    6. District:
    <b> {{isset($area) ? $area['dist_name']: ''}}</b>
  </div>

  <div style="margin-top: 10px">
    7. Circle:
    <b> {{isset($area) ? $area['name'] : '' }}</b>
  </div>

  <div style="margin-top: 10px">
    8. CD Block:
    <b> {{$block}}</b>
  </div>

  <div style="margin-top: 10px">
    9. GEO - Coordinates:<br>
    <div style="margin-top: 10px; margin-left: 12px">
      <b>[DD]</b> Latitude: <b> {{$latitude}}</b> , Longitude: <b>{{$longitude}}</b><br>
    </div>
    <div style="margin-top: 10px; margin-left: 12px">
      <b>[DMS]</b> Latitude: <b> {{$lat_dms}}</b> , Longitude: <b>{{$lng_dms}}</b>
    </div>
  </div>

  <div style="margin-top: 10px; page-break-before:always;">
    10. Financial Progress Summary
  </div>


  <div style="margin-left: 30pt; margin-top: 10px; ">a) Central Share:</div>
  <table class="border-table" style="margin-top: 10px">
    <tr>
      <td colspan="2">
        <b>Sl no</b>
      </td>
      <td colspan="3">
        <b>Instalment</b>
      </td>
      <td colspan="3">
        <b>Amount (in Lakhs)</b>
      </td>
      <td colspan="3">
        <b>Date of Release</b>
      </td>
      <td colspan="3">
        <b>Utilization Certificate Status</b>
      </td>
      <td colspan="3">
        <b>Date of submission</b>
      </td>
    </tr>
    {{$j=0}}
    @foreach($financial_progresses as $financialProgress)
      @if($financialProgress['shared_by'] === 'central_share')
        {{$j=$j+1}}
        <tr>
          <td colspan="2" width="50px" rowspan="2"
              style="text-align: center;width: 30px !important;">{{$j}}
          </td>
          <td colspan="3">
            <span>{{$financialProgress['instalment']}}</span>
          </td>
          <td colspan="3">
            <span>{{ isset($financialProgress['amount']) ? $financialProgress['amount'] : ''}}</span>
          </td>
          <td colspan="3">
            <span>{{ isset($financialProgress['release_date']) ? Carbon\Carbon::parse($financialProgress['release_date'])->format('Y-m-d'): ''}}</span>
          </td>
          <td colspan="3">
            <span>{{ $financialProgress['uc_status']}}</span>
          </td>
          <td colspan="3">
            <span>{{ isset($financialProgress['uc_date']) ? Carbon\Carbon::parse($financialProgress['uc_date'])->format('Y-m-d') : ''}}</span>
          </td>
        </tr>
        <tr>
          <td colspan="21">
            <div style="clear: both"></div>
            <div style="display: block !important; margin-top: 5px">
              Date of Sanction: <span
              >{{ isset($financialProgress['sanction_date']) ?Carbon\Carbon::parse($financialProgress['sanction_date'])->format('Y-m-d') : ''}}</span>
            </div>
            <div style="display: block !important; margin-top: 5px">Handed over of Completed Project by the
              Executing
              Department: <span style="font-weight: bold">{{$financialProgress['handed_over']}}</span>
            </div>
            <div style="display: block !important; margin-top: 5px">
              Date of Handed over: <span
                      style="font-weight: bold">{{ isset($financialProgress['handed_over_date']) ? Carbon\Carbon::parse($financialProgress['handed_over_date'])->format('Y-m-d') : ''}}</span>
            </div>
            <div style="display: block !important; margin-top: 5px">
              Taken over of completed project by the Beneficiary Department: <span
                      style="font-weight: bold">{{$financialProgress['taken_over']}}</span>
            </div>
            <div style="display: block !important; margin-top: 5px">
              Date of Taken over: <span
                      style="font-weight: bold">{{ isset($financialProgress['taken_over_date']) ?Carbon\Carbon::parse($financialProgress['taken_over_date'])->format('Y-m-d') : ''}}</span>
            </div>
          </td>
        </tr>
      @endif
    @endforeach
  </table>
  <div style="margin-left: 30pt; margin-top: 10px;">b) State Share:</div>
  <table class="border-table" style="margin-top: 10px">
    <tr>
      <td colspan="2">
        <b>Sl no</b>
      </td>
      <td colspan="3">
        <b>Instalment</b>
      </td>
      <td colspan="3">
        <b>Amount (in Lakhs)</b>
      </td>
      <td colspan="3">
        <b>Date of Release</b>
      </td>
      <td colspan="3">
        <b>Utilization Certificate Status</b>
      </td>
      <td colspan="3">
        <b>Date of submission</b>
      </td>
    </tr>
    {{$j=0}}
    @foreach($financial_progresses as $financialProgress)
      @if($financialProgress['shared_by'] === 'state_share')
        {{$j=$j+1}}
        <tr>
          <td colspan="2" width="50px" rowspan="2"
              style="text-align: center;width: 30px !important;">{{$j}}
          </td>
          <td colspan="3">
            <span>{{$financialProgress['instalment']}}</span>
          </td>
          <td colspan="3">
            <span>{{isset($financialProgress['amount']) ? $financialProgress['amount'] : ''}}</span>
          </td>
          <td colspan="3">
            <span>{{isset($financialProgress['release_date']) ? Carbon\Carbon::parse($financialProgress['release_date'])->format('Y-m-d'): ''}}</span>
          </td>
          <td colspan="3">
            <span>{{$financialProgress['uc_status']}}</span>
          </td>
          <td colspan="3">
            <span>{{isset($financialProgress['uc_date']) ? Carbon\Carbon::parse($financialProgress['uc_date'])->format('Y-m-d') : ''}}</span>
          </td>
        </tr>
        <tr>
          <td colspan="21">
            <div style="clear: both"></div>
            <div style="display: block !important; margin-top: 5px">
              Date of Sanction: <span
              >{{ isset($financialProgress['sanction_date']) ?Carbon\Carbon::parse($financialProgress['sanction_date'])->format('Y-m-d') : ''}}</span>
            </div>
            <div style="display: block !important; margin-top: 5px">Handed over of Completed Project by the
              Executing
              Department: <span style="font-weight: bold">{{$financialProgress['handed_over']}}</span>
            </div>
            <div style="display: block !important; margin-top: 5px">
              Date of Handed over: <span
                      style="font-weight: bold">{{ isset($financialProgress['handed_over_date']) ? Carbon\Carbon::parse($financialProgress['handed_over_date'])->format('Y-m-d') : ''}}</span>
            </div>
            <div style="display: block !important; margin-top: 5px">
              Taken over of completed project by the Beneficiary Department: <span
                      style="font-weight: bold">{{$financialProgress['taken_over']}}</span>
            </div>
            <div style="display: block !important; margin-top: 5px">
              Date of Taken over: <span
                      style="font-weight: bold">{{ isset($financialProgress['taken_over_date']) ?Carbon\Carbon::parse($financialProgress['taken_over_date'])->format('Y-m-d') : ''}}</span>
            </div>
          </td>
        </tr>
      @endif
    @endforeach

  </table>
  <div style="margin-top: 10px; page-break-before:always;">
    11. Physical Progress Summary:
  </div>
  <div class="table-responsive">
    <table class="border-table" style="margin-top: 10px;">
      <tr>
        <td colspan="2">
          <b>Sl no</b>
        </td>
        <td colspan="4">
          <b>Year</b>
        </td>
        <td colspan="4">
          <b>Period/ Quarters</b>
        </td>
        <td colspan="4">
          <b>Status</b>
        </td>
        <td colspan="7">
          <b>Physical Progress</b>
        </td>
        <td colspan="7">
          <b>Financial Progress</b>
        </td>
      </tr>
      {{$z=0}}
      @foreach($physical_progresses as $ph)
        {{$z=$z+1}}
        <tr>
          <td colspan="2" rowspan="2" style="width: 30px !important;" width="30px">
            <span>{{$z}}</span>
          </td>
          <td colspan="4" rowspan="2" style="width: 30px !important;" width="30px">
            <span>{{$ph['year']}}</span>
          </td>
          <td colspan="4" rowspan="2" style="width: 30px !important;" width="30px">
            <div>{{$ph['quarter']}}<br>{{$ph['quarter_sub']}}</div>
          </td>
          <td colspan="4" rowspan="2" style="width: 30px !important;" width="30px">
            <span>{{$ph['physical_progress']['status']}}</span>
          </td>
          <td colspan="7">
            <span>{{$ph['physical_progress']['physical_percent']}}</span>
          </td>
          <td colspan="7">
            <span>{{$ph['physical_progress']['financial_percent']}}</span>
          </td>

        </tr>
        <tr>
          @if (!empty($ph['photos']))
            <td colspan="14" style="height: 330px; max-height: 680px;">
              <!--                        <h5 style="display: block !important;">Photographs:</h5>-->
              @foreach($ph['photos'] as $id => $photo)
                <img src="{{$photo}}" alt="" width="125px"
                     style="margin-top: 5px; padding: 6px; height: 150px; float: left;"/>
                @if ($id%2 == 1)
                  <div style="clear: both"></div>
                @endif
              @endforeach

            </td>
          @else
            <td colspan="14">
            </td>
          @endif
        </tr>
      @endforeach
    </table>
  </div>
  <div style="margin-top: 10px">
    12. Project gist:
  </div>
  @if (strlen($project_gist) > 850)
    <div style="border: 1px solid black; word-wrap: break-word; padding:10px; margin-top: 10px; text-align: justify;">
      {{$project_gist}}
    </div>
  @else
    <div style="border: 1px solid black; height: 90px; word-wrap: break-word; padding:10px; margin-top: 10px; text-align: justify;">
      {{$project_gist}}
    </div>
  @endif
  <div style="margin-top: 10px">
    13. Any Other Remarks:
  </div>
  @if (strlen($remarks) > 850)
    <div style="border: 1px solid black; word-wrap: break-word; padding:10px; margin-top: 10px; text-align: justify;">
      {{$remarks}}
    </div>
  @else
    <div style="border: 1px solid black; height: 90px; word-wrap: break-word; padding:10px; margin-top: 10px; text-align: justify;">
      {{$remarks}}
    </div>
  @endif
  <h3>HOD Details</h3>
  <table class="border-table">
    <tr>
      <td>
        <b>Name: </b>
        <span>{{ $user['name'] }}</span>
      </td>
      <td>
        <b>Mobile No.: </b>
        <span>{{ $user['mobile'] }}</span>
      </td>
    </tr>
    <tr>
      <td>
        <b>Email ID: </b>
        <span>{{ $user['email'] }}</span>
      </td>
      <td>
        @if (isset($user['department']))
          <b>Department: </b>
          <span>{{ $user['department']['name'] }}</span>
        @elseif (isset($user['districts']))
          <b>District: </b>
          <span>{{ $user['district']}}</span>
        @endif

      </td>
    </tr>

    <div style="margin-top: 10pt; font-style: italic;">*** This Report is electronically generated and no signature is
      required.
    </div>
    <div style="margin-top: 30pt;  font-size: smaller">{{ "Report Generation Date: $TIME_NOW"}}</div>
</body>

</html>
