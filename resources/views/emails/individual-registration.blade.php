<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmación de Inscripción - Vuelta a la Fría 2026</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #00ecfe 0%, #00c4d4 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #00ecfe;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .footer {
            background: #f4f4f4;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            background: #00ecfe;
            color: #000;
            border-radius: 5px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #00ecfe;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🚴‍♂️ Vuelta a la Fría 2026</h1>
        <p>La Vuelta Menor más importante de Venezuela</p>
    </div>

    <div class="content">
        <h2>¡Hola {{ $athlete->first_name }} {{ $athlete->last_name }}!</h2>
        <p>Tu inscripción para la <strong>Vuelta a la Fría 2026</strong> ha sido registrada exitosamente.</p>

        <div class="info-box">
            <h3>📋 Detalles de tu inscripción:</h3>
            <table>
                <tr>
                    <td width="40%"><strong>Nombre:</strong></td>
                    <td>{{ $athlete->first_name }} {{ $athlete->last_name }}</td>
                </tr>
                <tr>
                    <td><strong>Categoría:</strong></td>
                    <td>{{ $athlete->category }}</td>
                </tr>
                <tr>
                    <td><strong>Género:</strong></td>
                    <td>{{ $athlete->gender }}</td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td>{{ $registration->email }}</td>
                </tr>
                <tr>
                    <td><strong>Teléfono:</strong></td>
                    <td>{{ $registration->phone }}</td>
                </tr>
                <tr>
                    <td><strong>Monto pagado:</strong></td>
                    <td><strong>${{ number_format($registration->amount, 2) }} USD</strong></td>
                </tr>
            </table>
        </div>

        <div class="info-box">
            <h3>📌 Información importante:</h3>
            <ul>
                <li>📅 <strong>Fechas:</strong> 12, 13 y 14 de junio de 2026</li>
                <li>📍 <strong>Lugar:</strong> La Fría, Municipio García de Hevia, Táchira</li>
                <li>⏰ <strong>Hora de llegada:</strong> 1 hora antes de tu categoría</li>
                <li>🪪 <strong>Documentación:</strong> Presenta tu cédula o pasaporte el día del evento</li>
                <li>🚴‍♂️ <strong>Casco obligatorio</strong> durante toda la competencia</li>
            </ul>
        </div>

        <div class="info-box">
            <h3>📞 Contacto:</h3>
            <p><strong>Jhoana Noriega</strong> (Coordinadora de Inscripciones)<br>
                📱 WhatsApp: <a href="https://wa.me/584247371101">+58 424-7371101</a><br>
                📧 Email: vueltalafria@gmail.com</p>
        </div>

        <p style="text-align: center;">
            <a href="{{ route('home') }}" class="btn">🌐 Visitar sitio web</a>
        </p>
    </div>

    <div class="footer">
        <p>Este es un correo automático, por favor no responder.</p>
        <p>© 2026 Vuelta a la Fría - Todos los derechos reservados</p>
    </div>
</div>
</body>
</html>
