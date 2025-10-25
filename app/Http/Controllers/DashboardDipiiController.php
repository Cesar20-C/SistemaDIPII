<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardDipiiController extends Controller
{
    // Configuración según tus tablas
    protected array $cfg = [
        'compras' => [
            'table'        => 'ingresos',   // tu tabla
            'date_col'     => 'fecha',
            'proveedor_fk' => 'proveedor_id',
            'producto_col' => 'producto',
            'peso_col'     => 'peso_total', // kg
        ],
        'proveedores' => [
            'table' => 'proveedores',
            'name'  => 'nombre',
            'pk'    => 'id',
        ],
        'documentos' => [
            ['table' => 'etiquetas',    'date_col' => 'fecha_elaboracion'],
            ['table' => 'certificados', 'date_col' => 'fecha_elaboracion'],
        ],
    ];

    public function index(Request $request)
    {
        [$desde, $hasta, $label] = $this->resolverRango($request);

        $kpis                 = $this->kpis($desde, $hasta);
        $comprasPorProducto   = $this->comprasPorProducto($desde, $hasta, 10);
        $topProveedores       = $this->topProveedores($desde, $hasta, 5);
        $documentosPorMes     = $this->documentosPorMes($desde, $hasta);
        $productosFrecuentes  = $this->productosMasFrecuentes($desde, $hasta, 10);

        return view('dashboard.index', compact(
            'kpis','comprasPorProducto','topProveedores','documentosPorMes','productosFrecuentes'
        ) + ['filters' => [
            'desde'=>$desde->toDateString(),
            'hasta'=>$hasta->toDateString(),
            'label'=>$label
        ]]);
    }

    // -------- Helpers
    protected function resolverRango(Request $request): array
    {
        $hoy  = Carbon::today();
        $anio = (int)($request->input('anio') ?: $hoy->year);
        $mes  = (int)($request->input('mes')  ?: $hoy->month);
        $modo = $request->input('modo', 'anio'); // 'mes'|'anio'|'rango'

        if ($modo === 'mes') {
            $desde = Carbon::create($anio, $mes, 1)->startOfDay();
            $hasta = (clone $desde)->endOfMonth();
            $label = $desde->isoFormat('MMMM YYYY');
        } elseif ($modo === 'rango') {
            $desde = Carbon::parse($request->input('desde', $hoy->copy()->startOfYear()))->startOfDay();
            $hasta = Carbon::parse($request->input('hasta', $hoy))->endOfDay();
            $label = $desde->toDateString().' → '.$hasta->toDateString();
        } else {
            $desde = Carbon::create($anio, 1, 1)->startOfDay();
            $hasta = Carbon::create($anio, 12, 31)->endOfDay();
            $label = (string)$anio;
        }
        return [$desde, $hasta, $label];
    }

    protected function baseCompra()
    {
        $c  = $this->cfg['compras'];
        $pv = $this->cfg['proveedores'];

        return DB::table($c['table'].' as c')
            ->join($pv['table'].' as pv', 'pv.'.$pv['pk'], '=', 'c.'.$c['proveedor_fk']);
    }

    protected function comprasPorProducto(Carbon $desde, Carbon $hasta, int $limit = 10)
    {
        $c = $this->cfg['compras'];

        return $this->baseCompra()
            ->whereBetween('c.'.$c['date_col'], [$desde, $hasta])
            ->selectRaw('c.'.$c['producto_col'].' as producto, SUM(c.'.$c['peso_col'].') as total_kg')
            ->groupBy('producto')
            ->orderByDesc('total_kg')
            ->limit($limit)
            ->get();
    }

    protected function topProveedores(Carbon $desde, Carbon $hasta, int $limit = 5)
    {
        $c  = $this->cfg['compras'];
        $pv = $this->cfg['proveedores'];

        return $this->baseCompra()
            ->whereBetween('c.'.$c['date_col'], [$desde, $hasta])
            ->selectRaw('pv.'.$pv['name'].' as proveedor, SUM(c.'.$c['peso_col'].') as total_kg')
            ->groupBy('proveedor')
            ->orderByDesc('total_kg')
            ->limit($limit)
            ->get();
    }

    // Productos que más ingresan (frecuencia de registros)
    protected function productosMasFrecuentes(Carbon $desde, Carbon $hasta, int $limit = 10)
    {
        $c = $this->cfg['compras'];

        return DB::table($c['table'].' as c')
            ->whereBetween('c.'.$c['date_col'], [$desde, $hasta])
            ->selectRaw('c.'.$c['producto_col'].' as producto, COUNT(*) as veces')
            ->groupBy('producto')
            ->orderByDesc('veces')
            ->limit($limit)
            ->get();
    }

    protected function documentosPorMes(Carbon $desde, Carbon $hasta)
    {
        $map = [];

        foreach ($this->cfg['documentos'] as $doc) {
            $tbl = $doc['table'];
            $col = $doc['date_col'];

            if (!Schema::hasTable($tbl)) continue;
            if (!Schema::hasColumn($tbl, $col)) {
                if (Schema::hasColumn($tbl, 'created_at')) $col = 'created_at';
                else continue;
            }

            $pairs = DB::table($tbl)
                ->whereBetween($col, [$desde, $hasta])
                ->selectRaw("DATE_FORMAT($col, '%Y-%m') as ym, COUNT(*) as cnt")
                ->groupBy('ym')
                ->pluck('cnt', 'ym');

            foreach ($pairs as $ym => $cnt) {
                $map[$ym] = ($map[$ym] ?? 0) + (int)$cnt;
            }
        }

        ksort($map);

        return collect($map)->map(fn($cnt, $ym) => ['ym' => $ym, 'cnt' => $cnt])->values();
    }

    protected function kpis(Carbon $desde, Carbon $hasta): array
    {
        $c  = $this->cfg['compras'];
        $pv = $this->cfg['proveedores'];

        $base = $this->baseCompra()->whereBetween('c.'.$c['date_col'], [$desde, $hasta]);

        $totalKg            = (float) ((clone $base)->selectRaw('SUM(c.'.$c['peso_col'].') as t')->value('t') ?? 0);
        $proveedoresActivos = (int)   ((clone $base)->selectRaw('COUNT(DISTINCT pv.'.$pv['pk'].') as n')->value('n') ?? 0);
        $productosDistintos = (int)   ((clone $base)->selectRaw('COUNT(DISTINCT c.'.$c['producto_col'].') as n')->value('n') ?? 0);
        $comprasRegistradas = (int)   DB::table($c['table'])->whereBetween($c['date_col'], [$desde, $hasta])->count();

        $docTotal = $this->documentosPorMes($desde, $hasta)->sum('cnt');

        $topProv = $this->topProveedores($desde, $hasta, 1)->first();
        $topProd = $this->comprasPorProducto($desde, $hasta, 1)->first();

        return [
            'total_kg'             => $totalKg,
            'proveedores_activos'  => $proveedoresActivos,
            'productos_distintos'  => $productosDistintos,
            'compras_registradas'  => $comprasRegistradas,
            'documentos_generados' => (int)$docTotal,
            'proveedor_top'        => $topProv ? ['proveedor' => $topProv->proveedor, 'kg' => (float)$topProv->total_kg] : null,
            'producto_top'         => $topProd ? ['producto' => $topProd->producto, 'kg' => (float)$topProd->total_kg] : null,
        ];
    }

    public function data(Request $request)
{
    [$desde, $hasta, $label] = $this->resolverRango($request);

    return response()->json([
        'filters' => [
            'label' => $label,
            'desde' => $desde->toDateString(),
            'hasta' => $hasta->toDateString(),
        ],
        'kpis'                => $this->kpis($desde, $hasta),
        'comprasPorProducto'  => $this->comprasPorProducto($desde, $hasta, 10),
        'topProveedores'      => $this->topProveedores($desde, $hasta, 5),
        'documentosPorMes'    => $this->documentosPorMes($desde, $hasta),
        'productosFrecuentes' => $this->productosMasFrecuentes($desde, $hasta, 10),
    ]);
}


}
