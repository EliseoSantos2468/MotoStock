<h1>¡Gracias por tu compra!</h1>
<p>Hola, adjunto a este correo encontrarás el detalle de tu compra realizada el {{ $recibo->fecha }}.</p>
<p>Total pagado: <strong>${{ number_format($recibo->total, 2) }}</strong></p>
<p>Si tienes alguna duda, contáctanos.</p>
<p style="font-size: 12px; color: #666;">
    ¿Tienes alguna duda con tus productos? <br>
    Responde directamente a este correo o escríbenos a: <strong>{{ $correoContacto }}</strong>
</p>