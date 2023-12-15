<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { Line, Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, ArcElement, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement } from 'chart.js'
import { useAuthStore } from '../../../stores/auth';

ChartJS.register(Title, ArcElement, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement)

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
const lastXDaysPie = ref('60') 
const transactions = ref([])
const totalTransactions= ref(0)
const totalDebitTransactions= ref(0)
const totalCreditTransactions= ref(0)
const totalDiferenceTransactions= ref(0)
const totalDebitAmountTransactions= ref(0)
const totalCreditAmountTransactions= ref(0)
const chartData = ref({
    datasets: [],
})
const chartDataT = ref({
    datasets: [],
})
const pieChartDataD = ref({
    datasets: [],
})
const pieChartDataC = ref({
    datasets: [],
})
const periodClick = (period) => {
    setChartData(period)
}
const periodClickT = (period) => {
    setChartDataT(period)
}
const periodClickPie = (period) => {
    setPieChartData(period)
}
const setChartData = async (period) => {
    lastXDays.value = period 
    await loadChartData()
}
const setChartDataT = async (period) => {
    lastXDaysT.value = period 
    await loadChartDataT()
}
const setPieChartData = async (period) => {
    lastXDaysPie.value = period 
    await loadPieChartDataD()
    await loadPieChartDataC()
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
    transactions.value = []
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

    //Calcular
    transactions.value = response.data.data
    totalTransactions.value = transactions.value.length
    totalDebitTransactions.value = 0
    totalCreditTransactions.value = 0
    totalDebitAmountTransactions.value = 0
    totalCreditAmountTransactions.value = 0
    totalDiferenceTransactions.value = 0


    for(let i = 0; i < transactions.value.length; i++) {
        if (transactions.value[i] < 0) {
            totalDebitTransactions.value += 1
            totalDebitAmountTransactions.value += parseFloat(transactions.value[i])
        } else {
            totalCreditTransactions.value += 1
            totalCreditAmountTransactions.value += parseFloat(transactions.value[i])
        }
    }
    totalDiferenceTransactions.value = (totalCreditAmountTransactions.value + totalDebitAmountTransactions.value).toFixed(2)
    totalDebitAmountTransactions.value = (totalDebitAmountTransactions.value).toFixed(2)
    totalCreditAmountTransactions.value = (totalCreditAmountTransactions.value).toFixed(2)
    
}

const loadPieChartDataD = async () => {
    const response = await axios.get(`vcards/${authStore.user.username}/statistics/transactionscategories?range=${lastXDaysPie.value}&type=D`)
    const newPieChartData = {
        labels: ['VueJs', 'EmberJs', 'ReactJs', 'AngularJs'],//categories
        datasets: [
            {
                backgroundColor: [
                    '#e6194b', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#46f0f0', '#f032e6', 
                    '#bcf60c', '#fabebe', '#008080', '#e6beff', '#9a6324', '#00D8FF', '#800000', '#aaffc3', 
                    '#808000', '#ffd8b1', '#000075', '#808080', '#E46651', '#41B883'
                            ],
            data: [] //ammounts
            }
        ]
        };
        newPieChartData.labels = response.data.labels
        newPieChartData.datasets[0].data = response.data.data
        pieChartDataD.value = newPieChartData
        
}
const loadPieChartDataC = async () => {
    const response = await axios.get(`vcards/${authStore.user.username}/statistics/transactionscategories?range=${lastXDaysPie.value}&type=C`)
    const newPieChartData = {
        labels: ['VueJs', 'EmberJs', 'ReactJs', 'AngularJs'],//categories
        datasets: [
            {
                backgroundColor: [
                    '#e6194b', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#46f0f0', '#f032e6', 
                    '#bcf60c', '#fabebe', '#008080', '#e6beff', '#9a6324', '#00D8FF', '#800000', '#aaffc3', 
                    '#808000', '#ffd8b1', '#000075', '#808080', '#E46651', '#41B883'
                            ],
            data: [] //ammounts
            }
        ]
        };
        newPieChartData.labels = response.data.labels
        newPieChartData.datasets[0].data = response.data.data
        pieChartDataC.value = newPieChartData
        
}
const pieChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
};


onMounted(() => {
    loadChartData()
    loadChartDataT()
    loadPieChartDataD()
    loadPieChartDataC()
})

</script>
<template>
    <br>

    <br>
    <div class="d-flex justify-content-between">
        <div class="mx-2">
          <h4>Balance Fluctuation</h4>
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
          <h4>Last Transactions</h4>
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
                  <p>Total Transactions: {{ totalTransactions }}</p>
                  <p>Total Debit Transactions: {{ totalDebitTransactions }}</p>
                  <p>Total Credit Transactions: {{ totalCreditTransactions }}</p>
                </div>
              </div>
        
              <!-- Repeated block on the side -->
              <div class="mx-2 mt-2">
                <div>
                  <!-- <p style="opacity: 0;"> . </p> -->
                  <p>Total Diference: {{ totalDiferenceTransactions }}</p>
                  <p>Total Debit Amount: {{ totalDebitAmountTransactions }}</p>
                  <p>Total Credit Amount: {{ totalCreditAmountTransactions }}</p>
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
      <hr>
      <br>
      <div class="d-flex justify-content-between">
        <div class="mx-2">
            <p></p>
          <h4>Ammount sent/received by Category</h4>
        </div>
        <div class="mx-2">
        <p>Time frame: last {{ lastXDaysPie }} 
            {{ lastXDaysPie === 'year' ? '' : (lastXDaysPie === 'all' ? ' time' : ' days') }}</p>
        </div>
    </div>
      <!-- <div class="row">
        <div class="col-md-6"> -->
          <br>
          <br>
          <Doughnut :data="pieChartDataD" :options="pieChartOptions" />
        <!-- </div>
        <div class="col-md-6"> -->
          <br>
          <br>
          <Doughnut :data="pieChartDataC" :options="pieChartOptions" />
        <!-- </div>
      </div> -->
      <div class="mx-2">
          <button class="btn btn-xs btn-outline-secondary" @click="periodClickPie('30')">30</button>
          <button class="btn btn-xs btn-outline-secondary" @click="periodClickPie('60')">60</button>
          <button class="btn btn-xs btn-outline-secondary" @click="periodClickPie('year')">Year</button>
          <button class="btn btn-xs btn-outline-secondary" @click="periodClickPie('all')">All</button>
        </div>
        <br>
        <br>
        <br>
        <br>
</template>


<style scoped>
button {
    margin: 3px;
}
h4 {
    color: darkgreen;
}
p {
    font-size: 17px;
    padding-left: 30px;
    font-weight: 600;
}
.chart-container {
  display: flex;
  justify-content: space-between;
}
</style>