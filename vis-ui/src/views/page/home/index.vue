<template>
    <div style="height:100%;">
        <el-row :gutter="20">
            <el-col :span="8" class="chart-container">
                <bar-chart
                    :type="0"
                    :title="workYearTitle"
                    :chart-data="workYearData"></bar-chart>
            </el-col>
            <el-col :span="9" class="chart-container">
                <map-chart
                    :cities="citiesData"
                    @change="handleChange"></map-chart>
            </el-col>
            <el-col :span="6" class="chart-container">
                <pie-chart
                    :hot-position-data="hotPositionData"></pie-chart>
            </el-col>
        </el-row>
        <el-row :gutter="20">
            <el-col :span="8" class="chart-container">
                <bar-chart
                    :type="1"
                    :title="salaryTitle"
                    :chart-data="salaryData"
                ></bar-chart>
            </el-col>
            <el-col :span="9" class="chart-container">
                <education-chart
                    :chart-data="educationData"></education-chart>
            </el-col>
            <el-col :span="6" class="chart-container">
                <position-salary
                    :chart-data="positionSalaryData"
                ></position-salary>
            </el-col>
        </el-row>
    </div>
</template>

<script>
import { http } from '@/utils/request';
import MapChart from './components/map';
import BarChart from './components/BarChart';
import PieChart from './components/PieChart';
import EducationChart from './components/educationChart'
import PositionSalary from './components/positionSalary';

export default {
    name: 'Index',
    data() {
        return {
            http,
            // loading: true,
            currentCity: '',
            workYearTitle: '工作经验分布情况',
            salaryTitle: '薪酬分布',
            educationData: {},
            hotPositionData: [],
            citiesData: [],
            salaryData: [],
            workYearData: [],
            positionSalaryData: []
        };
    },
    components: {
        MapChart,
        BarChart,
        PieChart,
        EducationChart,
        PositionSalary
    },
    // computed: {
    //     loading() {
    //         return this.loadingWorkYear && loadingPositionSalary;
    //     }
    // },
    created() {
        this.getHotPosition();
        this.getEducation();
        this.getWorkYear();
        this.getSalary();
        this.getCity();
        this.getPositionSalary();
    },
    methods: {
        handleChange(currentCity) {
            this.currentCity = currentCity;
            this.getPositionSalary();
            this.getHotPosition();
            this.getWorkYear();
            this.getSalary();
        },
        getPositionSalary() {
            this.http({
                url: '/vis/positionSalary?city=' + this.currentCity,
                method: 'get',
            })
                .then(( { data } ) => {
                    if (data.code !== 200) {
                        return;
                    }
                    this.positionSalaryData = data.data;
                })
                .finally(() => {
                });
        },
        getHotPosition() {
            this.http({
                url: '/vis/hotPosition?city=' + this.currentCity,
                method: 'get'
            })
                .then(( { data } )=> {
                    if (data.code !== 200) {
                        return;
                    }
                    this.hotPositionData = data.data;
                })
                .finally(() => {
                });
        },
        getEducation() {
            this.http({
                url: '/vis/education',
                method: 'get'
            })
                .then(( { data } )=> {
                    if (data.code !== 200) {
                        return;
                    }
                    this.educationData = data;
                })
                .finally(() => {
                });
        },
        getWorkYear() {
            // type = 0工作年限 type= 1 薪资
            this.http({
                url: `vis/workYear?type=0&city=${this.currentCity}`,
                method: 'get'
            })
                .then(( { data } )=> {
                    if (data.code !== 200) {
                        return;
                    }
                    this.workYearData = data.data;
                })
                .finally(() => {
                });
        },
        getSalary() {
            // this.loading = true;
            // type = 0工作年限 type= 1 薪资
            this.http({
                url: `vis/workYear?type=1&city=${this.currentCity}`,
                method: 'get'
            })
                .then(( { data } )=> {
                    if (data.code !== 200) {
                        return;
                    }
                    this.salaryData = data.data;
                })
                .finally(() => {
                });
        },
        getCity() {
            this.http({
                url: '/vis/city',
                method: 'get'
            })
                .then(( { data } ) => {
                    if (data.code !== 200) {
                        return;
                    }
                    this.citiesData = data.data;
                })
                .finally(() => {
                });
        }
    }
};
</script>


<style scoped>
    .el-row {
        margin-bottom: 20px;
    }

    .grid-content {
        display: flex;
        align-items: center;
        height: 100px;
    }

    .schart {
        width: 100%;
        height: 300px;
    }
    .chart-container {
        margin: 5px;
        background: #011A51;
        border: 2px solid #082265;
        border-radius: 5px;
        box-shadow:5px 5px 5px #082265;
    }
</style>
