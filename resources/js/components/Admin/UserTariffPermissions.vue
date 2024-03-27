<template>
    <div class="row">
        <div class="col-md-12">
            <h3>Тарифы доступные для редактирования</h3>
            <multiselect :options="tariffs"
                         placeholder="Тарифы"
                         v-model="selectedTariffs"
                         :multiple="true"
                         track-by="id"
                         label="name"
                         @select="setTariffEditable"
                         @remove="removeEditableForTariff"
            >
            </multiselect>
        </div>
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import Modal from "./Modal";

export default {
    name: "UserTariffPermissions",
    data() {
        return {
            selectedTariffs: [],
            showTariffs: false,
            allTariffs: null,
            showModal: false,
            modalText: ''
        }
    },
    props: ['tariffs', 'allowedTariffs', 'userId'],
    components: {
        Multiselect,
        Modal
    },
    mounted() {
        this.allTariffs = this.allowedTariffs
        this.allTariffs.forEach((tariff) => {
            this.selectedTariffs.push(tariff)
        })
    },
    methods: {
        setTariffEditable(tariff) {
            axios.post('/admin/admin-user/tariff/permission-edit/add', {
                userId: this.userId,
                tariffId: tariff.id
            }).then((response) => {
                console.log(response.data)
            }).catch((error) => {
                console.log(error.response.data)
            })
        },
        removeEditableForTariff(tariff) {
            axios.post('/admin/admin-user/tariff/permission-edit/remove', {
                userId: this.userId,
                tariffId: tariff.id
            }).then((response) => {
                console.log(response.data)
            }).catch((error) => {
                console.log(error.response.data)
            })
        }
    }
}
</script>

<style scoped>

</style>