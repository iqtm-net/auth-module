<template>
        
        <div>
            <br><br>
            <vcl-table v-if="!isLoaded" :rows="7" :columns="4"></vcl-table>
            <div v-else class="uk-margin"> 
                
                <div class="uk-child-width-1-4@m uk-margin" uk-grid uk-scrollspy="cls:  ">
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-taxi" style="font-size:3.1rem;color: rgb(255, 85, 147)"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.taxis}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Taxis</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-user" style="font-size:3.1rem;color: rgb(255, 85, 147)"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.users}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Users</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-store" style="font-size:3.1rem;color: rgb(93, 72, 175)"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.stores}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Stores</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fa fa-truck sidebar-icon" style="font-size:3.1rem;color:rgb(191, 194, 100);"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.delivers}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Delivers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                    
                    <!-- <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-user" style="font-size:3.1rem;color: black;"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.Orders_black_list}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">black list</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-dollar-sign" style="font-size:3.1rem;color: rgb(100, 194, 106);"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.WithdrawOrders}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top" style="margin: 5px 0px;">Total Withdraws</p>
                                        <button @click.prevent="DownloadWithdraw_balance()" style="padding: 3px 9px; width: 100%;" class="btn btn-outline-success"><span class="fas fa-download"></span></button>  
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    
                </div>

                <div class="uk-child-width-1-3@m" uk-grid uk-scrollspy="cls:  ">
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-dollar-sign" style="font-size:3.1rem;color: rgb(100, 194, 106);"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.Withdraw_balance}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top" style="margin: 5px 0px;">Withdrawn balance</p>
                                        <!-- <button @click.prevent="DownloadWithdraw_balance()" style="padding: 3px 9px; width: 100%;" class="btn btn-outline-success"><span class="fas fa-download"></span></button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-dollar-sign" style="font-size:3.1rem;color: rgb(100, 194, 106);"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.Users_Stores_balance}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top" style="margin: 5px 0px;">Unwithdrawn Balance</p>
                                        <!-- <button @click.prevent="DownloadUsers_Stores_balance()" style="padding: 3px 9px; width: 100%;" class="btn btn-outline-success"><span class="fas fa-download"></span></button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-dollar-sign" style="font-size:3.1rem;color: rgb(100, 194, 106);"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.Withdraw_balance + DataOutput.UnWithdraw_balance}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top" style="margin: 5px 0px;">Total</p>
                                        <!-- <button @click.prevent="DownloadWithdraw_and_unWithdrawen_balance()" style="padding: 3px 9px; width: 100%;" class="btn btn-outline-success"><span class="fas fa-download"></span></button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="uk-child-width-1-3@m" uk-grid uk-scrollspy="cls:  ">
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-taxi" style="font-size:3.1rem;color: rgb(255, 85, 147)"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.orders}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Delivery Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-user" style="font-size:3.1rem;color: rgb(255, 85, 147)"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.GiftOrders}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Gift Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-store" style="font-size:3.1rem;color: rgb(93, 72, 175)"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.TaxiRides}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Taxi Rides</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="uk-child-width-1-3@m" uk-grid uk-scrollspy="cls:  ">
                    <div class="CHART">
                        <chart style="overflow: auto;" class="highcharts uk-margin-card" :options="OrdersSubWeek"></chart>
                    </div>
                    <div class="CHART">
                        <chart class="highcharts uk-margin-card" :options="DeliveredItemsAmounts"></chart>
                    </div>
                    <div class="CHART">
                        <chart class="highcharts uk-margin-card" :options="DeliversFeesSubWeek"></chart>
                    </div>
                </div>

                <div class="uk-child-width-1-3@m" style="margin-top:0px;" uk-grid uk-scrollspy="cls:  ">
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-boxes sidebar-icon" style="font-size:3.1rem;color:#5ea3d5;"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.orders}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-file-invoice-dollar" style="font-size:3.1rem;color: rgb(100, 194, 106);"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.recieved_prices}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Sold Products</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-hand-holding-usd" style="font-size:3.1rem;color: rgb(189, 133, 218);"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.Deliver_Fee}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Delivers Fees</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="uk-divider-icon">
                <!-- <view-statistic :FilterOptions="{
                Account_Id:'All', 
                Account_Role_Number:'All', 
                dateFrom:'All', 
                dateTo:'All',  
                FromState:'All', 
                ToState:'All',  
                }"></view-statistic>  -->
                <hr class="uk-divider-icon">
                

                <!-- <div class="uk-child-width-1-3@m" uk-grid uk-scrollspy="cls:  ">

                    <div>
                        <div class="uk-card uk-card-default uk-margin-card">
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style=" justify-content: center; display: flex;">
                                        <router-link :to="`/ordersget/waiting`" class="StCls">
                                            <vue-circle :progress="WaitingOrdersAcceptedPercent" :size="70" :reverse="false" line-cap="round" :fill="fillwating" empty-fill="rgba(0, 0, 0, .1)" :animation-start-value="0.0" :start-angle="0" insert-mode="append" :thickness="10" :show-percent="false" @vue-circle-progress="progress" @vue-circle-end="progress_end">
                                                <i class="fas fa-clock" style="margin-right:0px;font-size:3.3rem;color: rgb(185, 185, 185);"></i>
                                            </vue-circle>
                                        </router-link>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.WaitingOrdersCountAccepted}} <font class="pt">({{WaitingOrdersAcceptedPercent}}%)</font></h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Accepted Waiting Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card">
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style=" justify-content: center; display: flex;">
                                        <router-link :to="`/ordersget/waiting_unaccepted`" class="StCls">
                                        <vue-circle :progress="WaitingOrdersNotAcceptedPercent" :size="70" :reverse="false" line-cap="round" :fill="fillwating" empty-fill="rgba(0, 0, 0, .1)" :animation-start-value="0.0" :start-angle="0" insert-mode="append" :thickness="10" :show-percent="false" @vue-circle-progress="progress" @vue-circle-end="progress_end">
                                            <i class="fas fa-clock" style="margin-right:0px;font-size:3.3rem;color: rgb(185, 185, 185);"></i>
                                        </vue-circle>
                                        </router-link>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.WaitingOrdersCountNotAccepted}} <font class="pt">({{WaitingOrdersNotAcceptedPercent}}%)</font></h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Unaccepted Waiting Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card">
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style=" justify-content: center; display: flex;">
                                        <router-link :to="`/ordersget/pending`" class="StCls">
                                        <vue-circle :progress="PendingOrdersPercent" :size="70" :reverse="false" line-cap="round" :fill="fillpending" empty-fill="rgba(0, 0, 0, .1)" :animation-start-value="0.0" :start-angle="0" insert-mode="append" :thickness="10" :show-percent="false" @vue-circle-progress="progress" @vue-circle-end="progress_end">
                                            <i class="fas fa-clock" style="margin-right:0px;font-size:3.3rem;color: rgb(222, 204, 73);"></i>
                                        </vue-circle>
                                        </router-link>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.PendingOrdersCount}} <font class="pt">({{PendingOrdersPercent}}%)</font></h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Pending Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-child-width-1-2@m" uk-grid uk-scrollspy="cls:  ">
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card">
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style=" justify-content: center; display: flex;">
                                        <router-link :to="`/ordersget/delivered`" class="StCls">
                                        <vue-circle :progress="DeliveredOrdersPercent" :size="70" :reverse="false" line-cap="round" :fill="filldelivered" empty-fill="rgba(0, 0, 0, .1)" :animation-start-value="0.0" :start-angle="0" insert-mode="append" :thickness="10" :show-percent="false" @vue-circle-progress="progress" @vue-circle-end="progress_end">
                                            <i class="fas fa-check-circle" style="margin-right:0px;font-size:3.3rem;color: rgb(60, 212, 107);"></i>
                                        </vue-circle>
                                        </router-link>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.DeliveredOrdersCount}} <font class="pt">({{DeliveredOrdersPercent}}%)</font></h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Delivered Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card">
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style=" justify-content: center; display: flex;">
                                        <router-link :to="`/ordersget/issued`" class="StCls">
                                        <vue-circle :progress="IssuedOrdersPercent" :size="70" :reverse="false" line-cap="round" :fill="fillissued" empty-fill="rgba(0, 0, 0, .1)" :animation-start-value="0.0" :start-angle="0" insert-mode="append" :thickness="10" :show-percent="false" @vue-circle-progress="progress" @vue-circle-end="progress_end">
                                        <i class="fas fa-times-circle" style="margin-right:0px;font-size:3.3rem;color: rgb(234, 82, 82);"></i>
                                        </vue-circle>
                                        </router-link>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.IssuedOrdersCount}} <font class="pt">({{IssuedOrdersPercent}}%)</font></h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Issued Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->


                <!-- <div class="uk-child-width-1-1@m" uk-grid uk-scrollspy="cls:  ">
                    <div>
                        <nav style="margin: 20px 0px;" class="uk-navbar-container" uk-navbar>
                            <div class="uk-navbar-left">
                                <ul class="uk-navbar-nav datepickerCs">
                                    <li><datepicker class="datepickerCsLi" placeholder="From" @selected="onChangeDateFrom($event)"></datepicker></li>
                                    <li><datepicker class="datepickerCsLi" placeholder="To" @selected="onChangeDateTo($event)"></datepicker></li>
                                </ul>
                            </div>
                        </nav>
                        <div class="uk-child-width-1-5@m" uk-grid uk-scrollspy="cls:  " v-if="ShowOrderByDate">
                            <div>
                                <div class="uk-card uk-card-default uk-margin-card" style="border-radius:10px;">
                                    <div class="uk-card-header" style=" padding: 1px 17px;">
                                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                                            <div class="uk-width-expand">
                                                <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.this.WaitingUnAcceptedByDate}}</h3>
                                                <p class="uk-text-meta uk-margin-remove-top">Waiting UnAccepted</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="uk-card uk-card-default uk-margin-card" style="border-radius:10px;">
                                    <div class="uk-card-header" style=" padding: 1px 17px;">
                                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                                            <div class="uk-width-expand">
                                                <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.this.WaitingAcceptedByDate}}</h3>
                                                <p class="uk-text-meta uk-margin-remove-top">Waiting Accepted</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="uk-card uk-card-default uk-margin-card" style="border-radius:10px;">
                                    <div class="uk-card-header" style=" padding: 1px 17px;">
                                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                                            <div class="uk-width-expand">
                                                <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.this.PendingByDate}}</h3>
                                                <p class="uk-text-meta uk-margin-remove-top">Pending</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="uk-card uk-card-default uk-margin-card" style="border-radius:10px;">
                                    <div class="uk-card-header" style=" padding: 1px 17px;">
                                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                                            <div class="uk-width-expand">
                                                <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.this.DeliveredByDate}}</h3>
                                                <p class="uk-text-meta uk-margin-remove-top">Delivered</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="uk-card uk-card-default uk-margin-card" style="border-radius:10px;">
                                    <div class="uk-card-header" style=" padding: 1px 17px;">
                                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                                            <div class="uk-width-expand">
                                                <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.this.IssuedByDate}}</h3>
                                                <p class="uk-text-meta uk-margin-remove-top">Issued</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-card uk-card-default uk-margin-card GCHART">
                            <GChart v-if="isLoaded" type="ColumnChart" :data="OrdersChartAllStatus" :options="chartOptions" />
                        </div>
                    </div>
                </div> -->

                <!--////////////////////////////////////////////////////////// Section 4 /////////////////////////////////////////////////////////////-->
                <div class="uk-child-width-1-3@m" uk-grid uk-scrollspy="cls:  ">
                    <!--/////////////////////////// TOP Deliver //////////////////////////////////////-->
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header" style="padding: 5px 30px;">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-star" style="font-size:3.1rem;color: rgb(222, 204, 73);"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">
                                            Top 5 Delivers
                                            <span class="fa fa-truck sidebar-icon" aria-hidden="true" style="font-size: 1.5rem; color: rgb(89, 89, 89);"></span>
                                        </h3>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-for="(order,index) in DataOutput.TopDelivers" :key="order.id" class="uk-card uk-card-default uk-margin-card">
                            <div class="uk-card-header" style="padding: 5px 30px;">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <span style="font-size:3.1rem;color: rgb(111, 218, 118);">#{{index+1}}</span>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">
                                            <router-link style="color: black;" :to="`/viewdelivers/${order.id}`">
                                                {{order.Deliver}}
                                            </router-link>
                                        </h3>
                                        <p style="font-size: 17px; color: #2075a7;" class="uk-text-meta uk-margin-remove-top">{{order.Total_Orders}} Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="justify-content-center" uk-grid align="center">
                            <button style="padding: 6px;margin: 9px;" @click.prevent="showPanelTopDelivers" class="btn btn-outline-info">show all</button>
                        </div>
                    </div>

                    <!--/////////////////////////// TOP Stores //////////////////////////////////////-->
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header" style="padding: 5px 30px;">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-star" style="font-size:3.1rem;color: rgb(222, 204, 73);"></i>

                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">
                                            Top 5 Stores
                                            <span class="fas fa-store" aria-hidden="true" style="font-size: 1.5rem; color: rgb(89, 89, 89);"></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-for="(order,index) in DataOutput.TopStores" :key="order.id" class="uk-card uk-card-default uk-margin-card">
                            <div class="uk-card-header" style="padding: 5px 30px;">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <span style="font-size:3.1rem;color: rgb(111, 218, 118);">#{{index+1}}</span>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">
                                            <router-link v-if="order.account_type == 2" style="color: black;" :to="`/viewusers/${order.id}`">
                                                {{order.Member}}
                                            </router-link>
                                            <router-link v-if="order.account_type == 3" style="color: black;" :to="`/viewstores/${order.id}`">
                                                {{order.Member}}
                                            </router-link>
                                        </h3>
                                        <p style="font-size: 17px; color: #2075a7;" class="uk-text-meta uk-margin-remove-top">{{order.Total_Orders}} Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="justify-content-center" uk-grid align="center">
                            <button style="padding: 6px;margin: 9px;" @click.prevent="showPanelTopMembers" class="btn btn-outline-info">show all</button>
                        </div>

                    </div>
                    <!--/////////////////////////// TOP Users //////////////////////////////////////-->
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header" style="padding: 5px 30px;">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fas fa-star" style="font-size:3.1rem;color: rgb(222, 204, 73);"></i>

                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">
                                            Top 5 Users
                                            <span class="fas fa-user" aria-hidden="true" style="font-size: 1.5rem; color: rgb(89, 89, 89);"></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-for="(order,index) in DataOutput.TopUsers" :key="order.id" class="uk-card uk-card-default uk-margin-card">
                            <div class="uk-card-header" style="padding: 5px 30px;">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <span style="font-size:3.1rem;color: rgb(111, 218, 118);">#{{index+1}}</span>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">
                                            <router-link v-if="order.account_type == 2" style="color: black;" :to="`/viewusers/${order.id}`">
                                                {{order.Member}}
                                            </router-link>
                                            <router-link v-if="order.account_type == 3" style="color: black;" :to="`/viewstores/${order.id}`">
                                                {{order.Member}}
                                            </router-link>
                                        </h3>
                                        <p style="font-size: 17px; color: #2075a7;" class="uk-text-meta uk-margin-remove-top">{{order.Total_Orders}} Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="justify-content-center" uk-grid align="center">
                            <button style="padding: 6px;margin: 9px;" @click.prevent="showPanelTopMembers" class="btn btn-outline-info">show all</button>
                        </div>

                    </div>

                    <!-- //////////////////////// Orders through States Statistic //////////////////////-->
                    <div>
                        <div class="uk-child-width-1-2@m" uk-grid uk-scrollspy="cls:  ">
                            <div>
                                <div class="uk-card uk-card-default uk-margin-card">
                                    <table border="0" class="table cust-table">
                                        <thead>
                                        <tr style="">
                                            <th style="width:200px;">Orders Sent From</th>
                                            <th style="width:200px;">Total</th>
                                        </tr>
                                        </thead>
                                        <tbody v-for="(order) in DataOutput.SendingStates" :key="order.id">
                                        <tr>
                                            <td style="width:200px;">{{order.location_from_state }}</td>
                                            <td style="width:200px;">{{order.Total_Orders }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div>
                                <div class="uk-card uk-card-default uk-margin-card">
                                    <table border="0" class="table cust-table">
                                        <thead>
                                        <tr style="">
                                            <th style="width:200px;">Orders Sent To</th>
                                            <th style="width:200px;">Total</th>
                                        </tr>
                                        </thead>
                                        <tbody v-for="(order) in DataOutput.ReceivingStates" :key="order.id">
                                        <tr>
                                            <td style="width:200px;">{{order.location_to_state }}</td>
                                            <td style="width:200px;">{{order.Total_Orders }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- //////////////////////// Last Reports //////////////////////-->
                    <div>
                        <div class="uk-card uk-card-default uk-margin-card" uk-grid>
                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                                        <i class="fa fa-file sidebar-icon" style="font-size:3.1rem;color:rgb(156, 156, 156);"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{DataOutput.CountReports}}</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">Reports</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-card uk-card-default uk-margin-card">
                            <table border="f0" class="table cust-table">
                                <thead>
                                <tr style="">
                                    <th style="width:200px;"><i class="fas fa-id-card"></i></th>
                                    <th style="width:200px;"><i class="fas fa-phone"></i></th>
                                    <th style="width:200px;"><i class="fas fa-truck"></i><i class="fas fa-key"></i></th>
                                    <th style="width:200px;"><i class="fas fa-envelope-open-text"></i></th>
                                </tr>
                                </thead>
                                <tbody v-for="(order) in DataOutput.Reports.data" :key="order.id">
                                <tr>
                                    <td style="width:200px;">{{order.name }}</td>
                                    <td style="width:200px;">{{order.phone_number }}</td>
                                    <td style="width:200px;">{{order.track_code }}</td>
                                    <td style="width:200px;">{{order.describtion }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <slideout-panel></slideout-panel>

                </div>
            </div>
        </div>
</template>

<script>
    var numberAbbreviate = require("number-abbreviate");
    import VueCircle from 'vue2-circle-progress'
    import {GChart} from 'vue-google-charts'
    import {Chart} from 'highcharts-vue'
    import VueSlideoutPanel from 'vue2-slideout-panel';
    Vue.use(VueSlideoutPanel);

    //TopDelivers
    import ShowAllTopDelivers from './Admin/ShowAllTopDelivers.vue'
    Vue.component("TopDelivers", {
        name: "TopDelivers",
        render: h => h(ShowAllTopDelivers)
    });

    //TopMembers
    import ShowAllTopMembers from './Admin/ShowAllTopMembers.vue'
    import Datepicker from "vuejs-datepicker";
    // import ViewStatistic from '../components/Dashboard/Member_Statistic.vue'

    Vue.component("TopMembers", {
        name: "TopMembers",
        render: h => h(ShowAllTopMembers)
    });

    export default {

        components: {
            VueCircle,
            Chart,
            Datepicker,
            GChart,
            // 'view-statistic': ViewStatistic,
        },
        data() {
            return {
                DataOutput: {},
                OrdersSubWeek: [],
                DeliveredItemsAmounts: [],
                DeliverFeeSubWeek: [],
                DeliversFeesSubWeek: [],
                hodhod : { gradient: ["black"] },
                carts : { gradient: ["rgb(213, 222, 73)"] },
                fillwating : { gradient: ["rgb(119, 118, 118)"] },
                fillorders : { gradient: ["rgb(200, 35, 211)"] },
                fillpending : { gradient: ["rgb(176,158,62)"] },
                filldelivered : { gradient: ["rgb(48,172,86)"] },
                fillissued : { gradient: ["rgb(168,59,59)"] },
                dateFrom: 'All',
                dateTo: 'All',
                isLoaded: false,
            };
        },
        created() {
            if (this.dateFrom)
            this.fetchHome(this.dateFrom,  this.dateTo);
        },

        methods:{
            fetchHome(dateFrom,dateTo){
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/home/${dateFrom}/${dateTo}`)
                    .then((response) => {

                        this.DataOutput = response.data;

                        this.OrdersSubWeek = {
                            chart: { type: 'spline' },
                            yAxis: { title: { text: '' } },
                            legend: { enabled: 0 },
                            title: { text: 'ORDERS (This Week)' },
                            series: [{
                                data: response.data.OrdersSubWeek,
                                color: '#5ea3d5'
                            }] };

                        this.DeliveredItemsAmounts = {
                            chart: { type: 'spline' },
                            yAxis: { title: { text: '' } },
                            legend: { enabled: 0 },
                            title: { text: 'SOLD PRODUCTS (This Week)' },
                            series: [{
                                data: response.data.DeliveredItemsAmounts,
                                color: 'rgb(100, 194, 106)'
                            }] };

                        this.DeliversFeesSubWeek = {
                            chart: { type: 'spline' },
                            yAxis: { title: { text: '' } },
                            legend: { enabled: 0 },
                            title: { text: 'DELIVERS FEES (This Week)' },
                            series: [{
                                data: response.data.DeliversFeesSubWeek,
                                color: 'rgb(189, 133, 218)'
                            }] };

                        // this.OrdersChartAllStatus = [

                        //     ['Orders', 'Day 1', 'Day 2', 'Day 3','Day 4','Day 5', 'Day 6', 'Day 7'],

                        //     response.data.WaitingOrdersNotAccepted,
                        //     response.data.WaitingOrdersAccepted,
                        //     response.data.PendingOrders,
                        //     response.data.DeliveredOrders,
                        //     response.data.IssuedOrders,
                        // ];

                        this.isLoaded = true;
                    });
            },

            onChangeDateFrom(event) {
                this.dateFrom = event.toISOString().substr(0,10);
                this.fetchHome(this.dateFrom,  this.dateTo);
                if(this.dateFrom !== 'All' && this.dateTo !== 'All'){
                    this.ShowOrderByDate = true;
                }
            },

            onChangeDateTo(event) {
                this.dateTo = event.toISOString().substr(0,10);
                this.fetchHome(this.dateFrom,  this.dateTo);
                if(this.dateFrom !== 'All' && this.dateTo !== 'All'){
                    this.ShowOrderByDate = true;
                }
            },

            showPanelTopDelivers() {
                const panel1Handle = this.$showPanel({
                    component: 'TopDelivers',
                    width:400,
                    openOn: 'right',
                });
            },

            showPanelTopMembers() {
                const panel1Handle = this.$showPanel({
                    component: 'TopMembers',
                    width:400,
                    openOn: 'right',
                });
            },

            progress(event,progress,stepValue){
                console.log(stepValue);
            },

            progress_end(event){
                console.log("Circle progress end");
            },

            DownloadWithdraw_and_unWithdrawen_balance(){ window.open(process.env.VUE_APP_URL+`/api/DownloadWithdrawOrders/Withdrawn_And_Unwithdrawn`, '_blank'); },

            DownloadUsers_Stores_balance(){ window.open(process.env.VUE_APP_URL+`/api/DownloadWithdrawOrders/CurrentBalances`, '_blank'); },

            DownloadWithdraw_balance(){ window.open(process.env.VUE_APP_URL+`/api/DownloadWithdrawOrders/Accepted`, '_blank'); },

            numberAbbreviate2(number){
                return numberAbbreviate(number);
            }, 
        }
    };
</script>

<style scoped>
.uk-card-header{
    border-bottom: none !important;
}
.uk-grid>*>:last-child{
    
}
</style>
