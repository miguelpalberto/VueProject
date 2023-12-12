<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { Line } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement } from 'chart.js'
import { useAuthStore } from '../../../stores/auth';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement)

const authStore = useAuthStore()

const createChartOptions = () => {
    return {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            y: {
                suggestedMin: 0,
            },
            x: {
                display: false
            }
        }
    }

}

const chartOptions = ref(createChartOptions())

const chartData = ref({
    datasets: [],
})

const loadChartData = async () => {
    const response = await axios.get(`vcards/${authStore.user.username}/statistics/balance?range=all`)
    const newChartData = {
        labels: [],
        datasets: [
            {
                label: 'Last 30 days balance updates',
                backgroundColor: '#000000',
                data: [],
            },
        ],
    }
    newChartData.labels = response.data.labels
    newChartData.datasets[0].data = response.data.data
    chartData.value = newChartData
}

onMounted(() => {
    loadChartData()
})

</script>
<template>
    <Line :data="chartData" :options="chartOptions" />
</template>