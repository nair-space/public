@extends('layouts.app')

@section('page-title', 'Sebaran Data')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- By Provinsi -->
        <div class="card">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Klien per Provinsi</h2>
            <canvas id="provinsiChart" height="300"></canvas>
        </div>

        <!-- By Disabilitas -->
        <div class="card">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Klien per Jenis Disabilitas</h2>
            <canvas id="disabilitasChart" height="300"></canvas>
        </div>
    </div>

    @push('scripts')
        <script>
            const provinsiData = @json($byProvinsi);
            const disabilitasData = @json($byDisabilitas);

            // Provinsi Chart
            new Chart(document.getElementById('provinsiChart'), {
                type: 'bar',
                data: {
                    labels: provinsiData.map(item => item.provinsi),
                    datasets: [{
                        label: 'Jumlah Klien',
                        data: provinsiData.map(item => item.total),
                        backgroundColor: '#3B82F6',
                        borderColor: '#1F2937',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Disabilitas Chart
            new Chart(document.getElementById('disabilitasChart'), {
                type: 'doughnut',
                data: {
                    labels: disabilitasData.map(item => item.jenis_disabilitas),
                    datasets: [{
                        data: disabilitasData.map(item => item.total),
                        backgroundColor: [
                            '#3B82F6', '#FACC15', '#22C55E', '#EF4444', '#8B5CF6',
                            '#F97316', '#06B6D4', '#EC4899', '#84CC16', '#6366F1'
                        ],
                        borderColor: '#1F2937',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        </script>
    @endpush
@endsection