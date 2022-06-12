<template>
    <div class="company-register-area">
        <div class="form-title">Add New Admin</div>
        <div class="form-group">
            <label>Admin Name</label>
            <input
                type="text"
                name="name"
                v-model="name"
                class="form-control"
            />
            <label>Admin Email</label>
            <input
                type="text"
                name="email"
                v-model="email"
                class="form-control"
            />
            <label>Admin Password</label>
            <input
                type="text"
                name="password"
                v-model="password"
                class="form-control"
            />
            <div>
                <button class=" register-btn btn" v-on:click="postCompanyData">
                    登録
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            name: "",
            email: "",
            password: ""
        };
    },

    methods: {
        postCompanyData() {
            var data = {
                name: this.name,
                email: this.email,
                password: this.password
            };

            axios.post("/api/addAdmin", data).then(res => {
                // テストのため返り値をコンソールに表示
                this.$emit("com_update", res.data);
            });
        }
    }
};
</script>

<style scoped>
.register-btn {
    width: 100px;
    padding: 10px 5px;
    margin-left: 20px;
}
.company-register-area {
    border: solid 2px lightgray;
    border-radius: 10px;
    padding: 20px;
    width: 100%;
}

.form-title {
    font-size: 24px;
}

.form-group {
    margin: 10px auto;
    text-align: center;
    width: 50%;
}
</style>
