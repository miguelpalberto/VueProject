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
const numberOfTransactions = ref([])
const totalTransactions= ref(0)
const totalDebitTransactions= ref(0)
const totalCreditTransactions= ref(0)
const totalDiferenceTransactions= ref(0)
const totalDebitAmountTransactions= ref(0)
const totalCreditAmountTransactions= ref(0)
const totalNumberOfTransactions= ref(0)
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
}

const setChartData = async (period) => {
    lastXDays.value = period 
    await loadChartData()
}
const setChartDataT = async (period) => {
    lastXDaysT.value = period 
    await loadChartDataT()
}
const loadChartData = async () => {
    const response = await axios.get(`statistics/balance?range=${lastXDays.value}`)
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
    numberOfTransactions.value = []
    const response = await axios.get(`statistics/alltransactions?range=${lastXDaysT.value}`)
    const newChartDataT = {
        labels: [],
        datasets: [
            {
                label: `Number of Transactions`,//`Transactions of the Last ${ lastXDays.value } days`,
                backgroundColor: '#B32020',
                data: [],
            },
        ],
    }
    newChartDataT.labels = response.data.labels
    newChartDataT.datasets[0].data = response.data.data
    chartDataT.value = newChartDataT
    console.log(response.data.data)

    //Calcular
    numberOfTransactions.value = response.data.data
    totalNumberOfTransactions.value = 0

    for(let i = 0; i < numberOfTransactions.value.length; i++) {
        totalNumberOfTransactions.value += parseFloat(numberOfTransactions.value[i])
    }
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
          <h4>Global Balance Fluctuation</h4>
        </div>

        <div class="mx-2">
        <p>Time frame: last {{ lastXDays }} 
            {{ lastXDays === 'year' ? '' : (lastXDays === 'all' ? ' time' : ' days') }}</p>
        </div>
      </div>
      <!-- <hr> -->
            <br>
    <Line :data="chartData" :options="chartOptions" />
    <br>
    <div class="mx-2">
        <button class="btn btn-xs btn-outline-dark" @click="periodClick('30')">30</button>
        <button class="btn btn-xs btn-outline-dark" @click="periodClick('60')">60</button>
        <button class="btn btn-xs btn-outline-dark" @click="periodClick('year')">Year</button>
        <button class="btn btn-xs btn-outline-dark" @click="periodClick('all')">All</button>
      </div>
      <!-- <hr> -->
      <hr>
      <br>
      <div class="d-flex justify-content-between">
        <div class="mx-2">
            <p></p>
          <h4>Number of Transactions per Day</h4>
        </div>
        <div class="mx-2">
        <p>Time frame: last {{ lastXDaysT }} 
            {{ lastXDaysT === 'year' ? '' : (lastXDaysT === 'all' ? ' time' : ' days') }}</p>
        </div>
    </div>
    <div class="mx-2 mt-2">
        <div>
            <div class="d-flex flex-column flex-md-row">
              <!-- Original block -->
              <div class="mx-2 mt-2">
                <div>
                  <p>Total Number Of Transactions: {{ totalNumberOfTransactions }}
                  </p>
                  <!-- <p>Total Number Of Debit Transactions: {{ totalDebitTransactions }}</p>
                  <p>Total Number Of Credit Transactions: {{ totalCreditTransactions }}</p> -->
                </div>
              </div>
        
              <!-- Repeated block on the side -->
              <div class="mx-2 mt-2">
                <div>
                  <!-- <p style="opacity: 0;"> . </p> -->
                  <!-- <p>Total Diference: {{ totalDiferenceTransactions }}</p>
                  <p>Total Debit Amount: {{ totalDebitAmountTransactions }}</p>
                  <p>Total Credit Amount: {{ totalCreditAmountTransactions }}</p> -->
                </div>
              </div>
            </div>
          </div>
        <br>
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