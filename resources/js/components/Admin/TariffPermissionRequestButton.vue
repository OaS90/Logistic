<template>
    <span>
        <a href="#" @click="sendRequest" class="btn btn-sm btn-link"><i class="la la-mail-reply"></i> Запрос доступов</a>

        <modal v-if="showModal" @close="showModal = false">
            <span slot="body">
                Запрос отправлен
                <br>
                <button class="btn btn-success" @click="showModal = !showModal">OK</button>
            </span>
            <span slot="footer"></span>
        </modal>
    </span>
</template>

<script>

import Modal from './Modal.vue'

export default {
    name: "TariffPermissionRequestButton",
    props: ['tariffId'],
    data() {
        return {
            requestUri: 'tariffs/' + this.tariffId + '/request',
            showModal: false
        }
    },
    components: {
        Modal
    },
    methods: {
        sendRequest() {
            axios.post('tariffs/' + this.tariffId + '/request')
                .then((response) => {
                if (response.status === 200) {
                    this.showModal = true
                }

                if (response.status === 500) {
                    this.showModal = true
                    this.modalText = response.data.message
                }
            }).catch((error) => {
                if (error.response.status === 500) {
                    this.showModal = true
                    this.modalText = error.response.data.message
                }
            })
        }
    }
}
</script>

<style scoped>

</style>
