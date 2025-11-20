<template>
    <div class="w-full">
        <canvas ref="canvasRef"></canvas>
    </div>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Chart, registerables, type ChartData, type ChartOptions } from 'chart.js';
import 'chartjs-adapter-date-fns';
import type { CowMilkProduction } from '../../stores/geographyStore';

Chart.register(...registerables);

const props = defineProps<{
    records: CowMilkProduction[];
}>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
let chart: Chart<'line'> | null = null;

const buildDataset = (label: string, data: (number | null)[], color: string) => ({
    label,
    data,
    spanGaps: true,
    tension: 0.3,
    borderColor: color,
    backgroundColor: `${color}33`,
    pointBorderColor: color,
    pointBackgroundColor: '#ffffff',
    pointHoverRadius: 5,
    pointRadius: 3,
    fill: false,
});

const createChart = () => {
    if (!canvasRef.value) return;

    const sorted = [...props.records].sort((a, b) => {
        const dateA = new Date(a.recorded_date ?? '').getTime();
        const dateB = new Date(b.recorded_date ?? '').getTime();
        return dateA - dateB;
    });

    const labels = sorted.map((record) => record.recorded_date ?? '');
    const morning = sorted.map((record) => record.morning_volume_liters ?? null);
    const midday = sorted.map((record) => record.midday_volume_liters ?? null);
    const evening = sorted.map((record) => record.evening_volume_liters ?? null);

    const data: ChartData<'line'> = {
        labels,
        datasets: [
            buildDataset('Morning', morning, '#3b82f6'),
            buildDataset('Midday', midday, '#10b981'),
            buildDataset('Evening', evening, '#f97316'),
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
                display: true,
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: (context) => {
                        const value = context.parsed.y ?? 0;
                        return `${context.dataset.label}: ${value.toLocaleString(undefined, {
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
                    maxRotation: 45,
                    minRotation: 0,
                    autoSkip: true,
                    autoSkipPadding: 16,
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
    () => props.records,
    () => {
        destroyChart();
        createChart();
    },
    { deep: true }
);

onMounted(createChart);
onBeforeUnmount(destroyChart);
</script>
