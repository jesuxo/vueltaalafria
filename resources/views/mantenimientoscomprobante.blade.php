{{-- resources/views/mantenimientos/comprobante.blade.php --}}
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Mantenimiento #{{ $mantenimiento->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .ticket {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #e0e0e0;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 5px 0;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px dotted #eee;
        }
        .info-label {
            font-weight: bold;
            color: #34495e;
        }
        .info-value {
            color: #2c3e50;
        }
        .title-section {
            background: #e6f3ff;
            padding: 10px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: bold;
            color: #2c3e50;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #95a5a6;
            border-top: 2px dashed #e0e0e0;
            padding-top: 15px;
        }
        .badge {
            background: #c1e0cd;
            color: #2c3e50;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
            margin: 5px 0;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .ticket {
                box-shadow: none;
                border-radius: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="ticket">
    <div class="header">
        <h2>🔧 REGISTRO DE MANTENIMIENTO</h2>
        <p>Comprobante #{{ str_pad($mantenimiento->id, 6, '0', STR_PAD_LEFT) }}</p>
        <p>Fecha: {{ $mantenimiento->fechaformat  }}</p>
    </div>

    <div class="title-section">👤 DATOS DEL CLIENTE</div>
    <div class="info-row">
        <span class="info-label">Cliente:</span>
        <span class="info-value">{{ $mantenimiento->cliente->descrip }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Cédula/RIF:</span>
        <span class="info-value">{{ $mantenimiento->cliente->id3 }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Teléfono:</span>
        <span class="info-value">{{ $mantenimiento->cliente->telef ?? $mantenimiento->cliente->movil ?? 'N/A' }}</span>
    </div>

    <div class="title-section">🚗 DATOS DEL VEHÍCULO</div>
    <div class="info-row">
        <span class="info-label">Placa:</span>
        <span class="info-value">{{ $mantenimiento->vehiculo->identificacion }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Marca/Modelo:</span>
        <span class="info-value">{{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Año:</span>
        <span class="info-value">{{ $mantenimiento->vehiculo->year ?? 'N/A' }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Tipo:</span>
        <span class="info-value">{{ $mantenimiento->vehiculo->tipo->tipo ?? 'N/A' }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Kilometraje:</span>
        <span class="info-value">{{ number_format($mantenimiento->kilometraje, 0, ',', '.') }} km</span>
    </div>

    <div class="title-section">🔧 MANTENIMIENTO REALIZADO</div>
    <div class="info-row">
        <span class="info-label">Tipo:</span>
        <span class="info-value">
                @switch($mantenimiento->tipo_mantenimiento)
                @case('cambio_aceite') Cambio de Aceite @break
                @case('cambio_filtro_aceite') Cambio Filtro de Aceite @break
                @case('cambio_filtro_gasolina') Cambio Filtro de Gasolina @break
                @case('cambio_filtro_aire') Cambio Filtro de Aire @break
                @case('mantenimiento_inyectores') Mantenimiento de Inyectores @break
                @case('bateria') Batería @break
                @default Otros
            @endswitch
            </span>
    </div>

    @if($mantenimiento->producto_utilizado)
        <div class="info-row">
            <span class="info-label">Producto:</span>
            <span class="info-value">{{ $mantenimiento->producto_utilizado }} {{ $mantenimiento->marca_producto ? '- ' . $mantenimiento->marca_producto : '' }}</span>
        </div>
    @endif

    @if($mantenimiento->costo)
        <div class="info-row">
            <span class="info-label">Costo:</span>
            <span class="info-value">$ {{ number_format($mantenimiento->costo, 2, ',', '.') }}</span>
        </div>
    @endif

    @if($mantenimiento->proximo_mantenimiento || $mantenimiento->proximo_kilometraje)
        <div class="title-section">⏰ PRÓXIMO MANTENIMIENTO</div>
        @if($mantenimiento->proximo_mantenimiento)
            <div class="info-row">
                <span class="info-label">Fecha sugerida:</span>
                <span class="info-value">{{ $mantenimiento->proximo_mantenimiento->format('d/m/Y') }}</span>
            </div>
        @endif
        @if($mantenimiento->proximo_kilometraje)
            <div class="info-row">
                <span class="info-label">Kilometraje sugerido:</span>
                <span class="info-value">{{ number_format($mantenimiento->proximo_kilometraje, 0, ',', '.') }} km</span>
            </div>
        @endif
    @endif

    @if($mantenimiento->observaciones)
        <div class="title-section">📝 OBSERVACIONES</div>
        <div style="background: #fff9e6; padding: 10px; border-radius: 8px; margin: 10px 0;">
            {{ $mantenimiento->observaciones }}
        </div>
    @endif

    <div style="text-align: center; margin: 15px 0;">
        <span class="badge">REGISTRADO POR: {{ $mantenimiento->usuario->first_name ?? 'SISTEMA' }}</span>
    </div>

    <div class="footer">
        <p>¡Gracias por confiar en nosotros!</p>
        <p>Este es un comprobante digital de mantenimiento</p>
        <p style="font-size: 10px;">{{ date('d/m/Y h:i:s A') }}</p>
    </div>

    <div style="text-align: center; margin-top: 20px;" class="no-print">
        <button onclick="window.print()" style="background: #c5d9e8; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; margin-right: 10px;">
            🖨️ Imprimir
        </button>
        <button onclick="window.close()" style="background: #e0e0e0; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">
            ❌ Cerrar
        </button>
    </div>
</div>

<script>
    // Auto-imprimir al cargar (opcional, descomenta si quieres que imprima automáticamente)
    // window.onload = function() {
    //     setTimeout(function() {
    //         window.print();
    //     }, 500);
    // }
</script>
</body>
</html>
