<template>
    <div class="h-80 w-full rounded-lg p-5">
        <div class="mb-4">
            <h3 class="text-sm font-medium text-surface-500 uppercase tracking-wide">Historical Activity</h3>
            <p class="text-xs text-surface-400">Past 30 days of agent activity</p>
        </div>
        <div class="h-64 w-full">
            <canvas ref="canvasRef"></canvas>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Chart, registerables, type ChartData, type ChartOptions } from 'chart.js';
import 'chartjs-adapter-date-fns';

Chart.register(...registerables);

const props = defineProps<{
    historicalData: {
        farmers: Array<{ date: string; count: number }>;
        cows: Array<{ date: string; count: number }>;
        milk_productions: Array<{ date: string; count: number }>;
        milk_deliveries: Array<{ date: string; count: number }>;
    } | null;
}>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
let chart: Chart<'line'> | null = null;

const createChart = () => {
    if (!canvasRef.value || !props.historicalData) return;

    // Get all unique dates across all datasets
    const allDates = new Set<string>();
    const datasets = [
        props.historicalData.farmers,
        props.historicalData.cows,
        props.historicalData.milk_productions,
        props.historicalData.milk_deliveries,
    ];

    datasets.forEach(dataset => {
        dataset?.forEach(item => allDates.add(item.date));
    });

    const labels = Array.from(allDates).sort();

    // Fill missing dates with 0 values
    const mapToLabels = (dataset: Array<{ date: string; count: number }> | undefined) => {
        if (!dataset) return labels.map(() => 0);

        const dataMap = new Map(dataset.map(item => [item.date, item.count]));
        return labels.map(date => dataMap.get(date) ?? 0);
    };

    const data: ChartData<'line'> = {
        labels,
        datasets: [
            {
                label: 'Farmers Registered',
                data: mapToLabels(props.historicalData.farmers),
                borderColor: '#10b981', // emerald-500
                backgroundColor: '#10b98122',
                tension: 0.3,
                fill: true,
            },
            {
                label: 'Cows Registered',
                data: mapToLabels(props.historicalData.cows),
                borderColor: '#f59e0b', // amber-500
                backgroundColor: '#f59e0b22',
                tension: 0.3,
            },
            {
                label: 'Milk Productions',
                data: mapToLabels(props.historicalData.milk_productions),
                borderColor: '#3b82f6', // blue-500
                backgroundColor: '#3b82f622',
                tension: 0.3,
            },
            {
                label: 'Milk Deliveries',
                data: mapToLabels(props.historicalData.milk_deliveries),
                borderColor: '#8b5cf6', // violet-500
                backgroundColor: '#8b5cf622',
                tension: 0.3,
            },
        ],
    };

    const options: ChartOptions<'line'> = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    boxWidth: 8,
                }
            },
            tooltip: {
                callbacks: {
                    label: (context) => {
                        const value = context.parsed.y ?? 0;
                        return `${context.dataset.label}: ${value}`;
                    },
                },
            },
        },
        scales: {
            x: {
                type: 'time',
                time: {
                    tooltipFormat: 'PP',
                    unit: 'day',
                    displayFormats: {
                        day: 'MMM d',
                    },
                },
                grid: {
                    display: false,
                },
            },
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0 // Only whole numbers for counts
                }
            },
        },
    };

    chart = new Chart(canvasRef.value, {
        type: 'line',
        data,
        options,
    });
};

const destroyChart = () => {
    if (chart) {
        chart.destroy();
        chart = null;
    }
};

watch(
    () => props.historicalData,
    () => {
        destroyChart();
        createChart();
    },
    { deep: true, immediate: true }
);

onMounted(createChart);
onBeforeUnmount(destroyChart);
</script>
