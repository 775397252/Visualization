<template>
  <div :class="className" :style="{height:height,width:width}" />
</template>

<script>
import _ from 'lodash';
import echarts from 'echarts'
require('echarts/theme/macarons') // echarts theme
import resize from './mixins/resize'

const animationDuration = 6000

export default {
  name: 'BarChart',
  mixins: [resize],
  props: {
    title: String,
    className: {
      type: String,
      default: 'chart'
    },
    width: {
      type: String,
      default: '100%'
    },
    height: {
      type: String,
      default: '320px'
    },
    chartData: {
      type: Array,
      default() {
        return [];
      }
    },
    type: Number
    // 0 表示工作年限 1表示薪水
  },
  data() {
    return {
      chart: null
    }
  },
  watch: {
    chartData: {
      deep: true,
      handler() {
        this.initChart();
      }
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.initChart()
    })
  },
  beforeDestroy() {
    if (!this.chart) {
      return
    }
    this.chart.dispose()
    this.chart = null
  },
  methods: {
    unique(arr) { // 根据唯一标识orderId来对数组进行过滤
 　　　　　　const res = new Map();  //定义常量 res,值为一个Map对象实例
　　　　　　//返回arr数组过滤后的结果，结果为一个数组   过滤条件是，如果res中没有某个键，就设置这个键的值为1
　　　　　　return arr.filter((arr) => !res.has(arr.OrderId) && res.set(arr.OrderId, 1)) 
    },
    initChart() {
      this.chart = echarts.init(this.$el, 'macarons')
      let seriesData = [];
      let xData = [];
      let legendData = [];
      seriesData = this.chartData.map(item => item.count);
      if (!this.type) {
        xData = this.chartData.map(item => item.WorkYear);
        legendData = this.chartData.map(item => item.WorkYear);
      } else {
        xData = this.chartData.map(item => item.Salary);
        legendData = this.chartData.map(item => item.Salary);
      }
      this.chart.setOption({
        title: {
          text: this.title,
          left: 'center',
          padding: [10, 10],
          textStyle: {
              color: '#008ACD',
              fontWeight: 'bold'
          }
        },
        color: ['#2F89CF'],
        tooltip: {
          trigger: 'axis',
          axisPointer: { // 坐标轴指示器，坐标轴触发有效
            type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
          },
          formatter: '{b} <br /> 数目 : {c}'
        },
        grid: {
          top: 50,
          left: '2%',
          right: '2%',
          bottom: '3%',
          containLabel: true
        },
        xAxis: [{
          type: 'category',
          nameLocation: 'start',
          data: xData,
          axisLabel: {
            interval: 0,
            rotate: -45
          },
          axisTick: {
            alignWithLabel: true
          },
          splitLine: {
            show: true,
            lineStyle: {
              type: 'dashed',
              color: '#1C2E78'
            }
          }
        }],
        yAxis: [{
          type: 'value',
          name: '人数',
          axisTick: {
            show: false
          },
          splitLine: {
            show: true,
            lineStyle: {
              type: 'dashed',
              color: '#1C2E78'
            }
          }
        }],
        legend: {
          show: false,
          data: legendData
        },
        series: {
          type: 'bar',
          data: seriesData,
          barWidth: '25px',
          itemStyle: {
              emphasis: {
                  barBorderRadius: 2
              },
              normal: {
                  label: {
                    show: true,
                    position: 'top',
                    textStyle: {
                      color: '#37BBF8',
                      fontSize: 12
                    }
                  },
                  barBorderRadius: 2,
                    color: new echarts.graphic.LinearGradient(
                        0, 1, 0, 0,
                        [
                            {offset: 0, color: '#37BBF8'},
                            {offset: 1, color: '#3977E6'}
                        ]
                    )
              }
          }
        }
      })
    }
  }
}
</script>
