<script>
export default {
    methods: {
        readFileAsDataURL: async function(file) {
            let result_base64 = await new Promise(resolve => {
                let fileReader = new FileReader();
                fileReader.onload = e => resolve(fileReader.result);
                fileReader.readAsDataURL(file);
            });

            return result_base64;
        },
        async download(data) {
            const res = await axios.post("/api/resumeDownload", data, {
                responseType: "arraybuffer"
            });
            try {
                if (res.status === 200) {
                    let blob = new Blob([res.data], {
                        type: "application/pdf"
                    });
                    let link = document.createElement("a");
                    link.href = window.URL.createObjectURL(blob);
                    link.download = data.resumeFile;
                    link.click();
                }
            } catch (e) {
                this.$store.dispatch("common/alertModalUp", {
                    data: OK,
                    successMessage: "エラーが発生しました"
                });
            }
        }
    }
};
</script>
