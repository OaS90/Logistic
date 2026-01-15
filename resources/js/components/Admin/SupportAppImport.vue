<template>
    <div class="csv-wrap">
        <h1>Загрузка заявки</h1>
        <div style="width: 400px">
            <label for="file-upload" class="btn request-btn" data-modal="modal-request1" style="max-width: 400px" id="button">
                <span>Создать заявку(ки) из файла csv</span>
            </label>
            <input type="file" hidden id="file-upload" @change="previewFiles" ref="fileUpload">
        </div>
        <hr>
        <div class="form-group">
            <label for="select" id="partner">
                <span>Выберете партнёра</span>
            </label>
            <select v-model="selectedPartner" @change="changePartner(selectedPartner)" class="form-control col-md-4">
                <option v-for="partner in partners" :value="partner">
                    {{ partner.company }} (Id партнера {{ partner.id }})
                </option>
            </select>
        </div>
        <div v-if="selectedPartner">
            <div class="form-group" v-if="selectedPartner.warehouses.length > 1">
                <label for="select" id="warehouses">
                    <span>Выбрать склад</span>
                </label>
                <select v-model="partnerWarehouse" class="form-control col-md-4">
                    <option v-for="warehouse in selectedPartner.warehouses" v-bind:value="warehouse">
                        {{ warehouse.address }}
                    </option>
                </select>
            </div>
            <div v-else>
                Адрес склада: {{ selectedPartner.warehouses[0].address }}
                <input type="hidden" v-model="selectedPartner.warehouses[0]">
            </div>
<!--            <span>Выбрано: {{ partnerWarehouse ? partnerWarehouse.id : '-' }}</span>-->
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
import axios from 'axios';

export default {
    name: "SupportAppImport",
    components: {
        Modal,
        PulseLoader
    },
    props: ['partners'],
    data() {
        return {
            selectedPartner: null,
            partnerWarehouse: null,
            showModal: false,
            modalText: '',
            loading: true,
            size: "15px",
            color: '#7c69ef'
        }
    },
    methods: {
        changePartner(partner) {
            if (partner.warehouses.length === 1) {
                this.partnerWarehouse = partner.warehouses[0]
            } else {
                this.partnerWarehouse = null
            }
        },
        previewFiles(event) {
            if (!this.selectedPartner) {
                this.showModal = true
                this.modalText = 'Не выбран партнёр';
                this.loading = false

                return false
            } else if (!this.partnerWarehouse) {
                this.showModal = true
                this.loading = false

                this.modalText = 'Не выбран склад';

                return false
            }

            let formData = new FormData()
            formData.append('document', event.target.files[0])
            formData.append('user_id', this.selectedPartner.id)
            formData.append('store_id', this.partnerWarehouse.store_id)
            this.showModal = true

            axios.post('/admin/support/import-app', formData, {
                headers: {
                    Accept: 'application/json'
                }
            }).then((response) => {
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

            this.loading = true
            this.$refs.fileUpload.value=null;
        }
    }
}
</script>

<style scoped>
#button {
    background-color: lightskyblue;
}

#button:hover {
    cursor: pointer;
}
</style>
