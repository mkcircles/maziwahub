<template>
    <div class="h-80 w-full">
        <canvas ref="canvasRef"></canvas>
    </div>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Chart, registerables, type ChartData, type ChartOptions } from 'chart.js';
import 'chartjs-adapter-date-fns';

Chart.register(...registerables);

type SummaryPoint = {
    date: string;
    total_volume: number;
};

const props = defineProps<{
    summary: SummaryPoint[];
}>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
let chart: Chart<'bar'> | null = null;

const buildChart = () => {
    if (!canvasRef.value) return;

    const sorted = [...props.summary].sort(
        (a, b) => new Date(a.date).getTime() - new Date(b.date).getTime(),
    );

    const labels = sorted.map((point) => point.date);
    const totals = sorted.map((point) => Number(point.total_volume ?? 0));

    const data: ChartData<'bar'> = {
        labels,
        datasets: [
            {
                label: 'Total Volume (L)',
                data: totals,
                borderRadius: 10,
                backgroundColor: 'rgba(56, 189, 248, 0.65)',
                borderColor: 'rgba(14, 165, 233, 0.9)',
                hoverBackgroundColor: 'rgba(14, 165, 233, 0.9)',
                maxBarThickness: 32,
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
                ticks: {
                    maxRotation: 0,
                    autoSkipPadding: 12,
                },
                grid: {
                    display: false,
                },
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(148, 163, 184, 0.15)',
                },
                ticks: {
                    callback: (value) =>
                        `${Number(value).toLocaleString(undefined, {
                            maximumFractionDigits: 0,
                        })}`,
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
    () => props.summary,
    () => {
        destroyChart();
        buildChart();
    },
    { deep: true },
);

onMounted(buildChart);
onBeforeUnmount(destroyChart);
</script>


