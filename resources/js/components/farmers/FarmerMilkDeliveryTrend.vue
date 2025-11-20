<template>
    <div class="h-72 w-full">
        <canvas ref="canvasRef"></canvas>
    </div>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Chart, registerables, type ChartData, type ChartOptions } from 'chart.js';
import 'chartjs-adapter-date-fns';
import type { MilkDelivery } from '../../stores/geographyStore';

Chart.register(...registerables);

const props = defineProps<{
    deliveries: MilkDelivery[];
}>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
let chart: Chart<'bar'> | null = null;

const createChart = () => {
    if (!canvasRef.value) return;

    const sorted = [...props.deliveries].sort((a, b) => {
        const dateA = new Date(a.delivery_date ?? '').getTime();
        const dateB = new Date(b.delivery_date ?? '').getTime();
        return dateA - dateB;
    });

    const labels = sorted.map((delivery) => delivery.delivery_date ?? '');
    const totals = sorted.map((delivery) => Number(delivery.volume_liters ?? 0));

    const data: ChartData<'bar'> = {
        labels,
        datasets: [
            {
                label: 'Total Volume (L)',
                data: totals,
                borderRadius: 6,
                backgroundColor: '#2563ebcc',
                borderColor: '#1d4ed8',
                hoverBackgroundColor: '#1d4ed8',
                maxBarThickness: 28,
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
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        })} L`;
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
                title: {
                    display: true,
                    text: 'Volume (Liters)',
                },
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
