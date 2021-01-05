<template>
  <div :class="className" :style="{height:height,width:width}" />
</template>

<script>
import _ from 'lodash';
import echarts from 'echarts'
require('echarts/theme/macarons') // echarts theme
import resize from './mixins/resize'

export default {
  name: 'EducationChart',
  mixins: [resize],
  props: {
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
        return {};
      }
    }
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
    initChart() {
      this.chart = echarts.init(this.$el, 'macarons')
      let seriesData = [];
      let legendData = [];
      let xData = [];
      seriesData = this.chartData.map(item => {
        return item.meanSalary.replace('k', '') * 1000;
      });
      xData = this.chartData.map(item => {
        return item.PositionName;
      });
      // let xData =  this.chartData.extra;
      // let legendData = _.keys(this.chartData.data);
      // for (const key in this.chartData.data) {
      //     seriesData.push({
      //       type: 'bar',
      //       name: key,
      //       data: this.chartData.data[key]
      //     });
      // }
      this.chart.setOption({
        title: {
          text: '各行业平均薪资水平',
          left: 'center',
          padding: [10, 10],
          textStyle: {
              color: '#008ACD',
              fontWeight: 'bold'
          }
        },
        color: ['#08539B', '#27D08A', '#4C60FF'],
        tooltip: {
          trigger: 'axis',
          axisPointer: { // 坐标轴指示器，坐标轴触发有效
            type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
          }
        },
        grid: {
          top: 50,
          left: '2%',
          right: '2%',
          bottom: '3%',
          containLabel: true
        },
        legend: {
          show: false,
          orient: 'vertical',
          left: 'right',
          top: 'bottom',
          data: legendData,
          textStyle: { //图例文字的样式
            color: '#fff',
            fontSize: 12
          }
        },
        xAxis: [{
          type: 'category',
          nameLocation: 'start',
          data: xData,
          boundaryGap: false,
          axisLabel: {
            interval: 0,
            rotate: 45
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
        series: {
          type: 'line',
          data: seriesData,
          itemStyle: {
            normal: {
              label: {
                show: true,
                formatter(params) {
                  return params.data / 1000 + 'k';
                },
                position: 'top',
                textStyle: {
                  color: '#37BBF8',
                  fontSize: 12
                }
              }
            }
          }
        }
      })
    }
  }
}
</script>

<style scoped>
.chart {
    /* margin: 20px; */
}
</style>