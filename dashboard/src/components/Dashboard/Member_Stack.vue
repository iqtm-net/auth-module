<template>

    <div class="uk-modal-dialog uk-modal-body" style="width:auto;">
        <nav style="margin: 20px 0px; background:none;" class="uk-navbar-container" uk-navbar>
            <div class="uk-navbar-right">
                <ul class="uk-navbar-nav">
                    <li>
                        <button :disabled="miniload || member_stacks.data == 0" class="btn btn-outline-success" type="button" @click.prevent="PoP()">
                            POP
                        </button> 
                    </li>
                    <li>
                        <button :disabled="miniload || member_stacks.data == 0" class="btn btn-outline-success" type="button" @click.prevent="DownloadExcel()">
                            Download Excel
                        </button> 
                    </li>
                </ul>
            </div>
        </nav>

        <table border="0" class="table cust-table uk-card uk-card-default">
            <thead>
            <tr>
                <th style="width:80px;">#</th>
                <th style="width:200px;">Product</th>
                <th style="width:200px;">Price</th>
                <th style="width:200px;">Deliver Fee</th>
                <th style="width:200px;">Tracking Code</th>
            </tr>
            </thead>
            <tbody v-for="(member_stack,index) in member_stacks.data" :key="member_stack.id">
            <tr>
                <td style="width:80px;">{{ index+1 }}</td>
                <td style="width:200px;">{{ member_stack.product_name }}</td>
                <td style="width:200px;">{{ member_stack.recieved_price }}</td>
                <td style="width:200px;">{{ member_stack.Deliver_Fee }}</td>
                <td style="width:200px;">{{ member_stack.track_code }}</td>
            </tr>
            </tbody>
        </table>
        <br> 
        <empty-result :Data="member_stacks.data"></empty-result>
    </div>
 
</template>

<script>
    export default {

        props: ['AccountId', 'Account_Role_Number'], 

        data() {
            return {
                miniload: false,
                isLoaded2: false,
                member_stacks: {},
            };
        },

        watch: {
            AccountId: function() {
                this.member_stack(); 
            } 
        },

        methods: { 
                member_stack(){
                    this.isLoaded2 = false;
                    this.axios.get(process.env.VUE_APP_URL+`/api/MemberOrders/member_stack/${this.Account_Role_Number}/${this.AccountId}`)
                        .then(res => {
                            if (res.status == 200) {
                                this.member_stacks = res.data;
                                this.isLoaded2 = true;
                            } 
                        }).catch(res => {
                            this.$toasted.show(res, { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    });
                },

                async PoP(){
                    this.miniload = true;

                    let formData = new FormData();
                    formData.append('member_id', this.AccountId);
                    formData.append('account_type', this.Account_Role_Number);

                    await this.DownloadExcel();

                    this.axios.post(process.env.VUE_APP_URL+`/api/MemberOrders/Member_Pop_stack`, formData)
                        .then(res => {
                            if (res.status == 200) {
                                this.miniload = false; 
                                this.member_stack();
                                this.$toasted.show('Popped !', { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                            
                            } 
                        }).catch(res => {
                            this.miniload = false;
                            this.$toasted.show(res, { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    });
                },

                async DownloadExcel(){
                    this.miniload = true;

                    let formData = new FormData();
                    formData.append('member_id', this.AccountId);
                    formData.append('account_type', this.Account_Role_Number);

                    // Download STACK
                    this.axios.post(process.env.VUE_APP_URL+`/api/MemberOrders/Download_Member_Stack`, formData, { responseType: 'blob' })
                    .then((response) => {
                        var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                        var fileLink = document.createElement('a');

                        fileLink.href = fileURL;
                        fileLink.setAttribute('download', Date()+'.xlsx');
                        document.body.appendChild(fileLink);
                        fileLink.click();
                        this.miniload = false; 
                    });
                }
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
</style>
