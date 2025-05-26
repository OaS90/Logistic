<template>
    <div>
        <label for="file-upload" class="btn btn-primary" data-modal="modal-request1" style="margin-bottom: 0; margin-right: 5px" id="button">
            <span>Импорт c выводом изменений</span>
        </label>
        <input type="file" hidden id="file-upload" @change="beforeImport" ref="fileUpload">
        <button class="btn btn-primary"  @click="exportSettings">Экспорт</button>
        <a target="_blank" href="https://yandex.ru/map-constructor/" class="btn btn-success ml-1">Конструктор карт</a>
        <hr>

        <div v-if="changedZones">
            <h2>Изменения в зонах</h2>

            <button class="btn btn-primary mb-2" @click="importSettings">Отправить в сервис</button>
            <p>Изменено: {{ changedZones.length }}</p>
            <table class="table table-bordered" style="width: 70%">
                <thead>
                <tr>
                    <th class="choice"><input type="checkbox" class="input-lg checkbox" @change="selectAllZones($event)"></th>
                    <th>Id Региона</th>
                    <th>Тип полигона</th>
                    <th>Зона</th>
                    <th>Регион</th>
                    <th>Код филала</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(zone, index) in changedZones">
                    <td class="choice">
                        <input type="checkbox" class="input-lg checkbox" v-model="zone.to_import">
                    </td>
                    <td>{{ zone.region_id }}</td>
                    <td>{{ zone.type }}</td>
                    <td>{{ zone.zone_name }}</td>
                    <td>{{ zone.region_name }}</td>
                    <td>{{ zone.filial_code }}</td>
                </tr>
                </tbody>

            </table>
        </div>

        <modal v-if="showModal" @close="showModal = false">
            <template #body>
                <template v-if="loading">
                    <PulseLoader :loading="loading" :color="'#7c69ef'"/>
                </template>
                <template v-else>
                    {{ errorText }}
                    <br>
                    <button class="btn btn-secondary" @click="showModal = false">ОК</button>
                </template>
            </template>
        </modal>
    </div>
</template>

<script>
import Modal from "./Modal.vue";
import {DotLoader, PulseLoader} from "vue3-spinner";
export default {
    data() {
        return {
            changedZones: null,
            showModal: false,
            loading: false,
            errorText: ''
        }
    },
    components: {
        PulseLoader,
        Modal,
        DotLoader
    },
    methods: {
        selectAllZones(event) {
            this.changedZones.forEach(zone => {
                zone.to_import = !!event.target.checked
            })
        },
        beforeImport(event) {
            let formData = new FormData()
            formData.append('document', event.target.files[0])

            axios.post('/admin/yandex-zones/prepare-import', formData)
                .then(response => {
                    this.changedZones = response.data.changes
                }).catch(errors => {
                    this.loading = false
                    this.errorText = 'Ошибка обработки данных.'
                })

            this.$refs.fileUpload.value = null;
        },
        exportSettings() {
            this.loading = true;
            this.showModal = true

            axios.get('/admin/yandex-zones/export', {responseType: 'blob'})
                .then(response => {
                    this.loading = false
                    this.showModal = false
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'data.geojson');
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(error => {
                    this.loading = false
                    this.errorText = 'Ошибка экспорта из сервиса.'
                })
        },

        importSettings() {
            let zonesToImport = this.changedZones.filter(point => {
                return point.to_import === true
            })

            this.showModal = true
            this.loading = true

            axios.post('/admin/yandex-zones/import-to-service', {zonesToImport: zonesToImport, zones: this.changedZones})
                .then(response => {
                    this.showModal = false;
                    this.loading = false
                    this.changedZones = null
                }).catch(errors => {
                    this.loading = false
                    this.errorText = 'Ошибка импорта в сервис.'
                })
        }
    }
}
</script>

<style scoped>
.choice {
    text-align: center;
    width: 5%;
}
</style>
