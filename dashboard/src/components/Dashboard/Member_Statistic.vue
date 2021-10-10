<template>
    <div>
        <vcl-table v-if="!isLoaded" :rows="3" :columns="3"></vcl-table>
        <div v-else class="" uk-grid>
            <div class="uk-width-1-2@m">
                <div class="uk-card uk-card-default uk-margin-top" align="center">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex" uk-grid>
                            <div class="uk-width-auto uk-text-cario"> {{total_data.name}} </div>
                            <div class="uk-width-expand uk-text-right uk-text-cario"> {{total_data.value}} </div>
                        </div>
                        <div class="uk-grid-small uk-flex" uk-grid>
                            <div class="uk-width-auto uk-text-cario"> Deliver Fee </div>
                            <div class="uk-width-expand uk-text-right uk-text-cario"> {{total_data.total_df}} IQD</div>
                        </div>
                        <div class="uk-grid-small uk-flex" uk-grid>
                            <div class="uk-width-auto uk-text-cario"> Items </div>
                            <div class="uk-width-expand uk-text-right uk-text-cario"> {{total_data.total_items}} IQD</div>
                        </div>
                        <div class="uk-grid-small uk-flex" uk-grid>
                            <div class="uk-width-auto uk-text-cario"> Invoice </div>
                            <div class="uk-width-expand uk-text-right uk-text-cario"> {{total_data.total_inv}} IQD </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-width-1-2@m">
                <v-chart class="chart" :option="option" />
            </div>
            <div class="uk-width-1-1@m">
                <div class="uk-child-width-1-4@m" uk-grid>
                    <div v-for="stat in option.series[0].data" v-bind:key="stat.id"> 
                        <div class="uk-card uk-card-default" align="center">
                            <div style="padding: 10px;" class="uk-card-header">
                                <div class="uk-grid-small uk-flex" uk-grid>
                                    <div class="uk-width-auto uk-text-cario"> {{stat.name}} </div>
                                    <div class="uk-width-expand uk-text-right uk-text-cario"> {{stat.value}} </div>
                                </div>
                                <div class="uk-grid-small uk-flex" uk-grid>
                                    <div class="uk-width-auto uk-text-cario"> Deliver Fee </div>
                                    <div class="uk-width-expand uk-text-right uk-text-cario"> {{stat.total_df}} IQD</div>
                                </div>
                                <div class="uk-grid-small uk-flex" uk-grid>
                                    <div class="uk-width-auto uk-text-cario"> Items </div>
                                    <div class="uk-width-expand uk-text-right uk-text-cario"> {{stat.total_items}} IQD</div>
                                </div>
                                <div class="uk-grid-small uk-flex" uk-grid>
                                    <div class="uk-width-auto uk-text-cario"> Invoice </div>
                                    <div class="uk-width-expand uk-text-right uk-text-cario"> {{stat.total_inv}} IQD </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</template>

<script>

    import { use } from "echarts/core";
    import { CanvasRenderer } from "echarts/renderers";
    import { PieChart } from "echarts/charts";
    import { TitleComponent, TooltipComponent, LegendComponent } from "echarts/components";
    import VChart, { THEME_KEY } from "vue-echarts";
    
    use([
        CanvasRenderer,
        PieChart,
        TitleComponent,
        TooltipComponent,
        LegendComponent
    ]);
    
    export default {

        props: ['FilterOptions'], 

        components: {
            VChart,
        },

        provide: {
            [THEME_KEY]: "caravan"
        },

        data() {
            return {
                // option : {
                //     title: {
                //         text: "Traffic Sources",
                //         left: "center"
                //     },
                //     tooltip: {
                //         trigger: "item",
                //         formatter: "{a} <br/>{b} : {c} ({d}%)"
                //     },
                //     legend: {
                //         orient: "vertical",
                //         left: "left",
                //         data: ["Direct", "Email", "Ad Networks", "Video Ads", "Search Engines"]
                //     },
                //     series: [
                //         {
                //         name: "Traffic Sources",
                //         type: "pie",
                //         radius: "55%",
                //         center: ["50%", "60%"],
                //         data: [
                //             { value: 335, name: "Direct" },
                //             { value: 310, name: "Email" },
                //             { value: 234, name: "Ad Networks" },
                //             { value: 135, name: "Video Ads" },
                //             { value: 1548, name: "Search Engines" }
                //         ],
                //         emphasis: {
                //             itemStyle: {
                //             shadowBlur: 10,
                //             shadowOffsetX: 0,
                //             shadowColor: "rgba(0, 0, 0, 0.5)"
                //             }
                //         }
                //         }
                //     ]
                // },
                option: {
                    title: {
                        // text: "Traffic Sources",
                        // left: "center"
                    },
                    tooltip: {
                        trigger: "item",
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        orient: "vertical",
                        left: "left",
                        data: []
                    },
                    series: [
                    {
                        // name: "Traffic Sources",
                        type: "pie",
                        radius: "80%",
                        center: ["50%", "50%"],
                        data: [],
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: "rgba(0, 0, 0, 0.5)"
                            }
                        }
                    }
                    ]
                },
                miniload: false,
                isLoaded: false,
                total_data: [],
            };
        },

        watch: {
            FilterOptions: function() {
                this.Fetch();
            } 
        },  

        created(){
            this.Fetch();
        },

        methods: { 
            Fetch(){
                this.isLoaded = false;
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/Statistic/${this.FilterOptions.Account_Role_Number}/${this.FilterOptions.Account_Id}/${this.FilterOptions.dateFrom}/${this.FilterOptions.dateTo}/${this.FilterOptions.FromState}/${this.FilterOptions.ToState}`)
                .then(res => {
                    this.total_data = res.data[0];
                    this.option.series[0].data = res.data[1];
                    this.isLoaded = true;
                })
                .catch(res => {
                    this.$toasted.show("Error 500 | ", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.isLoaded = true;
                });
            },
  
        }, 

        
    };
</script>

<style scoped>
.customer-view {
    display: flex;
    align-items: center;
}
.user-img {
    flex: 1;
}
.user-img img {
    max-width: 160px;
}
.user-info {
    flex: 3;
    overflow-x: scroll;
}
.uk-navbar-nav li {margin: 0px 5px;}
.chart {
  height: 350px;
  width: 100%;
}
</style>
