<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Stall;
use App\Models\Parking;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Charts\AdminChart;
use App\Models\Tenant;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->bgcolor = collect([
            '#7158e2',
            '#3ae374',
            '#ff3838',
            "#FF851B",
            "#7FDBFF",
            "#B10DC9",
            "#FFDC00",
            "#001f3f",
            "#39CCCC",
            "#01FF70",
            "#85144b",
            "#F012BE",
            "#3D9970",
            "#111111",
            "#AAAAAA",
        ]);
    }

    public function index()
    {
        $userCount = User::count();
        $tenantCount = Tenant::count();

        $tenantByAddress = Tenant::whereNotNull('address')
            ->groupBy('address')
            ->orderBy('total')
            ->pluck(DB::raw('count(address) as total'), 'address')
            ->all();

        $tenantByAddressChart = new AdminChart();
        $tenantByAddressChart->labels(array_keys($tenantByAddress))
            ->dataset('Tenant Demographics by Address', 'bar', array_values($tenantByAddress))
            ->backgroundColor(['#7158e2', '#3ae374', '#ff3838','#111111', '#7FDBFF','#7158e2',"#39CCCC",'#7158e2']);

        $stallStatuses = Stall::groupBy('status')
            ->orderBy('total')
            ->pluck(DB::raw('count(status) as total'), 'status')
            ->all();

        $stallStatusesChart = new AdminChart();
        $stallStatusesChart->labels(array_keys($stallStatuses))
            ->dataset('Stall Statuses', 'pie', array_values($stallStatuses))
            ->backgroundColor(['#ff5733', '#ffc300', '#36a2eb']);

        $parkingChargesByDay = Parking::select(DB::raw('DATE(parking_start) as day'), DB::raw('SUM(charge) as total_charge'))
            ->groupBy(DB::raw('DATE(parking_start)'))
            ->orderBy(DB::raw('DATE(parking_start)'))
            ->get();

        $parkingLabels = [];
        $parkingCharges = [];

        foreach ($parkingChargesByDay as $parkingCharge) {
            $parkingLabels[] = $parkingCharge->day;
            $parkingCharges[] = $parkingCharge->total_charge;
        }

        $parkingChargesChart = new AdminChart();
        $parkingChargesChart->labels($parkingLabels)
            ->dataset('Parking Charges by Day', 'line', $parkingCharges)
            ->backgroundColor(['rgba(54, 162, 235, 0.2)'])
            ->color(['rgb(54, 162, 235)']);

        $countChart = new AdminChart();
        $countChart->labels(['Users', 'Tenants'])
            ->dataset('User vs Tenant Count', 'doughnut', [$userCount, $tenantCount])
            ->backgroundColor(['#ff3838', '#fbbd08']);

        $countChart->options([
            'responsive' => true,
            'legend' => ['display' => true],
            'tooltips' => ['enabled' => true],
            'aspectRatio' => 1,
            'scales' => [
                'yAxes' => [
                    [
                        'display' => true,
                    ],
                ],
                'xAxes' => [
                    [
                        'gridLines' => ['display' => false],
                        'display' => true,
                    ],
                ],
            ],
        ]);

        return view('admin.analytics', compact('tenantByAddressChart', 'stallStatusesChart', 'countChart', 'parkingChargesChart'));
    }
}
