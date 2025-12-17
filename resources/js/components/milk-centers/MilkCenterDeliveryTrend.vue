<template>
    <div class="h-80 w-full mb-6 rounded-lg bg-white p-4 shadow">
        <h3 class="mb-4 text-base font-semibold text-gray-900">Collection Volume Trend</h3>
        <div class="relative h-64 w-full">
            <canvas ref="canvasRef"></canvas>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Chart, registerables, type ChartData, type ChartOptions } from 'chart.js';
import 'chartjs-adapter-date-fns';

Chart.register(...registerables);

interface MilkDeliverySummary {
    delivery_date: string;
    volume_liters: number;
}

const props = defineProps<{
    deliveries: MilkDeliverySummary[];
}>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
let chart: Chart<'bar'> | null = null;

const createChart = () => {
    if (!canvasRef.value) return;

    // Aggregate deliveries by date
    const aggregated = props.deliveries.reduce((acc, curr) => {
        const date = curr.delivery_date;
        if (!acc[date]) {
            acc[date] = 0;
        }
        acc[date] += Number(curr.volume_liters);
        return acc;
    }, {} as Record<string, number>);

    // Convert to array and sort by date
    const sortedData = Object.entries(aggregated)
        .map(([date, total]) => ({ date, total }))
        .sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());

    const labels = sortedData.map(d => d.date);
    const totals = sortedData.map(d => d.total);

    const data: ChartData<'bar'> = {
        labels,
        datasets: [
            {
                label: 'Total Collected (L)',
                data: totals,
                borderRadius: 4,
                backgroundColor: '#3b82f6', // blue-500
                borderColor: '#2563eb', // blue-600
                borderWidth: 1,
                hoverBackgroundColor: '#2563eb',
                maxBarThickness: 40,
            },
        ],
    };

    const options: ChartOptions<'bar'> = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                display: false,
            },
            tooltip: {
                callbacks: {
                    label: (context) => {
                        const value = context.parsed.y ?? 0;
                        return `${value.toLocaleString(undefined, {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 1,
                        })} L`;
                    },
                    title: (context) => {
                        const date = new Date(context[0].label);
                        return date.toLocaleDateString(undefined, {
                            weekday: 'short',
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                    }
                },
                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                padding: 10,
                cornerRadius: 8,
            },
        },
        scales: {
            x: {
                type: 'time',
                time: {
                    unit: 'day',
                    displayFormats: {
                        day: 'MMM d',
                    },
                    tooltipFormat: 'PP',
                },
                grid: {
                    display: false,
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Volume (Liters)',
                    font: {
                        size: 12,
                        weight: 'bold'
                    }
                },
                grid: {
                    color: '#f3f4f6',
                },
                border: {
                    display: false
                }
            },
        },
    };

    chart = new Chart(canvasRef.value, {
        type: 'bar',
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
    () => props.deliveries,
    () => {
        destroyChart();
        createChart();
    },
    { deep: true }
);

onMounted(createChart);
onBeforeUnmount(destroyChart);
</script>
