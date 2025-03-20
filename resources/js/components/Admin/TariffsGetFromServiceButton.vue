<template>
    <div>
        <div class="col-sm-12">
            <div class="d-print-none with-border">
                <a class="btn btn-secondary" @click="getTariffs">
                    <span class="ladda-label"><i class="la la-plus"></i> Получить тарифы из сервиса</span>
                </a>
            </div>
        </div>
        <modal v-if="showModal" @close="showModal = false">
            <template #body>
                <template v-if="loading">
                    {{ modalText }}
                    <PulseLoader :loading="loading" :color="'#7c69ef'"/>
                </template>
                <template v-else>
                    {{ modalText }}
                    <br>
                    <button class="btn btn-secondary" @click="showModal = false">ОК</button>
                </template>
            </template>
        </modal>
    </div>
</template>

<script>
import Modal from "./Modal.vue";
import { PulseLoader } from "vue3-spinner"

export default {
    name: "TariffsGetFromServiceButton",
    data() {
        return {
            showModal: false,
            loading: true,
            modalText: 'Получение тарифов...',
            size: "15px",
            color: '#7c69ef',
            btnText: 'OK',
            btnClass: 'btn btn-secondary'
        }
    },
    methods: {
        getTariffs() {
            this.showModal = true

            axios.post('tariffs-holodilnik/get', {})
                .then((response) => {
                    this.loading = false
                    if (response.status === 200) {
                        this.showModal = true
                        this.modalText = response.data.message
                    }
                }).catch((error) => {
                if (error.response.status === 500) {
                    this.showModal = true
                    this.loading = false
                    this.modalText = 'Ошибка загрузки файла.' + error.response.data.message
                }
            })
        }
    },
    components: {
        Modal,
        PulseLoader
    },
}
</script>

<style scoped>

</style>
