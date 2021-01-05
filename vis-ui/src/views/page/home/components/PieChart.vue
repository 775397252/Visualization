<template>
  <div :class="className" :style="{height:height,width:width}" />
</template>

<script>
import echarts from 'echarts'
require('echarts/theme/macarons') // echarts theme
import resize from './mixins/resize'

export default {
  name: 'PieChart',
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
    hotPositionData: {
      type: Array,
      default() {
        return [];
      }
    }
  },
  data() {
    return {
      chart: null
    }
  },
  watch: {
    hotPositionData: {
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
      const seriesData = this.hotPositionData.map(item => {
        return {
          name: item.PositionName,
          value: item.count
        };
      });
      const legendData = this.hotPositionData.map(item => {
        return item.PositionName;
      })
      this.chart.setOption({
        title: {
            text: '热门职业及职位数量',
            left: 'center',
            padding: [10, 10],
            textStyle: {
                color: '#008ACD',
                fontWeight: 'bold'
            }
        },
        color: ['#08F5C4','#E7A135', '#66A125', '#D95478', '#588BD9', '#AAC7F8', '#6A69EB', '#3D31DA', '#8751A6', '#DBC5B4'],
        tooltip: {
          trigger: 'item',
          formatter: '{a} <br/>{b} : {c} ({d}%)'
        },
        legend: {
          show: false,
          orient: 'vertical',
          x: 'right',
          y: '30px',
          icon: 'circle',
          data: legendData,
          textStyle: { //图例文字的样式
            color: '#fff',
            fontSize: 12
          }
        },
        series: [
          {
            name: '热门职业',
            type: 'pie',
            roseType: 'radius',
            radius: [15, 95],
            center: ['50%', '50%'],
            data: seriesData,
            animationEasing: 'cubicInOut',
            animationDuration: 2600
          }
        ]
      })
    }
  }
}
</script>
