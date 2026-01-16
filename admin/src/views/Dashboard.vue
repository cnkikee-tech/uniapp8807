<template>
  <div class="dashboard">
    <el-row :gutter="20" class="stat-row">
      <el-col :span="6">
        <el-card class="stat-card">
          <div class="stat-content">
            <div class="stat-icon blue">
              <el-icon size="32"><CreditCard /></el-icon>
            </div>
            <div class="stat-info">
              <div class="stat-number">{{ statistics.totalCards }}</div>
              <div class="stat-label">总名片数</div>
            </div>
          </div>
        </el-card>
      </el-col>
      
      <el-col :span="6">
        <el-card class="stat-card">
          <div class="stat-content">
            <div class="stat-icon green">
              <el-icon size="32"><User /></el-icon>
            </div>
            <div class="stat-info">
              <div class="stat-number">{{ statistics.totalUsers }}</div>
              <div class="stat-label">总用户数</div>
            </div>
          </div>
        </el-card>
      </el-col>
      
      <el-col :span="6">
        <el-card class="stat-card">
          <div class="stat-content">
            <div class="stat-icon orange">
              <el-icon size="32"><TrendCharts /></el-icon>
            </div>
            <div class="stat-info">
              <div class="stat-number">{{ statistics.todayCards }}</div>
              <div class="stat-label">今日新增</div>
            </div>
          </div>
        </el-card>
      </el-col>
      
      <el-col :span="6">
        <el-card class="stat-card">
          <div class="stat-content">
            <div class="stat-icon purple">
              <el-icon size="32"><View /></el-icon>
            </div>
            <div class="stat-info">
              <div class="stat-number">{{ statistics.totalViews }}</div>
              <div class="stat-label">总浏览量</div>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>
    
    <el-row :gutter="20" class="chart-row">
      <el-col :span="12">
        <el-card title="名片增长趋势">
          <template #header>
            <div class="card-header">
              <span>名片增长趋势</span>
              <el-date-picker
                v-model="dateRange"
                type="daterange"
                range-separator="至"
                start-placeholder="开始日期"
                end-placeholder="结束日期"
                size="small"
                @change="loadChartData"
              />
            </div>
          </template>
          <div ref="chartRef" class="chart-container" style="height: 300px;"></div>
        </el-card>
      </el-col>
      
      <el-col :span="12">
        <el-card title="最新名片">
          <template #header>
            <span>最新名片</span>
          </template>
          <el-table :data="recentCards" style="width: 100%">
            <el-table-column prop="name" label="姓名" width="120" />
            <el-table-column prop="position" label="职位" width="150" />
            <el-table-column prop="company" label="公司" />
            <el-table-column prop="created_at" label="创建时间" width="160">
              <template #default="{ row }">
                {{ formatDate(row.created_at) }}
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import * as echarts from 'echarts'
import { CreditCard, User, TrendCharts, View } from '@element-plus/icons-vue'
import { getStatistics, getChartData, getRecentCards } from '@/api/dashboard'

const statistics = reactive({
  totalCards: 0,
  totalUsers: 0,
  todayCards: 0,
  totalViews: 0
})

const recentCards = ref([])
const dateRange = ref([])
const chartRef = ref()
let chartInstance = null

const loadStatistics = async () => {
  try {
    const res = await getStatistics()
    Object.assign(statistics, res.data)
  } catch (error) {
    console.error('加载统计数据失败:', error)
  }
}

const loadRecentCards = async () => {
  try {
    const res = await getRecentCards()
    recentCards.value = res.data
  } catch (error) {
    console.error('加载最新名片失败:', error)
  }
}

const loadChartData = async () => {
  try {
    const params = {}
    if (dateRange.value && dateRange.value.length === 2) {
      params.start_date = dateRange.value[0]
      params.end_date = dateRange.value[1]
    }
    
    const res = await getChartData(params)
    const data = res.data
    
    if (chartInstance) {
      chartInstance.setOption({
        xAxis: {
          data: data.dates
        },
        series: [{
          data: data.numbers
        }]
      })
    }
  } catch (error) {
    console.error('加载图表数据失败:', error)
  }
}

const initChart = () => {
  chartInstance = echarts.init(chartRef.value)
  
  const option = {
    tooltip: {
      trigger: 'axis',
      axisPointer: {
        type: 'line'
      }
    },
    grid: {
      left: '3%',
      right: '4%',
      bottom: '3%',
      containLabel: true
    },
    xAxis: {
      type: 'category',
      boundaryGap: false,
      data: []
    },
    yAxis: {
      type: 'value',
      name: '名片数量'
    },
    series: [{
      name: '名片数',
      type: 'line',
      smooth: true,
      symbol: 'circle',
      symbolSize: 8,
      itemStyle: {
        color: '#409EFF'
      },
      areaStyle: {
        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
          { offset: 0, color: 'rgba(64, 158, 255, 0.3)' },
          { offset: 1, color: 'rgba(64, 158, 255, 0.1)' }
        ])
      },
      data: []
    }]
  }
  
  chartInstance.setOption(option)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('zh-CN')
}

onMounted(async () => {
  await Promise.all([
    loadStatistics(),
    loadRecentCards()
  ])
  
  initChart()
  await loadChartData()
  
  // 响应式处理
  window.addEventListener('resize', () => {
    if (chartInstance) {
      chartInstance.resize()
    }
  })
})
</script>

<style scoped>
.dashboard {
  padding: 0;
}

.stat-row {
  margin-bottom: 20px;
}

.stat-card {
  border-radius: 8px;
  box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
}

.stat-content {
  display: flex;
  align-items: center;
  padding: 20px;
}

.stat-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 20px;
}

.stat-icon.blue {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.stat-icon.green {
  background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  color: white;
}

.stat-icon.orange {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
}

.stat-icon.purple {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
}

.stat-info {
  flex: 1;
}

.stat-number {
  font-size: 28px;
  font-weight: bold;
  color: #303133;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 14px;
  color: #909399;
}

.chart-row {
  margin-top: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chart-container {
  width: 100%;
  height: 300px;
}
</style>