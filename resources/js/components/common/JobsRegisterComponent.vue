<template>
    <div class="job-content">
        <div class="form-title">New Job Offer</div>

        <div class="job-register-area">
            <div class="form-group title-form">
                <label>
                    Job Title:
                </label>
                <input
                    type="text"
                    name="title"
                    v-model="formData.job_title"
                    class="form-control"
                />
            </div>
            <div class="form-group salary-form">
                <label>
                    Anual Salary:
                </label>
                <input
                    type="text"
                    name="salary"
                    v-model="formData.annual_salary"
                    class="form-control"
                />
            </div>

            <div class="form-group place-form">
                <place-show-component
                    @select-city="selectCity"
                    @loading-map="loadingOrNot"
                    ref="placeShowComponent"
                ></place-show-component>
            </div>

            <div class="form-group type-form">
                <label>
                    Job type
                </label>
                <select-box-component
                    :baseData="jobTypes"
                    @changeSelectedVal="selectType"
                ></select-box-component>
            </div>

            <div class="form-group type-form">
                <label>
                    Tags
                </label>
                <select-tag-component
                    ref="selectTagComponent"
                ></select-tag-component>
            </div>

            <div class="form-group category-form">
                <label>
                    Category
                </label>
                <select-box-component
                    :baseData="categories"
                    @changeSelectedVal="selectCategory"
                ></select-box-component>
            </div>

            <div class="form-group description-form">
                <label>
                    Description:
                </label>
                <textarea
                    name="description"
                    v-model="formData.description"
                    class="form-control"
                    rows="8"
                ></textarea>
            </div>
            <div class="form-group requirement-form">
                <label>
                    Requirement:
                </label>
                <textarea
                    name="requirement"
                    v-model="formData.requirement"
                    class="form-control"
                    rows="8"
                ></textarea>
            </div>
            <div class="form-group benefit-form">
                <label>
                    Benefit:
                </label>
                <textarea
                    name="benefit"
                    v-model="formData.benefit"
                    class="form-control"
                    rows="8"
                ></textarea>
            </div>
            <div class="form-group experience-fom">
                <label>
                    Experience:
                </label>
                <textarea
                    name="experience"
                    v-model="formData.experience"
                    class="form-control"
                    rows="8"
                ></textarea>
            </div>

            <label>
                Video:
            </label>
            <video-upload-component
                ref="videoUploadComponent"
            ></video-upload-component>

            <div class="form-group submit-form">
                <button
                    v-on:click="postData"
                    class="btn submit-btn"
                    v-bind:disabled="loading"
                >
                    Submit
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            formData: {
                job_title: "",
                annual_salary: "",
                type: "",
                category: "",
                city: "",
                description: "",
                requirement: "",
                benefit: "",
                experience: "",
                company: this.$route.params.id
            },
            loading: false
        };
    },

    methods: {
        postData() {
            const data = JSON.parse(JSON.stringify(this.formData));
            (data.tag = this.$refs.selectTagComponent.selectedTagList),
                (data.addressObj = this.$refs.placeShowComponent.addressObj),
                (data.latLng = this.$refs.placeShowComponent.lastMarkerPosition),
                (data.mapFlg = this.$refs.placeShowComponent.mapShow),
                (data.videoFile = this.$refs.videoUploadComponent.uploadVideoUrl);

            this.$store.commit("common/setLoadingFlg", true);
            axios.post("/api/registerJob", data).then(res => {
                this.$store.commit("common/setLoadingFlg", false);

                this.$store.dispatch("common/alertModalUp", {
                    data: res.status,
                    successMessage: "登録しました。"
                });
            });
        },
        selectType: function(val) {
            this.formData.type = val;
        },
        selectCategory: function(val) {
            this.formData.category = val;
        },
        selectCity: function(val) {
            this.formData.city = val;
        },
        loadingOrNot: function(flg) {
            this.loading = flg;
        }
    },
    props: ["categories", "jobTypes"]
};
</script>

<style scoped>
.form-group {
    margin: 10px auto;
    text-align: left;
}
.title-form,
.salary-form,
.address-form {
    display: inline-block;
    width: 49%;
    padding-left: 2%;
    padding-right: 2%;
}
.container {
    z-index: 100;
    height: 100%;
}

.job-content {
    width: 100%;
    margin: auto;
}
.form-control {
    width: 15%;
    display: inline-block;
    width: 100%;
}
label {
    text-align: left;
    padding-top: 15px;
}
.submit-form {
    width: 100px;
    text-align: center;
}
.submit-btn {
    padding: 20px 20px;
    border-radius: 10px;
}
.job-register-area {
    border: solid 2px lightgray;
    border-radius: 10px;
    padding: 20px;
    width: 100%;
    color: white;
    height: 100%;

    background: #d3d3d3b0;
    opacity: 1;
}

.form-title {
    font-size: 24px;
    color: white;
    font-weight: bold;
}
</style>
