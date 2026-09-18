<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/lib/utils';

type Point = { label: string; value: number };

const props = withDefaults(
    defineProps<{
        data: Point[];
        height?: number;
        class?: string;
    }>(),
    {
        height: 220,
    },
);

const padding = { top: 16, right: 12, bottom: 28, left: 12 };

const chart = computed(() => {
    const values = props.data.map((d) => d.value);
    const max = Math.max(1, ...values);
    const min = 0;

    const width = 600;
    const chartWidth = width - padding.left - padding.right;
    const chartHeight = props.height - padding.top - padding.bottom;

    const stepX =
        props.data.length > 1 ? chartWidth / (props.data.length - 1) : 0;

    const coords = props.data.map((d, i) => ({
        x: padding.left + stepX * i,
        y:
            padding.top +
            chartHeight -
            (chartHeight * (d.value - min)) / (max - min),
    }));

    const smooth = (pts: { x: number; y: number }[]) => {
        if (pts.length < 2) {
            return '';
        }

        let d = `M ${pts[0].x} ${pts[0].y}`;

        for (let i = 0; i < pts.length - 1; i++) {
            const p0 = pts[i - 1] ?? pts[i];
            const p1 = pts[i];
            const p2 = pts[i + 1];
            const p3 = pts[i + 2] ?? p2;

            const c1x = p1.x + (p2.x - p0.x) / 6;
            const c1y = p1.y + (p2.y - p0.y) / 6;
            const c2x = p2.x - (p3.x - p1.x) / 6;
            const c2y = p2.y - (p3.y - p1.y) / 6;

            d += ` C ${c1x.toFixed(1)} ${c1y.toFixed(1)}, ${c2x.toFixed(1)} ${c2y.toFixed(1)}, ${p2.x.toFixed(1)} ${p2.y.toFixed(1)}`;
        }

        return d;
    };

    const curve = smooth(coords);

    const last = coords[coords.length - 1] ?? {
        x: padding.left,
        y: padding.top,
    };
    const first = coords[0] ?? { x: padding.left, y: padding.top };

    const points = coords.map((p, i) => ({ ...p, label: props.data[i].label }));

    return {
        points,
        linePath: curve,
        areaPath: `${curve} L ${last.x} ${padding.top + chartHeight} L ${first.x} ${padding.top + chartHeight} Z`,
        max,
    };
});
</script>

<template>
    <svg
        :viewBox="`0 0 600 ${height}`"
        preserveAspectRatio="none"
        class="w-full"
        :class="cn('overflow-visible', props.class)"
        role="img"
        aria-label="Grafik aktivitas"
    >
        <defs>
            <linearGradient id="area-fill" x1="0" y1="0" x2="0" y2="1">
                <stop
                    offset="0%"
                    stop-color="var(--chart-1)"
                    stop-opacity="0.28"
                />
                <stop
                    offset="100%"
                    stop-color="var(--chart-1)"
                    stop-opacity="0.02"
                />
            </linearGradient>
        </defs>

        <!-- Baseline grid -->
        <line
            x1="12"
            :y1="height - 28"
            :x2="588"
            :y2="height - 28"
            class="stroke-border"
            stroke-width="1"
        />

        <!-- Area under the curve -->
        <path :d="chart.areaPath" fill="url(#area-fill)" />

        <!-- Line -->
        <path
            :d="chart.linePath"
            class="stroke-chart-1"
            stroke-width="2.5"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
        />

        <!-- Points -->
        <circle
            v-for="(p, i) in chart.points"
            :key="i"
            :cx="p.x"
            :cy="p.y"
            r="3"
            class="fill-background stroke-chart-1"
            stroke-width="2"
        />
        <circle
            :cx="chart.points.length ? chart.points[chart.points.length - 1].x : 0"
            :cy="chart.points.length ? chart.points[chart.points.length - 1].y : 0"
            r="4.5"
            class="fill-chart-1"
        />

        <!-- X labels -->
        <text
            v-for="(p, i) in chart.points"
            :key="i"
            :x="p.x"
            :y="height - 8"
            text-anchor="middle"
            class="fill-muted-foreground text-[10px]"
        >
            {{ p.label }}
        </text>
    </svg>
</template>