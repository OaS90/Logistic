<template>
    <div class="csv-wrap">
        <h1>цуацу</h1>
        <div style="width: 400px">
            <label for="file-upload" class="btn request-btn" data-modal="modal-request1" style="max-width: 400px" id="button">
                <span>Создать заявку(ки) из файла csv</span>
            </label>
            <input type="file" hidden id="file-upload" @change="previewFiles" ref="fileUpload">
        </div>
        <div>
            <label for="select" id="partner">
                <span>Выберете партнёра</span>
            </label>
            <select v-model="selectedPartner">
                <option v-for="partner in partners" v-bind:value="partner">
                    {{ partner.company }} (Id партнера {{ partner.id }})
                </option>
            </select>
            <span>Выбрано: {{ selectedPartner ? selectedPartner.company : '-' }}</span>
        </div>
        <div v-if="selectedPartner">
            <div v-if="selectedPartner.warehouses.length > 1">
                <label for="select" id="warehouses">
                    <span>Выбрать склад</span>
                </label>
                <select v-model="partnerWarehouse">
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
            <span slot="body">{{ modalText }}</span>
            <span slot="footer"></span>
        </modal>
    </div>
</template>

<script>
import Modal from "./Modal";

export default {
    name: "SupportAppImport",
    components: {
        Modal
    },
    data() {
        return {
            selectedPartner: null,
            partnerWarehouse: null,
            partners: {},
            showModal: false,
            modalText: '',
        }
    },
    mounted() {
        axios.get('/admin/support/partners').then(response => {
            this.partners = response.data;
        })
    },
    methods: {
        previewFiles(event) {
            let formData = new FormData()
            formData.append('document', event.target.files[0])
            formData.append('user_id', this.selectedPartner.id)
            formData.append('store_id', this.partnerWarehouse.store_id)

            axios.post('/admin/support/import-app', formData, {
                headers: {
                    accept: 'application/json', 'Content-Type': 'application/json'
                }
            }).then(() => {
                if (error.response.status === 200) {
                    this.showModal = true
                    this.modalText = 'Файл успешно загружен'
                }
            }).catch((error) => {
                this.showModal = true
                this.modalText = 'Ошибка загрузки файла.' + error.response.data.message
            })

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