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
const chartOptionsT = ref(createChartOptions())
const lastXDays = ref('60')
const lastXDaysT = ref('60')  //available options: 30days, 60days, year, all //Ref para ser reativa
const transactions = ref([])
const totalTransactions= ref(0)
const totalDebitTransactions= ref(0)
const totalCreditTransactions= ref(0)
const chartData = ref({
    datasets: [],
})
const chartDataT = ref({
    datasets: [],
})

const periodClick = (period) => {
    setChartData(period)
}
const periodClickT = (period) => {
    setChartDataT(period)
    //calculateTransactions(period)
}
// const calculateTransactions = async (period) => {
//     console.log(transactions.value)    
//     console.log(transactions.value[0])
//     //todo
// }
const setChartData = async (period) => {
    lastXDays.value = period // Atualiza o estado da variável
    await loadChartData() // Recarrega os dados do gráfico
}
const setChartDataT = async (period) => {
    lastXDaysT.value = period // Atualiza o estado da variável
    await loadChartDataT() // Recarrega os dados do gráfico
}
const loadChartData = async () => {
    const response = await axios.get(`vcards/${authStore.user.username}/statistics/balance?range=${lastXDays.value}`)
    const newChartData = {
        labels: [],
        datasets: [
            {
                label: `Balance Updates`,//`Last ${ lastXDays.value } days balance updates`,
                backgroundColor: '#11540b',
                data: [],
            },
        ],
    }
    newChartData.labels = response.data.labels
    newChartData.datasets[0].data = response.data.data
    chartData.value = newChartData
}
const loadChartDataT = async () => {
    const response = await axios.get(`vcards/${authStore.user.username}/statistics/transactions?range=${lastXDaysT.value}`)
    const newChartDataT = {
        labels: [],
        datasets: [
            {
                label: `Transactions`,//`Transactions of the Last ${ lastXDays.value } days`,
                backgroundColor: '#8f4700',
                data: [],
            },
        ],
    }
    newChartDataT.labels = response.data.labels
    newChartDataT.datasets[0].data = response.data.data
    chartDataT.value = newChartDataT
    //console.log(response.data.data)

    //Calculate total transactions
    transactions.value = response.data.data
    //totalTransactions.value = transactions.value.length

}


onMounted(() => {
    loadChartData()
    loadChartDataT()
    //calculateTransactions('60')
})

</script>
<template>
    <br>

    <br>
    <div class="d-flex justify-content-between">
        <div class="mx-2">
          <h4>Balance Fluctuation</h4>
        </div>
      </div>
      <!-- <hr> -->
      <div class="mx-2 mt-2">
        <div>
          <!-- <p>Total Credit Transactions: {{ totalCreditTransactions }}</p>
          <p>Total Debit Transactions: {{ totalDebitTransactions }}</p> -->
          <p>Graph of the Balance Fluctuation of the last {{ lastXDays }} 
            {{ lastXDays === 'year' ? '' : (lastXDays === 'all' ? ' time' : ' days') }}</p>
            <br>
        </div>
    </div>
    <Line :data="chartData" :options="chartOptions" />
    <br>
    <div class="mx-2">
        <button class="btn btn-xs btn-outline-dark" @click="periodClick('30')">30</button>
        <button class="btn btn-xs btn-outline-dark" @click="periodClick('60')">60</button>
        <button class="btn btn-xs btn-outline-dark" @click="periodClick('year')">Year</button>
        <button class="btn btn-xs btn-outline-dark" @click="periodClick('all')">All</button>
      </div>
      <!-- <hr> -->
      <br>
      <hr>
      <div class="d-flex justify-content-between">
        <div class="mx-2">
            <br>
          <h4>Last Transactions</h4>
        </div>
    </div>
    <div class="mx-2 mt-2">
        <div>
          <!-- <p>Total Credit Transactions: {{ totalCreditTransactions }}</p>
          <p>Total Debit Transactions: {{ totalDebitTransactions }}</p> -->
          <p>Total Transactions of the last {{ lastXDaysT }} 
            {{ lastXDaysT === 'year:' ? '' : (lastXDaysT === 'all' ? ' time:' : ' days:') }}
            {{ totalTransactions }}</p>
            <br>
        </div>
    </div>
    <Line :data="chartDataT" :options="chartOptionsT" />
    <br>
    <div class="mx-2">
        <button class="btn btn-xs btn-outline-dark" @click="periodClickT('30')">30</button>
        <button class="btn btn-xs btn-outline-dark" @click="periodClickT('60')">60</button>
        <button class="btn btn-xs btn-outline-dark" @click="periodClickT('year')">Year</button>
        <button class="btn btn-xs btn-outline-dark" @click="periodClickT('all')">All</button>
      </div>
      <!-- <hr> -->
      <br>

    
</template>


<style scoped>
button {
    margin: 3px; /* Optional: Add some margin between buttons */
}
h4 {
    color: darkgreen;
}
p {
    font-size: 17px;
    padding-left: 30px;
    font-weight: 600;
}
</style>