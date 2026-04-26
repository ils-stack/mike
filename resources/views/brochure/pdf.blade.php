<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1,h2,h3 { margin: 0; padding:0; }
        .container { width: 100%; }
        .unit-block { border:1px solid #ccc; padding:10px; margin-bottom:10px; }
        .header { text-align:center; margin-bottom:20px; }
        .section-title { background:#eee; padding:6px; font-weight:bold; }
        .image { width:100%; height:auto; margin-bottom:10px; }
        .table { width:100%; border-collapse: collapse; margin-bottom:15px; }
        .table th, .table td { border:1px solid #ccc; padding:6px; }
    </style>
</head>

<body>

<div class="header">
    <h2>{{ $brochure->title }}</h2>
    <p>Prepared by: {{ Auth::user()->name }} {{ Auth::user()->surname }}</p>
</div>

@foreach($units as $unit)

<div class="unit-block">
    <h3>Unit {{ $unit->unit_no }} — {{ $unit->unit_type }}</h3>

    <!-- Random image -->
    @php
        $images = [
            'assets/user_4/images/68ceba2247db7.png',
            'assets/user_4/images/68cec3c6b8506.png',
            'assets/user_4/images/68cec3e486926.jpg',
            'assets/user_4/images/68cec4c28a3ce.jpeg',
            'assets/user_4/images/68cec4c619699.jpg',
            'assets/user_4/images/68cec4cd210ba.jpg'
        ];
        $randImg = $images[array_rand($images)];
    @endphp

    <img src="{{ public_path('storage/'.$randImg) }}" class="image">

    <table class="table">
        <tr><th>Unit Size</th><td>{{ $unit->unit_size }} m²</td></tr>
        <tr><th>Gross Rental</th><td>{{ $unit->gross_rental }}</td></tr>
        <tr><th>Sale Price</th><td>{{ $unit->sale_price }}</td></tr>
        <tr><th>Yield</th><td>{{ $unit->yield_percentage }}</td></tr>
        <tr><th>Availability</th><td>{{ $unit->availability }}</td></tr>
    </table>

</div>

@endforeach


<h3 class="section-title"> Agents </h3>

@foreach($agents as $agent)
<p><strong>{{ $agent->company_name }}</strong><br>
Contact: {{ $agent->contact_person }}<br>
Phone: {{ $agent->cell_number }}<br>
Email: {{ $agent->email }}</p>
@endforeach


<h3 class="section-title"> Property Managers </h3>

@foreach($managers as $m)
<p><strong>{{ $m->company_name }}</strong><br>
Manager: {{ $m->manager_name }}<br>
Phone: {{ $m->cell_number }}<br>
Email: {{ $m->email }}</p>
@endforeach


</body>
</html>
