<script setup>
import axios from 'axios'
import { ref, onMounted, watch } from 'vue'
import { Line } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement } from 'chart.js'
import { useAuthStore } from '../../../stores/auth';
import { useTransactionStore } from '../../../stores/transaction';


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
const lastXDaysT = ref('60') 
const paymentType = ref(null)  //available options: 30days, 60days, year, all //Ref para ser reativa
const type = ref(null) 
const numberOfTransactions = ref([])
const totalTransactions= ref(0)
const totalDebitTransactions= ref(0)
const totalCreditTransactions= ref(0)
const totalDiferenceTransactions= ref(0)
const totalDebitAmountTransactions= ref(0)
const totalCreditAmountTransactions= ref(0)
const totalNumberOfTransactions= ref(0)
const transactionStore = useTransactionStore()
const chartData = ref({
    datasets: [],
})
const chartDataT = ref({
    datasets: [],
})


watch(paymentType, (newValue, oldValue) => {
  //console.log(`Payment Type changed from ${oldValue} to ${newValue}`);
  loadChartDataT()
});
watch(lastXDaysT, (newValue, oldValue) => {
    loadChartDataT()
  //console.log(`Last X Days changed from ${oldValue} to ${newValue}`);
});
watch(type, () => {
    loadChartDataT()
});

 const periodClick = (period) => {
     setChartData(period)
 }
const setPeriodT = (period) => {
    //console.log(period)
    lastXDaysT.value = period
}


 const setChartData = async (period) => {
     lastXDays.value = period 
     await loadChartData()
 }
// const setChartDataT = async (period, type) => {
//     lastXDaysT.value = period 
//     paymentType.value = type
//     await loadChartDataT()
// }
const loadChartData = async () => {
    const response = await axios.get(`vcards/statistics?range=${lastXDays.value}`)
    const newChartData = {
        labels: [],
        datasets: [
            {
                label: `Balance`,//`Last ${ lastXDays.value } days balance updates`,
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
    //console.log(lastXDaysT.value + ' ' + paymentType.value)

    const params = {}

    if (lastXDaysT.value)
    {
        params.range = lastXDaysT.value
    }

    if (paymentType.value){
        params.payment_type = paymentType.value
    }
    if (type.value){
        params.type = type.value
    }

    const response = await axios.get(`transactions/statistics`, { params })
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
})

</script>
<template>
    <br>

    <br>
    <div class="d-flex justify-content-between">
        <div class="mx-2">
          <h4>VCards Global Balance Fluctuation</h4>
        </div>

        <div class="mx-2">
        <p>Time frame: last {{ lastXDays }} 
            {{ lastXDays === 'year' ? '' : (lastXDays === 'all' ? ' time' : ' days') }}</p>
        </div>
      </div>
      <div class="mx-2 mt-2">
        <div class="d-flex flex-column flex-md-row">
          <!-- Original block -->
          <div class="mx-2 mt-2">
              <p>Active VCards:</p>
            </div>
        </div>
    <br>
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
            <div class="d-flex flex-column flex-md-row">
              <!-- Original block -->
              <div class="mx-2 mt-2">
                  <p>Total Number Of Transactions: {{ totalNumberOfTransactions }}</p>
                </div>
            </div>
        <br>
    </div>
    <Line :data="chartDataT" :options="chartOptionsT" />
    <br>
    <div class="mx-2 d-flex align-items-center" id="typeDropdown">
        <div>
          <button class="btn btn-xs btn-outline-dark" @click="setPeriodT('30')">30</button>
          <button class="btn btn-xs btn-outline-dark" @click="setPeriodT('60')">60</button>
          <button class="btn btn-xs btn-outline-dark" @click="setPeriodT('year')">Year</button>
          <button class="btn btn-xs btn-outline-dark" @click="setPeriodT('all')">All</button>
        </div>
    
        <div class="d-flex flex-column flex-md-row">
          <div class="align-items-center">
            <select id="inputPaymentType" style="font-size: 14px;" v-model="paymentType" class="form-select">
              <option v-for="paymentTypeB in transactionStore.paymentTypes" :key="paymentTypeB.value" :value="paymentTypeB.value">
                {{ paymentTypeB.text }}
              </option>
            </select>
          </div>
    
          <div class="align-items-center">
            <select id="inputType" style="font-size: 14px;" v-model="type" class="form-select">
              <option v-for="typeB in transactionStore.types" :key="typeB.value" :value="typeB.value">
                {{ typeB.text }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </template>
    
    <style scoped>
      button,
      select,
      .reset-button {
        height: 38px;
        vertical-align: middle;
        margin-right: 5px;
      }
    
      h4 {
        color: darkgreen;
      }
    
      p {
        font-size: 17px;
        padding-left: 30px;
        font-weight: 600;
      }
    
      #inputPaymentType {
        margin-right: 5px;
        margin-left: 30px;
      }
    
      #inputType {
        margin-left: 5px;
        margin-right: 5px;
      }
    
      @media (min-width: 768px) {
        #inputType {
          margin-left: 70px;
          margin-right: 5px;
        }
      }
    </style>