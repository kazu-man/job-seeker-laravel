<template>
    <div class="container">
        <div class="col-md-6 offset-md-3">
            <div class="card mt-4">
                <div class="card-header">
                    <p class="mb-0">パスワード再設定</p>
                </div>
                <div class="card-body">
                    <form @submit.prevent="ResetPassword">
                        <div class="form-group">
                            <label>Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="form-control"
                                placeholder="Email"
                            />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input
                                v-model="form.password"
                                type="password"
                                class="form-control"
                                placeholder="Password"
                            />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label>Password (confirm)</label>
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                class="form-control"
                                placeholder="Password"
                            />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <input
                                type="submit"
                                value="Register"
                                class="btn btn-success w-100"
                                style="margin:auto;margin-top:10px;"
                            />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                email: "", // リセット対象のメールアドレス
                password: "", // 新しいパスワード
                password_confirmation: "", // 新しいパスワード確認
                token: "" // パスワードリセット実行するための一時的なトークン
            }
        };
    },

    created() {
        this.setQuery();
        this.checkToken();
    },

    methods: {
        async ResetPassword() {
            // パスワードリセットリクエストを投げる関数
            await axios.post("/api/password/reset", this.form).then(res => {
                if (res.status == 200) {
                    this.$store.dispatch("common/alertModalUp", {
                        data: 200,
                        successMessage: "パスワードを設定しました"
                    });
                    this.$router.push(`${this.routePath}/top`).catch(() => {});
                }
            });
        },
        setQuery() {
            // getリクエストのパラメータを取得する関数
            this.form.token = this.$route.params.token || ""; // パスワードリセットするために必要なToken
        },
        async checkToken() {
            await axios
                .get("/api/checkUrlBeforeChangePassword/" + this.form.token)
                .catch(() => {
                    this.$router.push(`${this.routePath}/top`).catch(() => {});
                });
        }
    },
    computed: {
        routePath: function() {
            return this.$store.getters["auth/routePath"];
        }
    }
};
</script>

<style scoped>
.container {
    padding-top: 0px;
}
</style>
